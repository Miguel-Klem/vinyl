<?php
// CONEXÃO COM O BANCO

$servidor = 'localhost';
$usuario  = 'root';
$senha    = '94265221118'; // Senha MySQL
$banco    = 'vinyl';

// Tenta conectar
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Se deu erro, mostra o motivo e para tudo
if ($conexao->connect_error) {
    die('Erro ao conectar no banco: ' . $conexao->connect_error);
}

// Faz os acentos funcionarem direitinho
$conexao->set_charset('utf8mb4');
