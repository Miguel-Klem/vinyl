<?php
// =====================================================
// SESSÃO = a "memória" do site sobre quem está logado.
// Quem faz login ganha $_SESSION['usuario_id'] e $_SESSION['usuario_nome'].
//
// Como usar em uma página:
//     require_once '../includes/sessao.php';
// =====================================================

// Liga a sessão (só se ainda não estiver ligada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Para páginas que ficam SOLTAS (tela cheia).
// Se a pessoa não estiver logada, é mandada para a tela de entrar.
function exigir_login() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: entrar.php');
        exit;
    }
}

// Para páginas que aparecem DENTRO do site (menu, player...).
// Se a sessão acabou, mostra um aviso com link para entrar de novo.
// (target="_top" faz o link abrir na janela inteira, e não dentro do quadro)
function exigir_login_quadro() {
    if (!isset($_SESSION['usuario_id'])) {
        echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8">';
        echo '<link rel="stylesheet" href="../css/estilo.css"></head><body>';
        echo '<p class="aviso-sessao">Sua sessão terminou. ';
        echo '<a target="_top" href="entrar.php">Entrar de novo</a></p>';
        echo '</body></html>';
        exit;
    }
}
