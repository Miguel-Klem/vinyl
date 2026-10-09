<?php
// SAIR (LOGOUT): esquece quem estava logado e volta para a porta de entrada (index.html).
require_once '../includes/sessao.php';

$_SESSION = [];       // limpa tudo que estava guardado
session_destroy();    // encerra a sessão

header('Location: ../index.html');
exit;
