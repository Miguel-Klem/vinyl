<?php
// =====================================================
// SUA BIBLIOTECA
// Mostra as Músicas Curtidas, as playlists da pessoa e quem ela segue.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

$total_curtidas = buscar_um($conexao,
    'SELECT COUNT(*) AS total FROM curtidas WHERE usuario_id = ?',
    'i', array($meu_id));

// Playlists com a quantidade de músicas de cada uma
$playlists = buscar_todos($conexao,
    'SELECT p.id, p.nome, p.imagem,
            (SELECT COUNT(*) FROM playlist_musicas pm WHERE pm.playlist_id = p.id) AS total
     FROM playlists p
     WHERE p.usuario_id = ?
     ORDER BY p.id DESC',
    'i', array($meu_id));

// Pessoas que esta pessoa segue
$seguindo = buscar_todos($conexao,
    'SELECT u.id, u.nome_exibicao, u.foto_perfil
     FROM seguidores s
     JOIN usuarios u ON u.id = s.seguido_id
     WHERE s.seguidor_id = ?
     ORDER BY u.nome_exibicao',
    'i', array($meu_id));

$titulo_pagina = 'Sua Biblioteca';
$menu_ativo    = 'biblioteca';
require '../includes/cabecalho.php';
?>

<h1 class="titulo-pagina">Sua Biblioteca</h1>

<section class="secao">
    <h2>Playlists</h2>
    <div class="fileira">

        <!-- Playlist padrão: músicas curtidas -->
        <div class="cartao-musica">
            <a href="curtidas.php">
                <div class="tile-roxo"><img src="../imagens/logo.svg" alt=""></div>
                <strong>Músicas Curtidas</strong>
                <small><?php echo texto_musicas($total_curtidas['total']); ?></small>
            </a>
        </div>

        <!-- Playlists criadas pela pessoa -->
        <?php foreach ($playlists as $playlist) { mostrar_cartao_playlist($playlist); } ?>

        <!-- Atalho para criar uma nova -->
        <div class="cartao-musica">
            <a href="criar_playlist.php">
                <div class="tile-roxo tile-novo"><span>+</span></div>
                <strong>Criar playlist</strong>
                <small>Escolha nome e imagem</small>
            </a>
        </div>
    </div>
</section>

<section class="secao">
    <h2>Seguindo</h2>
    <?php if (count($seguindo) == 0) { ?>
        <p class="estado-vazio">Você ainda não segue ninguém. Use a Busca para encontrar pessoas.</p>
    <?php } else { ?>
        <div class="fileira">
            <?php foreach ($seguindo as $pessoa) { mostrar_cartao_pessoa($pessoa); } ?>
        </div>
    <?php } ?>
</section>

<?php require '../includes/rodape.php'; ?>
