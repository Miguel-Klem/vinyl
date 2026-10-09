<?php
// =====================================================
// TELA DE ENTRAR (LOGIN)
// Parte de cima: PHP que processa o formulário.
// Parte de baixo: HTML da tela.
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

// Se a pessoa já está logada, não precisa ver o login: vai para o início
if (isset($_SESSION['usuario_id'])) {
    header('Location: principal.html');
    exit;
}

$erro     = '';   // mensagem de erro (vazia = sem erro)
$mensagem = '';   // mensagem de sucesso

// Mensagens que chegam de outras telas pelo endereço (?cadastro=ok)
if (isset($_GET['cadastro'])) {
    $mensagem = 'Conta criada com sucesso! Entre com seu e-mail e senha.';
}
if (isset($_GET['senha_alterada'])) {
    $mensagem = 'Senha alterada! Entre com a nova senha.';
}

// Só entra aqui quando a pessoa clicou no botão "Entrar"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Pega o que foi digitado (o "?? ''" evita erro se vier vazio)
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha o e-mail e a senha.';
    } else {

        // Procura o usuário pelo e-mail.
        // O "?" é um espaço reservado: o PHP coloca o e-mail ali com segurança.
        $comando = $conexao->prepare('SELECT id, senha, nome_exibicao FROM usuarios WHERE email = ?');
        $comando->bind_param('s', $email);   // 's' = texto (string)
        $comando->execute();
        $usuario = $comando->get_result()->fetch_assoc();   // dados do usuário (ou vazio)

        // password_verify compara a senha digitada com a senha embaralhada do banco
        if ($usuario && password_verify($senha, $usuario['senha'])) {

            // Deu certo! Guarda quem é na sessão
            session_regenerate_id(true);
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome_exibicao'];

            header('Location: principal.html');
            exit;
        } else {
            // Mensagem igual para e-mail errado e senha errada (de propósito)
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Vinyl</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<div class="caixa-formulario">
    <img class="logo-formulario" src="../imagens/logo.svg" alt="Logo Vinyl">
    <h1>Entrar no Vinyl</h1>

    <?php if ($erro !== '') { ?>
        <p class="mensagem-erro"><?php echo limpar($erro); ?></p>
    <?php } ?>

    <?php if ($mensagem !== '') { ?>
        <p class="mensagem-sucesso"><?php echo limpar($mensagem); ?></p>
    <?php } ?>

    <form method="post" action="entrar.php">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required
               value="<?php echo limpar($_POST['email'] ?? ''); ?>">

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>

        <!-- "Esqueceu a Senha?" fica embaixo da senha -->
        <a class="link-esqueceu" href="recuperar_senha.php">Esqueceu a Senha?</a>

        <button type="submit" class="botao botao-largo">Entrar</button>
    </form>

    <!-- Botão de criar conta fica embaixo do botão de entrar -->
    <a class="botao botao-contorno botao-largo botao-espaco" href="cadastro.php">Criar conta</a>
</div>

</body>
</html>
