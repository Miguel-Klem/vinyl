<?php
// =====================================================
// ADICIONAR UMA MÚSICA A UMA PLAYLIST
// Chega aqui pelo link "+ Playlist" das músicas e do player.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

$musica_id = (int) ($_GET['musica'] ?? $_POST['musica'] ?? 0);

$musica = buscar_um($conexao, SQL_MUSICAS . ' WHERE m.id = ?', 'i', array($musica_id));

$mensagem = '';

// ----- Clicou em "Adicionar" em alguma playlist -----
if ($musica !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $playlist_id = (int) ($_POST['playlist_id'] ?? 0);

    // Só deixa adicionar em playlist que é da própria pessoa
    $playlist = buscar_um($conexao,
        'SELECT id, nome FROM playlists WHERE id = ? AND usuario_id = ?',
        'ii', array($playlist_id, $meu_id));

    if ($playlist !== null) {
        // INSERT IGNORE: se a música já estiver lá, o banco simplesmente ignora
        $adicionadas = executar($conexao,
            'INSERT IGNORE INTO playlist_musicas (playlist_id, musica_id) VALUES (?, ?)',
            'ii', array($playlist_id, $musica_id));

        if ($adicionadas > 0) {
            $mensagem = 'Música adicionada à playlist "' . $playlist['nome'] . '"!';
        } else {
            $mensagem = 'Essa música já está na playlist "' . $playlist['nome'] . '".';
        }
    }
}

// Playlists da pessoa, marcando em quais a música já está
$playlists = array();
if ($musica !== null) {
    $playlists = buscar_todos($conexao,
        'SELECT p.id, p.nome,
                (SELECT COUNT(*) FROM playlist_musicas pm
                 WHERE pm.playlist_id = p.id AND pm.musica_id = ?) AS ja_tem
         FROM playlists p
         WHERE p.usuario_id = ?
         ORDER BY p.nome',
        'ii', array($musica_id, $meu_id));
}

$titulo_pagina = 'Adicionar à playlist';
$menu_ativo    = '';
require '../includes/cabecalho.php';

if ($musica === null) {
    echo '<h1 class="titulo-pagina">Música não encontrada</h1>';
    require '../includes/rodape.php';
    exit;
}
?>

<h1 class="titulo-pagina">Adicionar à playlist</h1>

<div class="cabecalho-pagina cabecalho-pequeno">
    <img class="cabecalho-capa" src="<?php echo limpar(url_capa($musica['capa'])); ?>" alt="">
    <div>
        <h2><?php echo limpar($musica['titulo']); ?></h2>
        <p class="cabecalho-info"><?php echo limpar($musica['artista']); ?> · <?php echo limpar($musica['album']); ?></p>
    </div>
</div>

<?php if ($mensagem !== '') { ?>
    <p class="mensagem-sucesso"><?php echo limpar($mensagem); ?></p>
<?php } ?>

<?php if (count($playlists) == 0) { ?>
    <p class="estado-vazio">
        Você ainda não tem playlists.
        <a class="link-destaque" href="criar_playlist.php">Criar minha primeira playlist</a>
    </p>
<?php } else { ?>
    <div class="lista-musicas">
        <?php foreach ($playlists as $item) { ?>
            <div class="linha-playlist">
                <strong><?php echo limpar($item['nome']); ?></strong>
                <?php if ($item['ja_tem'] > 0) { ?>
                    <span class="texto-suave">Já está nesta playlist</span>
                <?php } else { ?>
                    <form method="post" action="adicionar_playlist.php">
                        <input type="hidden" name="musica" value="<?php echo $musica_id; ?>">
                        <input type="hidden" name="playlist_id" value="<?php echo (int) $item['id']; ?>">
                        <button type="submit" class="botao botao-pequeno">Adicionar</button>
                    </form>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <p class="estado-vazio"><a class="link-destaque" href="criar_playlist.php">+ Criar nova playlist</a></p>
<?php } ?>

<?php require '../includes/rodape.php'; ?>
