<?php
// =====================================================
// SEGUIR / DEIXAR DE SEGUIR
// Não mostra nada na tela: faz a ação no banco e volta para o perfil.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $alvo_id = (int) ($_POST['id'] ?? 0);
    $acao    = $_POST['acao'] ?? '';

    // Não dá para seguir a si mesmo, e a pessoa precisa existir
    $existe = buscar_um($conexao, 'SELECT id FROM usuarios WHERE id = ?', 'i', array($alvo_id));

    if ($existe !== null && $alvo_id !== $meu_id) {

        if ($acao === 'seguir') {
            // INSERT IGNORE: se já segue, não dá erro
            executar($conexao,
                'INSERT IGNORE INTO seguidores (seguidor_id, seguido_id) VALUES (?, ?)',
                'ii', array($meu_id, $alvo_id));

        } elseif ($acao === 'deixar') {
            executar($conexao,
                'DELETE FROM seguidores WHERE seguidor_id = ? AND seguido_id = ?',
                'ii', array($meu_id, $alvo_id));
        }

        header('Location: perfil.php?id=' . $alvo_id);
        exit;
    }
}

header('Location: perfil.php');
exit;
