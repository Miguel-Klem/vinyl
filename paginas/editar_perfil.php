<?php
// =====================================================
// EDITAR PERFIL
// Troca a foto de perfil e o nome de exibição.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

$usuario = buscar_um($conexao,
    'SELECT nome_exibicao, foto_perfil FROM usuarios WHERE id = ?',
    'i', array($meu_id));

$erro = '';
$nome = $usuario ? $usuario['nome_exibicao'] : '';

if ($usuario !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome         = trim($_POST['nome_exibicao'] ?? '');
    $remover_foto = isset($_POST['remover_foto']);

    if ($nome === '') {
        $erro = 'O nome de exibição não pode ficar vazio.';

    } elseif (mb_strlen($nome) > 60) {
        $erro = 'O nome de exibição pode ter no máximo 60 letras.';

    } else {

        // Tenta salvar a foto nova (se a pessoa escolheu uma)
        $erro_imagem = '';
        $foto_nova   = salvar_imagem_enviada('foto', 'perfis', 'usuario' . $meu_id, $erro_imagem);

        if ($erro_imagem !== '') {
            $erro = $erro_imagem;
        } else {

            // Decide qual foto fica no banco
            $foto_final = $usuario['foto_perfil'];          // por padrão, continua a mesma

            if ($foto_nova !== '') {                        // escolheu foto nova
                apagar_imagem('perfis', $usuario['foto_perfil']);
                $foto_final = $foto_nova;

            } elseif ($remover_foto) {                      // pediu para voltar ao vinil
                apagar_imagem('perfis', $usuario['foto_perfil']);
                $foto_final = null;
            }

            executar($conexao,
                'UPDATE usuarios SET nome_exibicao = ?, foto_perfil = ? WHERE id = ?',
                'ssi', array($nome, $foto_final, $meu_id));

            $_SESSION['usuario_nome'] = $nome;

            header('Location: perfil.php?salvo=1');
            exit;
        }
    }
}

$titulo_pagina = 'Editar perfil';
$menu_ativo    = '';
require '../includes/cabecalho.php';
?>

<h1 class="titulo-pagina">Editar perfil</h1>

<div class="caixa-formulario">

    <?php if ($erro !== '') { ?>
        <p class="mensagem-erro"><?php echo limpar($erro); ?></p>
    <?php } ?>

    <img class="foto-perfil-media" src="<?php echo limpar(url_foto_perfil($usuario ? $usuario['foto_perfil'] : '')); ?>" alt="Foto atual">

    <form method="post" action="editar_perfil.php" enctype="multipart/form-data">
        <label for="nome_exibicao">Nome de exibição</label>
        <input type="text" id="nome_exibicao" name="nome_exibicao" maxlength="60" required value="<?php echo limpar($nome); ?>">

        <label for="foto">Nova foto de perfil (até 3 MB)</label>
        <input type="file" id="foto" name="foto" accept="image/*">

        <div class="linha-termos">
            <input type="checkbox" id="remover_foto" name="remover_foto">
            <label for="remover_foto">Remover minha foto e voltar para o vinil</label>
        </div>

        <button type="submit" class="botao botao-largo">Salvar</button>
    </form>

    <a class="link-voltar" href="perfil.php">Cancelar</a>
</div>

<?php require '../includes/rodape.php'; ?>
