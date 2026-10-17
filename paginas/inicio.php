<?php

// PÁGINA INICIAL

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

// Últimas músicas que esta pessoa ouviu (sem repetir). Se nunca ouviu nada, vem vazio.
$recentes = buscar_todos($conexao,
    SQL_MUSICAS . '
     JOIN (SELECT musica_id, MAX(id) AS ultimo FROM historico WHERE usuario_id = ? GROUP BY musica_id) h
          ON h.musica_id = m.id
     ORDER BY h.ultimo DESC
     LIMIT 10',
    'i', array($meu_id));

// Top 10 mais ouvidas do site (o "Brasil" do Vinyl)
$top_brasil = buscar_todos($conexao,
    SQL_MUSICAS . ' ORDER BY m.total_reproducoes DESC, m.id ASC LIMIT 10');

// Top 10 do Rock: só músicas cujo gênero combina com a lista em generos_rock()
$top_rock = buscar_todos($conexao,
    SQL_MUSICAS . ' WHERE ' . condicao_rock() . ' ORDER BY m.total_reproducoes DESC, m.id ASC LIMIT 10');

// Cumprimento conforme a hora
$hora = (int) date('H');
if ($hora < 12)      { $cumprimento = 'Bom dia'; }
elseif ($hora < 18)  { $cumprimento = 'Boa tarde'; }
else                 { $cumprimento = 'Boa noite'; }

$titulo_pagina = 'Início';
$menu_ativo    = 'inicio';
require '../includes/cabecalho.php';
?>

<h1 class="titulo-pagina"><?php echo $cumprimento . ', ' . limpar($_SESSION['usuario_nome']); ?></h1>

<?php if (count($recentes) > 0) { ?>
    <section class="secao">
        <h2>Ouvidas recentemente</h2>
        <div class="fileira">
            <?php foreach ($recentes as $musica) { mostrar_cartao_musica($musica); } ?>
        </div>
    </section>
<?php } ?>

<section class="secao">
    <h2>Top 10 mais ouvidas do Brasil</h2>
    <?php if (count($top_brasil) == 0) { ?>
        <p class="estado-vazio">Ainda não há músicas cadastradas.</p>
    <?php } else { ?>
        <div class="fileira">
            <?php
            $posicao = 1;
            foreach ($top_brasil as $musica) {
                mostrar_cartao_musica($musica, '', 0, $posicao);
                $posicao++;
            }
            ?>
        </div>
    <?php } ?>
</section>

<section class="secao">
    <h2>Top 10 mais ouvidas do Rock</h2>
    <?php if (count($top_rock) == 0) { ?>
        <p class="estado-vazio">Ainda não há músicas de Rock cadastradas.</p>
    <?php } else { ?>
        <div class="fileira">
            <?php
            $posicao = 1;
            foreach ($top_rock as $musica) {
                mostrar_cartao_musica($musica, '', 0, $posicao);
                $posicao++;
            }
            ?>
        </div>
    <?php } ?>
</section>

<?php require '../includes/rodape.php'; ?>
