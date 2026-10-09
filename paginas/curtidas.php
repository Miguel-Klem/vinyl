<?php
// =====================================================
// MÚSICAS CURTIDAS (a playlist padrão de todo usuário)
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

// ATENÇÃO: esta ordem tem que ser igual à de ids_da_lista() em funcoes.php
$musicas = buscar_todos($conexao,
    SQL_MUSICAS . '
     JOIN curtidas c ON c.musica_id = m.id
     WHERE c.usuario_id = ?
     ORDER BY c.data_curtida DESC, m.id DESC',
    'i', array($meu_id));

$titulo_pagina = 'Músicas Curtidas';
$menu_ativo    = 'curtidas';
require '../includes/cabecalho.php';
?>

<div class="cabecalho-pagina">
    <div class="cabecalho-capa-roxa"><img src="../imagens/logo.svg" alt=""></div>
    <div>
        <span class="cabecalho-tipo">Playlist</span>
        <h1>Músicas Curtidas</h1>
        <p class="cabecalho-info"><?php echo limpar($_SESSION['usuario_nome']); ?> · <?php echo texto_musicas(count($musicas)); ?></p>
    </div>
</div>

<?php if (count($musicas) > 0) { ?>
    <div class="barra-acoes">
        <a class="botao" target="player" href="<?php echo limpar(link_tocar($musicas[0]['id'], 'curtidas')); ?>">Tocar tudo</a>
    </div>

    <div class="lista-musicas">
        <?php
        $numero = 1;
        foreach ($musicas as $musica) {
            mostrar_linha_musica($musica, $numero, 'curtidas');
            $numero++;
        }
        ?>
    </div>
<?php } else { ?>
    <p class="estado-vazio">
        Você ainda não curtiu nenhuma música. Toque uma música e clique no vinil ao lado do nome dela, na barra de baixo.
    </p>
<?php } ?>

<?php require '../includes/rodape.php'; ?>
