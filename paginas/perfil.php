<?php
// =====================================================
// PERFIL
// Sem ?id= mostra o SEU perfil. Com ?id=5 mostra o perfil de outra pessoa.
// Mostra: foto, nome, quantidade de playlists, seguidores e seguindo.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

// Qual perfil mostrar (o meu, se não vier nenhum id)
$perfil_id = isset($_GET['id']) ? (int) $_GET['id'] : $meu_id;
if ($perfil_id <= 0) { $perfil_id = $meu_id; }

$eh_meu_perfil = ($perfil_id === $meu_id);

$pessoa = buscar_um($conexao,
    'SELECT id, nome_exibicao, foto_perfil FROM usuarios WHERE id = ?',
    'i', array($perfil_id));

$titulo_pagina = $pessoa ? $pessoa['nome_exibicao'] : 'Perfil';
$menu_ativo    = '';
require '../includes/cabecalho.php';

if ($pessoa === null) {
    echo '<h1 class="titulo-pagina">Perfil não encontrado</h1>';
    echo '<p class="estado-vazio"><a class="link-destaque" href="inicio.php">Voltar para o início</a></p>';
    require '../includes/rodape.php';
    exit;
}

// Números do perfil
$total_playlists  = buscar_um($conexao, 'SELECT COUNT(*) AS total FROM playlists WHERE usuario_id = ?', 'i', array($perfil_id));
$total_seguidores = buscar_um($conexao, 'SELECT COUNT(*) AS total FROM seguidores WHERE seguido_id = ?', 'i', array($perfil_id));
$total_seguindo   = buscar_um($conexao, 'SELECT COUNT(*) AS total FROM seguidores WHERE seguidor_id = ?', 'i', array($perfil_id));

// Eu já sigo essa pessoa?
$eu_sigo = buscar_um($conexao,
    'SELECT 1 AS sim FROM seguidores WHERE seguidor_id = ? AND seguido_id = ?',
    'ii', array($meu_id, $perfil_id));

// Playlists dessa pessoa
$playlists = buscar_todos($conexao,
    'SELECT p.id, p.nome, p.imagem,
            (SELECT COUNT(*) FROM playlist_musicas pm WHERE pm.playlist_id = p.id) AS total
     FROM playlists p
     WHERE p.usuario_id = ?
     ORDER BY p.id DESC',
    'i', array($perfil_id));
?>

<?php if (isset($_GET['salvo'])) { ?>
    <p class="mensagem-sucesso">Perfil atualizado!</p>
<?php } ?>

<div class="cabecalho-pagina">
    <img class="foto-perfil-grande" src="<?php echo limpar(url_foto_perfil($pessoa['foto_perfil'])); ?>" alt="Foto de perfil">
    <div>
        <span class="cabecalho-tipo">Perfil</span>
        <h1><?php echo limpar($pessoa['nome_exibicao']); ?></h1>
        <p class="cabecalho-info">
            <strong><?php echo (int) $total_playlists['total']; ?></strong> playlists ·
            <strong><?php echo (int) $total_seguidores['total']; ?></strong> seguidores ·
            <strong><?php echo (int) $total_seguindo['total']; ?></strong> seguindo
        </p>
    </div>
</div>

<div class="barra-acoes">
    <?php if ($eh_meu_perfil) { ?>
        <a class="botao" href="editar_perfil.php">Editar perfil</a>
        <a class="botao botao-contorno" target="_top" href="sair.php">Sair da conta</a>
    <?php } else { ?>
        <!-- Seguir / deixar de seguir -->
        <form method="post" action="seguir.php">
            <input type="hidden" name="id" value="<?php echo $perfil_id; ?>">
            <?php if ($eu_sigo) { ?>
                <input type="hidden" name="acao" value="deixar">
                <button type="submit" class="botao botao-contorno">Seguindo (clique para deixar de seguir)</button>
            <?php } else { ?>
                <input type="hidden" name="acao" value="seguir">
                <button type="submit" class="botao">Seguir</button>
            <?php } ?>
        </form>
    <?php } ?>
</div>

<section class="secao">
    <h2>Playlists</h2>
    <?php if (count($playlists) == 0) { ?>
        <p class="estado-vazio">Nenhuma playlist ainda.</p>
    <?php } else { ?>
        <div class="fileira">
            <?php foreach ($playlists as $playlist) { mostrar_cartao_playlist($playlist); } ?>
        </div>
    <?php } ?>
</section>

<?php require '../includes/rodape.php'; ?>
