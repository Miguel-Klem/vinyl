<?php
// =====================================================
// CRIAR PLAYLIST
// A pessoa escolhe o nome e (se quiser) uma imagem.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

$erro = '';
$nome = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');

    if ($nome === '') {
        $erro = 'Digite um nome para a playlist.';

    } elseif (mb_strlen($nome) > 100) {
        $erro = 'O nome pode ter no máximo 100 letras.';

    } else {

        // Tenta salvar a imagem (é opcional). Se der erro, $erro_imagem recebe o motivo.
        $erro_imagem = '';
        $nome_imagem = salvar_imagem_enviada('imagem', 'capas_playlists', 'playlist', $erro_imagem);

        if ($erro_imagem !== '') {
            $erro = $erro_imagem;
        } else {
            // Sem imagem escolhida: guarda NULL no banco (aparece o vinil)
            $imagem_para_banco = ($nome_imagem !== '') ? $nome_imagem : null;

            $novo_id = inserir($conexao,
                'INSERT INTO playlists (usuario_id, nome, imagem) VALUES (?, ?, ?)',
                'iss', array($meu_id, $nome, $imagem_para_banco));

            header('Location: playlist.php?id=' . $novo_id);
            exit;
        }
    }
}

$titulo_pagina = 'Criar playlist';
$menu_ativo    = '';
require '../includes/cabecalho.php';
?>

<h1 class="titulo-pagina">Criar playlist</h1>

<div class="caixa-formulario">

    <?php if ($erro !== '') { ?>
        <p class="mensagem-erro"><?php echo limpar($erro); ?></p>
    <?php } ?>

    <!-- enctype multipart é obrigatório para enviar arquivos -->
    <form method="post" action="criar_playlist.php" enctype="multipart/form-data">
        <label for="nome">Nome da playlist</label>
        <input type="text" id="nome" name="nome" maxlength="100" required value="<?php echo limpar($nome); ?>">

        <label for="imagem">Imagem (opcional, até 3 MB)</label>
        <input type="file" id="imagem" name="imagem" accept="image/*">

        <button type="submit" class="botao botao-largo">Criar playlist</button>
    </form>

    <a class="link-voltar" href="biblioteca.php">Cancelar</a>
</div>

<?php require '../includes/rodape.php'; ?>
