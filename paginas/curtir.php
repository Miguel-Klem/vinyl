<?php
// =====================================================
// BOTÃO DE CURTIR (o vinil)
// Mini-página que aparece dentro do player, ao lado do nome da música.
// Clicar no vinil curte ou descurte a música.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

$musica_id = (int) ($_GET['musica'] ?? $_POST['musica'] ?? 0);

// ----- Clicou no vinil -----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $musica_id > 0) {

    $ja_curtiu = buscar_um($conexao,
        'SELECT 1 AS sim FROM curtidas WHERE usuario_id = ? AND musica_id = ?',
        'ii', array($meu_id, $musica_id));

    if ($ja_curtiu) {
        // Já estava curtida: descurte
        executar($conexao,
            'DELETE FROM curtidas WHERE usuario_id = ? AND musica_id = ?',
            'ii', array($meu_id, $musica_id));
    } else {
        // Só curte se a música existir de verdade
        $existe = buscar_um($conexao, 'SELECT id FROM musicas WHERE id = ?', 'i', array($musica_id));
        if ($existe !== null) {
            executar($conexao,
                'INSERT IGNORE INTO curtidas (usuario_id, musica_id) VALUES (?, ?)',
                'ii', array($meu_id, $musica_id));
        }
    }

    // Volta para a própria página (evita repetir a ação ao recarregar)
    header('Location: curtir.php?musica=' . $musica_id);
    exit;
}

// ----- Mostrar o vinil: cheio (roxo) se curtida, vazio se não -----
$curtida = buscar_um($conexao,
    'SELECT 1 AS sim FROM curtidas WHERE usuario_id = ? AND musica_id = ?',
    'ii', array($meu_id, $musica_id));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Curtir</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="corpo-curtir">

<form method="post" action="curtir.php">
    <input type="hidden" name="musica" value="<?php echo $musica_id; ?>">
    <button type="submit" class="botao-vinil<?php echo $curtida ? ' curtida' : ''; ?>"
            title="<?php echo $curtida ? 'Remover das Músicas Curtidas' : 'Curtir'; ?>">
        <svg viewBox="0 0 24 24">
            <circle class="vinil-disco" cx="12" cy="12" r="10"/>
            <circle class="vinil-sulco" cx="12" cy="12" r="6.5"/>
            <circle class="vinil-miolo" cx="12" cy="12" r="3"/>
        </svg>
    </button>
</form>

</body>
</html>
