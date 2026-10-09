<?php
// =====================================================
// ESQUECEU A SENHA - PASSO 2
// Só chega aqui quem passou pelo passo 1 (recuperar_senha.php).
// A pessoa escolhe uma nova senha.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

// Se não passou pelo passo 1, volta para lá
if (!isset($_SESSION['recuperar_usuario_id'])) {
    header('Location: recuperar_senha.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $senha           = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (strlen($senha) < 6) {
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';

    } elseif ($senha !== $confirmar_senha) {
        $erro = 'A senha e a confirmação não são iguais.';

    } else {

        // Embaralha a nova senha e troca no banco
        $senha_embaralhada = password_hash($senha, PASSWORD_DEFAULT);
        $usuario_id        = $_SESSION['recuperar_usuario_id'];

        $comando = $conexao->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
        $comando->bind_param('si', $senha_embaralhada, $usuario_id);   // 's' = texto, 'i' = número
        $comando->execute();

        // Apaga a "permissão" da sessão e volta para o login
        unset($_SESSION['recuperar_usuario_id']);
        header('Location: entrar.php?senha_alterada=ok');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova senha - Vinyl</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<div class="caixa-formulario">
    <img class="logo-formulario" src="../imagens/logo.svg" alt="Logo Vinyl">
    <h1>Nova senha</h1>

    <?php if ($erro !== '') { ?>
        <p class="mensagem-erro"><?php echo limpar($erro); ?></p>
    <?php } ?>

    <form method="post" action="nova_senha.php">
        <label for="senha">Nova senha (mínimo 6 caracteres)</label>
        <input type="password" id="senha" name="senha" required>

        <label for="confirmar_senha">Confirmar nova senha</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required>

        <button type="submit" class="botao botao-largo">Salvar nova senha</button>
    </form>

    <a class="link-voltar" href="entrar.php">Cancelar</a>
</div>

</body>
</html>
