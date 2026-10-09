<?php
// =====================================================
// CABEÇALHO DAS PÁGINAS DO SITE: menu lateral + barra de cima.
// Toda página do site faz:
//     $titulo_pagina = 'Início';      // título da aba
//     $menu_ativo    = 'inicio';      // qual item do menu fica destacado
//     require '../includes/cabecalho.php';
//     ... conteúdo da página ...
//     require '../includes/rodape.php';
// (a página precisa já ter carregado conexao.php, sessao.php e funcoes.php)
// =====================================================

$meu_id = (int) $_SESSION['usuario_id'];

// Dados de quem está logado (foto e nome para a barra de cima)
$eu = buscar_um($conexao, 'SELECT nome_exibicao, foto_perfil FROM usuarios WHERE id = ?', 'i', array($meu_id));
if ($eu === null) {
    // A conta não existe mais no banco: trata como sessão encerrada
    unset($_SESSION['usuario_id']);
    exigir_login_quadro();
}

// Playlists da pessoa, para aparecerem no menu
$minhas_playlists = buscar_todos($conexao,
    'SELECT id, nome FROM playlists WHERE usuario_id = ? ORDER BY nome',
    'i', array($meu_id));

if (!isset($titulo_pagina)) { $titulo_pagina = 'Vinyl'; }
if (!isset($menu_ativo))    { $menu_ativo = ''; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo limpar($titulo_pagina); ?> - Vinyl</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<!-- ================= MENU LATERAL ================= -->
<nav class="menu-lateral">

    <a class="menu-logo" href="inicio.php">
        <img src="../imagens/logo.svg" alt="">
        <span>Vinyl</span>
    </a>

    <ul class="menu-itens">
        <li>
            <a href="inicio.php"<?php echo classe_ativo($menu_ativo, 'inicio'); ?>>
                <svg viewBox="0 0 24 24"><path d="M12 3 3 10v11h6v-7h6v7h6V10z"/></svg>
                Início
            </a>
        </li>
        <li>
            <a href="buscar.php"<?php echo classe_ativo($menu_ativo, 'buscar'); ?>>
                <svg viewBox="0 0 24 24"><path d="M10 2a8 8 0 1 0 4.9 14.3l5.4 5.4 1.4-1.4-5.4-5.4A8 8 0 0 0 10 2zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12z"/></svg>
                Buscar
            </a>
        </li>
        <li>
            <a href="biblioteca.php"<?php echo classe_ativo($menu_ativo, 'biblioteca'); ?>>
                <svg viewBox="0 0 24 24"><path d="M3 3h3v18H3zm5 0h3v18H8zm5.2.5 2.9-.8 4.8 17.7-2.9.8z"/></svg>
                Sua Biblioteca
            </a>
        </li>
    </ul>

    <h3 class="menu-titulo">Playlists</h3>
    <a class="botao botao-largo botao-pequeno" href="criar_playlist.php">+ Criar playlist</a>

    <ul class="menu-playlists">
        <!-- Playlist que já vem pronta: as músicas curtidas -->
        <li>
            <a href="curtidas.php"<?php echo classe_ativo($menu_ativo, 'curtidas'); ?>>Músicas Curtidas</a>
        </li>
        <!-- Playlists criadas pela pessoa -->
        <?php foreach ($minhas_playlists as $playlist_menu) { ?>
            <li>
                <a href="playlist.php?id=<?php echo (int) $playlist_menu['id']; ?>"<?php echo classe_ativo($menu_ativo, 'playlist:' . $playlist_menu['id']); ?>>
                    <?php echo limpar($playlist_menu['nome']); ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</nav>

<!-- ================= ÁREA PRINCIPAL ================= -->
<div class="area-principal">

    <!-- Barra de cima: foto do usuário no canto direito -->
    <header class="topo">
        <a class="topo-sair" target="_top" href="sair.php">Sair</a>
        <a class="topo-perfil" href="perfil.php" title="Seu perfil: <?php echo limpar($eu['nome_exibicao']); ?>">
            <img src="<?php echo limpar(url_foto_perfil($eu['foto_perfil'])); ?>" alt="Seu perfil">
        </a>
    </header>

    <main class="conteudo">
