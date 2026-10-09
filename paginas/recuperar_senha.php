<?php
// =====================================================
// ESQUECEU A SENHA - PASSO 1
// A pessoa informa o e-mail e a data de nascimento.
// Se os dois baterem com uma conta, ela segue para a tela
// de criar uma NOVA senha (nova_senha.php).
//
// (A senha antiga não dá para mostrar: ela é guardada embaralhada.)
// A data de nascimento serve para confirmar que é a dona da conta.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email      = strtolower(trim($_POST['email'] ?? ''));
    $nascimento = $_POST['data_nascimento'] ?? '';

    if ($email === '' || $nascimento === '') {
        $erro = 'Preencha o e-mail e a data de nascimento.';
    } else {

        // Procura uma conta com esse e-mail E essa data de nascimento
        $comando = $conexao->prepare('SELECT id FROM usuarios WHERE email = ? AND data_nascimento = ?');
        $comando->bind_param('ss', $email, $nascimento);
        $comando->execute();
        $usuario = $comando->get_result()->fetch_assoc();

        if ($usuario) {
            // Guarda na sessão qual conta vai trocar a senha e vai para o passo 2
            $_SESSION['recuperar_usuario_id'] = $usuario['id'];
            header('Location: nova_senha.php');
            exit;
        } else {
            $erro = 'Não encontramos uma conta com esses dados.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar senha - Vinyl</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<div class="caixa-formulario">
    <img class="logo-formulario" src="../imagens/logo.svg" alt="Logo Vinyl">
    <h1>Recuperar senha</h1>

    <?php if ($erro !== '') { ?>
        <p class="mensagem-erro"><?php echo limpar($erro); ?></p>
    <?php } ?>

    <form method="post" action="recuperar_senha.php">
        <label for="email">E-mail da conta</label>
        <input type="email" id="email" name="email" required
               value="<?php echo limpar($_POST['email'] ?? ''); ?>">

        <label for="data_nascimento">Data de nascimento</label>
        <input type="date" id="data_nascimento" name="data_nascimento" required>

        <button type="submit" class="botao botao-largo">Continuar</button>
    </form>

    <a class="link-voltar" href="entrar.php">Voltar para entrar</a>
</div>

</body>
</html>
