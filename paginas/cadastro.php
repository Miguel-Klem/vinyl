<?php

// TELA DE CRIAR CONTA (CADASTRO)

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

// Quem já está logado não precisa criar conta
if (isset($_SESSION['usuario_id'])) {
    header('Location: principal.html');
    exit;
}

$erro = '';

// Só entra aqui quando a pessoa clicou em "Criar conta"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Pega tudo que foi digitado
    $email           = strtolower(trim($_POST['email'] ?? ''));
    $senha           = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $nascimento      = $_POST['data_nascimento'] ?? '';
    $nome_exibicao   = trim($_POST['nome_exibicao'] ?? '');
    $aceitou_termos  = isset($_POST['aceitar_termos']);   // true se a caixinha foi marcada

    //  VALIDAÇÕES: cada "if" confere uma regra. Para no primeiro erro.
    if ($email === '' || $senha === '' || $confirmar_senha === '' || $nascimento === '' || $nome_exibicao === '') {
        $erro = 'Preencha todos os campos.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';

    } elseif (strlen($senha) < 6) {
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';

    } elseif ($senha !== $confirmar_senha) {
        $erro = 'A senha e a confirmação não são iguais.';

    } elseif (!data_valida($nascimento) || new DateTime($nascimento) > new DateTime('today')) {
        $erro = 'Data de nascimento inválida.';

    } elseif (calcular_idade($nascimento) < 13) {
        $erro = 'Você precisa ter pelo menos 13 anos para criar uma conta.';

    } elseif (strlen($nome_exibicao) > 60) {
        $erro = 'O nome de exibição pode ter no máximo 60 letras.';

    } elseif (!$aceitou_termos) {
        $erro = 'Você precisa aceitar os Termos e a Política de Privacidade.';

    } else {

        // Confere se esse e-mail já tem conta
        $comando = $conexao->prepare('SELECT id FROM usuarios WHERE email = ?');
        $comando->bind_param('s', $email);
        $comando->execute();
        $ja_existe = $comando->get_result()->fetch_assoc();

        if ($ja_existe) {
            $erro = 'Já existe uma conta com esse e-mail.';
        } else {

            // Embaralha a senha antes de guardar (nunca se guarda a senha pura!)
            $senha_embaralhada = password_hash($senha, PASSWORD_DEFAULT);

            // Salva o novo usuário no banco (é isso que aparece no Workbench)
            $comando = $conexao->prepare(
                'INSERT INTO usuarios (email, senha, nome_exibicao, data_nascimento) VALUES (?, ?, ?, ?)'
            );
            $comando->bind_param('ssss', $email, $senha_embaralhada, $nome_exibicao, $nascimento);
            $comando->execute();

            // Conta criada: manda para o login com mensagem de sucesso
            header('Location: entrar.php?cadastro=ok');
            exit;
        }
    }
}

// Menor data permitida no calendário: hoje menos 13 anos
$data_maxima = date('Y-m-d', strtotime('-13 years'));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta - Vinyl</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

<div class="caixa-formulario">
    <img class="logo-formulario" src="../imagens/logo.svg" alt="Logo Vinyl">
    <h1>Criar conta</h1>

    <?php if ($erro !== '') { ?>
        <p class="mensagem-erro"><?php echo limpar($erro); ?></p>
    <?php } ?>

    <form method="post" action="cadastro.php">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required
               value="<?php echo limpar($_POST['email'] ?? ''); ?>">

        <label for="senha">Senha (mínimo 6 caracteres)</label>
        <input type="password" id="senha" name="senha" required>

        <label for="confirmar_senha">Confirmar senha</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" required>

        <label for="data_nascimento">Data de nascimento (mínimo 13 anos)</label>
        <input type="date" id="data_nascimento" name="data_nascimento" required
               max="<?php echo $data_maxima; ?>"
               value="<?php echo limpar($_POST['data_nascimento'] ?? ''); ?>">

        <label for="nome_exibicao">Nome de exibição</label>
        <input type="text" id="nome_exibicao" name="nome_exibicao" maxlength="60" required
               value="<?php echo limpar($_POST['nome_exibicao'] ?? ''); ?>">

        <!-- Caixinha dos termos. Os links abrem em outra aba para não perder o que foi digitado -->
        <div class="linha-termos">
            <input type="checkbox" id="aceitar_termos" name="aceitar_termos" required>
            <label for="aceitar_termos">
                Li e aceito os
                <a href="termos.php" target="_blank">Termos e Condições de Uso</a> e a
                <a href="termos.php#privacidade" target="_blank">Política de Privacidade</a>.
            </label>
        </div>

        <button type="submit" class="botao botao-largo">Criar conta</button>
    </form>

    <a class="link-voltar" href="entrar.php">Já tenho conta. Voltar para entrar</a>
</div>

</body>
</html>
