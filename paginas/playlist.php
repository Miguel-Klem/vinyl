<?php
// =====================================================
// PLAYLIST
// Mostra as músicas de uma playlist. Quem é dono também pode
// remover músicas e excluir a playlist.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

$playlist_id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// Dados da playlist e o nome do dono
$playlist = buscar_um($conexao,
    'SELECT p.id, p.nome, p.imagem, p.usuario_id, u.nome_exibicao AS dono
     FROM playlists p
     JOIN usuarios u ON u.id = p.usuario_id
     WHERE p.id = ?',
    'i', array($playlist_id));

$eh_dono = ($playlist !== null && (int) $playlist['usuario_id'] === $meu_id);

// ----- AÇÕES (só o dono pode) -----
if ($playlist !== null && $eh_dono && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';

    if ($acao === 'remover') {
        $musica_id = (int) ($_POST['musica'] ?? 0);
        executar($conexao,
            'DELETE FROM playlist_musicas WHERE playlist_id = ? AND musica_id = ?',
            'ii', array($playlist_id, $musica_id));
        header('Location: playlist.php?id=' . $playlist_id);
        exit;
    }

    if ($acao === 'excluir') {
        // As músicas dentro da playlist são apagadas junto (ON DELETE CASCADE no banco)
        executar($conexao, 'DELETE FROM playlists WHERE id = ?', 'i', array($playlist_id));
        apagar_imagem('capas_playlists', $playlist['imagem']);
        header('Location: biblioteca.php');
        exit;
    }
}

$titulo_pagina = $playlist ? $playlist['nome'] : 'Playlist';
$menu_ativo    = 'playlist:' . $playlist_id;
require '../includes/cabecalho.php';

// ----- PLAYLIST NÃO EXISTE -----
if ($playlist === null) {
    echo '<h1 class="titulo-pagina">Playlist não encontrada</h1>';
    echo '<p class="estado-vazio">Essa playlist não existe mais. <a class="link-destaque" href="biblioteca.php">Voltar para Sua Biblioteca</a></p>';
    require '../includes/rodape.php';
    exit;
}

// ----- MÚSICAS DA PLAYLIST -----
// ATENÇÃO: esta ordem tem que ser igual à de ids_da_lista() em funcoes.php
$musicas = buscar_todos($conexao,
    SQL_MUSICAS . '
     JOIN playlist_musicas pm ON pm.musica_id = m.id
     WHERE pm.playlist_id = ?
     ORDER BY pm.id',
    'i', array($playlist_id));

$pedindo_confirmacao = ($eh_dono && isset($_GET['excluir']));
?>

<div class="cabecalho-pagina">
    <img class="cabecalho-capa" src="<?php echo limpar(url_imagem_playlist($playlist['imagem'])); ?>" alt="">
    <div>
        <span class="cabecalho-tipo">Playlist</span>
        <h1><?php echo limpar($playlist['nome']); ?></h1>
        <p class="cabecalho-info">
            <a class="link-destaque" href="perfil.php?id=<?php echo (int) $playlist['usuario_id']; ?>"><?php echo limpar($playlist['dono']); ?></a>
            · <?php echo texto_musicas(count($musicas)); ?>
        </p>
    </div>
</div>

<?php if ($pedindo_confirmacao) { ?>
    <!-- Confirmação antes de excluir (para ninguém apagar sem querer) -->
    <div class="aviso-exclusao">
        <p>Tem certeza que quer excluir a playlist "<?php echo limpar($playlist['nome']); ?>"? Isso não pode ser desfeito.</p>
        <form method="post" action="playlist.php">
            <input type="hidden" name="id" value="<?php echo $playlist_id; ?>">
            <input type="hidden" name="acao" value="excluir">
            <button type="submit" class="botao botao-perigo">Sim, excluir</button>
            <a class="botao botao-contorno" href="playlist.php?id=<?php echo $playlist_id; ?>">Cancelar</a>
        </form>
    </div>
<?php } ?>

<div class="barra-acoes">
    <?php if (count($musicas) > 0) { ?>
        <a class="botao" target="player" href="<?php echo limpar(link_tocar($musicas[0]['id'], 'playlist', $playlist_id)); ?>">Tocar tudo</a>
    <?php } ?>
    <?php if ($eh_dono && !$pedindo_confirmacao) { ?>
        <a class="botao botao-contorno" href="playlist.php?id=<?php echo $playlist_id; ?>&amp;excluir=1">Excluir playlist</a>
    <?php } ?>
</div>

<?php if (count($musicas) == 0) { ?>
    <p class="estado-vazio">
        Esta playlist está vazia.
        <?php if ($eh_dono) { ?>
            Use a <a class="link-destaque" href="buscar.php">Busca</a> e clique em "+ Playlist" para adicionar músicas.
        <?php } ?>
    </p>
<?php } else { ?>
    <div class="lista-musicas">
        <?php
        $numero = 1;
        foreach ($musicas as $musica) {

            // Botão "Remover" só aparece para o dono
            $botao_remover = '';
            if ($eh_dono) {
                $botao_remover  = '<form method="post" action="playlist.php">';
                $botao_remover .= '<input type="hidden" name="id" value="' . $playlist_id . '">';
                $botao_remover .= '<input type="hidden" name="acao" value="remover">';
                $botao_remover .= '<input type="hidden" name="musica" value="' . (int) $musica['id'] . '">';
                $botao_remover .= '<button type="submit" class="botao-texto">Remover</button>';
                $botao_remover .= '</form>';
            }

            mostrar_linha_musica($musica, $numero, 'playlist', $playlist_id, $botao_remover);
            $numero++;
        }
        ?>
    </div>
<?php } ?>

<?php require '../includes/rodape.php'; ?>
