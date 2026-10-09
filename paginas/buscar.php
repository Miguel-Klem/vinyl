<?php
// =====================================================
// BUSCAR
// Procura músicas (título, artista, álbum ou gênero) e pessoas (nome de exibição).
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];

$termo   = trim($_GET['q'] ?? '');
$musicas = array();
$pessoas = array();

if ($termo !== '') {
    // Os símbolos % e _ têm sentido especial no LIKE; aqui viram letras normais
    $parecido = '%' . addcslashes($termo, '%_\\') . '%';

    $musicas = buscar_todos($conexao,
        SQL_MUSICAS . '
         WHERE m.titulo LIKE ? OR a.nome LIKE ? OR m.album LIKE ? OR m.genero LIKE ?
         ORDER BY m.total_reproducoes DESC, m.titulo
         LIMIT 50',
        'ssss', array($parecido, $parecido, $parecido, $parecido));

    $pessoas = buscar_todos($conexao,
        'SELECT id, nome_exibicao, foto_perfil FROM usuarios
         WHERE nome_exibicao LIKE ? AND id <> ?
         ORDER BY nome_exibicao
         LIMIT 20',
        'si', array($parecido, $meu_id));
}

$titulo_pagina = 'Buscar';
$menu_ativo    = 'buscar';
require '../includes/cabecalho.php';
?>

<h1 class="titulo-pagina">Buscar</h1>

<form class="busca-form" method="get" action="buscar.php">
    <input type="search" name="q" placeholder="Músicas, artistas, álbuns ou pessoas" value="<?php echo limpar($termo); ?>" autofocus>
    <button type="submit" class="botao">Buscar</button>
</form>

<?php if ($termo === '') { ?>

    <p class="estado-vazio">Digite o nome de uma música, artista, álbum ou pessoa.</p>

<?php } else { ?>

    <section class="secao">
        <h2>Músicas</h2>
        <?php if (count($musicas) == 0) { ?>
            <p class="estado-vazio">Nenhuma música encontrada para "<?php echo limpar($termo); ?>".</p>
        <?php } else { ?>
            <div class="lista-musicas">
                <?php
                $numero = 1;
                foreach ($musicas as $musica) {
                    mostrar_linha_musica($musica, $numero);
                    $numero++;
                }
                ?>
            </div>
        <?php } ?>
    </section>

    <section class="secao">
        <h2>Pessoas</h2>
        <?php if (count($pessoas) == 0) { ?>
            <p class="estado-vazio">Nenhuma pessoa encontrada.</p>
        <?php } else { ?>
            <div class="fileira">
                <?php foreach ($pessoas as $pessoa) { mostrar_cartao_pessoa($pessoa); } ?>
            </div>
        <?php } ?>
    </section>

<?php } ?>

<?php require '../includes/rodape.php'; ?>
