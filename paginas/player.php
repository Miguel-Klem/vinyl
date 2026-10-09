<?php
// =====================================================
// PLAYER (a barra de música que fica embaixo do site)
//
// Esta página fica no quadro "player" do principal.html. Por ser um quadro
// separado, a música continua tocando quando você troca de página lá em cima.
//
// Como ela recebe ordens (pelo endereço):
//   player.php?tocar=5                      -> toca a música 5 (avulsa)
//   player.php?tocar=5&lista=playlist&id=2  -> toca a música 5 DENTRO da playlist 2
//   player.php?tocar=5&lista=curtidas       -> toca a música 5 dentro das curtidas
//   player.php?acao=proxima                 -> próxima música
//   player.php?acao=anterior                -> música anterior
//
// O que o site lembra durante o login (na sessão):
//   $_SESSION['tocando']    = id da música atual
//   $_SESSION['fila']       = ids da playlist em que ela está (vazio = música avulsa)
//   $_SESSION['anteriores'] = músicas avulsas já tocadas (para o botão "anterior")
//   $_SESSION['tocar_agora']= true quando deve começar a tocar sozinha
// =====================================================

require_once '../configuracao/conexao.php';
require_once '../includes/sessao.php';
require_once '../includes/funcoes.php';

exigir_login_quadro();
$meu_id = (int) $_SESSION['usuario_id'];


// Muda para uma nova música: guarda a atual como "anterior",
// soma 1 nas reproduções (Top 10) e registra no histórico (recentes).
function comecar_musica($conexao, $meu_id, $musica_id, $guardar_anterior) {

    $atual = (int) ($_SESSION['tocando'] ?? 0);

    if ($guardar_anterior && $atual > 0 && $atual !== $musica_id) {
        if (!isset($_SESSION['anteriores'])) {
            $_SESSION['anteriores'] = array();
        }
        $_SESSION['anteriores'][] = $atual;

        // Guarda no máximo 50 para não crescer sem fim
        if (count($_SESSION['anteriores']) > 50) {
            array_shift($_SESSION['anteriores']);
        }
    }

    $_SESSION['tocando']     = $musica_id;
    $_SESSION['tocar_agora'] = true;

    executar($conexao,
        'UPDATE musicas SET total_reproducoes = total_reproducoes + 1 WHERE id = ?',
        'i', array($musica_id));

    executar($conexao,
        'INSERT INTO historico (usuario_id, musica_id) VALUES (?, ?)',
        'ii', array($meu_id, $musica_id));
}


// ----- ORDEM: TOCAR UMA MÚSICA -----
if (isset($_GET['tocar'])) {

    $musica_id = (int) $_GET['tocar'];
    $existe    = buscar_um($conexao, 'SELECT id FROM musicas WHERE id = ?', 'i', array($musica_id));

    if ($existe !== null) {

        $lista    = $_GET['lista'] ?? '';
        $id_lista = (int) ($_GET['id'] ?? 0);

        // Fila = músicas da playlist. Se a música não está nela, é tratada como avulsa.
        $fila = ids_da_lista($conexao, $meu_id, $lista, $id_lista);
        if (!in_array($musica_id, $fila)) {
            $fila = array();
        }
        $_SESSION['fila'] = $fila;

        comecar_musica($conexao, $meu_id, $musica_id, true);
    }

    // Recarrega a página sem as ordens no endereço (assim apertar F5 não toca de novo)
    header('Location: player.php');
    exit;
}


// ----- ORDEM: PRÓXIMA / ANTERIOR -----
if (isset($_GET['acao'])) {

    $acao    = $_GET['acao'];
    $atual   = (int) ($_SESSION['tocando'] ?? 0);
    $fila    = $_SESSION['fila'] ?? array();
    $posicao = array_search($atual, $fila);       // posição na playlist (false = música avulsa)

    if ($atual > 0 && $acao === 'proxima') {

        if ($posicao !== false) {
            // Dentro de uma playlist: vai para a seguinte (depois da última, volta para a primeira)
            $proxima = $fila[($posicao + 1) % count($fila)];
        } else {
            // Música avulsa: vai para uma música qualquer do banco
            $sorteada = buscar_um($conexao,
                'SELECT id FROM musicas WHERE id <> ? ORDER BY RAND() LIMIT 1',
                'i', array($atual));
            $proxima = $sorteada ? (int) $sorteada['id'] : 0;
        }

        if ($proxima > 0) {
            comecar_musica($conexao, $meu_id, (int) $proxima, true);
        }

    } elseif ($atual > 0 && $acao === 'anterior') {

        if ($posicao !== false) {
            // Dentro de uma playlist: volta uma posição (na primeira não faz nada)
            if ($posicao > 0) {
                comecar_musica($conexao, $meu_id, (int) $fila[$posicao - 1], false);
            }
        } else {
            // Música avulsa: volta para a última que tocou antes dela (se existir)
            $anteriores = $_SESSION['anteriores'] ?? array();
            if (count($anteriores) > 0) {
                $anterior_id = array_pop($anteriores);
                $_SESSION['anteriores'] = $anteriores;
                comecar_musica($conexao, $meu_id, (int) $anterior_id, false);
            }
        }
    }

    header('Location: player.php');
    exit;
}


// ----- MOSTRAR O PLAYER -----
$tocando_id = (int) ($_SESSION['tocando'] ?? 0);
$musica     = null;
if ($tocando_id > 0) {
    $musica = buscar_um($conexao, SQL_MUSICAS . ' WHERE m.id = ?', 'i', array($tocando_id));
}

// Só começa a tocar sozinha logo depois de uma ordem (e não ao recarregar a página)
$tocar_agora = !empty($_SESSION['tocar_agora']);
unset($_SESSION['tocar_agora']);

// Os botões anterior/próxima ficam ativos?
$pode_proxima  = ($musica !== null);
$pode_anterior = false;
if ($musica !== null) {
    $fila    = $_SESSION['fila'] ?? array();
    $posicao = array_search($tocando_id, $fila);
    if ($posicao !== false) {
        $pode_anterior = ($posicao > 0);
    } else {
        $pode_anterior = (count($_SESSION['anteriores'] ?? array()) > 0);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player - Vinyl</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="corpo-player">

<div class="barra-player">

    <!-- ESQUERDA: capa, nome, artista e o vinil de curtir -->
    <div class="player-info">
        <?php if ($musica !== null) { ?>
            <img class="player-capa" src="<?php echo limpar(url_capa($musica['capa'])); ?>" alt="Capa">
            <div class="player-textos">
                <strong><?php echo limpar($musica['titulo']); ?></strong>
                <small><?php echo limpar($musica['artista']); ?></small>
            </div>
            <!-- O vinil fica em um mini-quadro próprio: curtir não interrompe a música -->
            <iframe class="quadro-curtir" src="curtir.php?musica=<?php echo (int) $musica['id']; ?>" scrolling="no" title="Curtir"></iframe>
        <?php } else { ?>
            <img class="player-capa" src="../imagens/logo.svg" alt="">
            <div class="player-textos">
                <strong>Nenhuma música tocando</strong>
                <small>Escolha uma música para começar</small>
            </div>
        <?php } ?>
    </div>

    <!-- CENTRO: anterior | barra de tempo, tempo e volume | próxima -->
    <div class="player-controles">

        <?php if ($pode_anterior) { ?>
            <a class="botao-controle" href="player.php?acao=anterior" title="Música anterior">
                <svg viewBox="0 0 24 24"><path d="M6 5h2.5v14H6zM20 5v14L9.5 12z"/></svg>
            </a>
        <?php } else { ?>
            <span class="botao-controle desativado" title="Não há música anterior">
                <svg viewBox="0 0 24 24"><path d="M6 5h2.5v14H6zM20 5v14L9.5 12z"/></svg>
            </span>
        <?php } ?>

        <?php if ($musica !== null) { ?>
            <!-- O player do navegador já traz play/pause, barra de tempo e volume -->
            <audio controls preload="auto" controlslist="nodownload"
                   <?php if ($tocar_agora) { echo 'autoplay'; } ?>
                   src="../musicas/<?php echo rawurlencode($musica['arquivo_mp3']); ?>">
            </audio>
        <?php } else { ?>
            <div class="audio-vazio">0:00 ──────────────── 0:00</div>
        <?php } ?>

        <?php if ($pode_proxima) { ?>
            <a class="botao-controle" href="player.php?acao=proxima" title="Próxima música">
                <svg viewBox="0 0 24 24"><path d="M15.5 5H18v14h-2.5zM4 5v14l10.5-7z"/></svg>
            </a>
        <?php } else { ?>
            <span class="botao-controle desativado" title="Nenhuma música tocando">
                <svg viewBox="0 0 24 24"><path d="M15.5 5H18v14h-2.5zM4 5v14l10.5-7z"/></svg>
            </span>
        <?php } ?>
    </div>

    <!-- DIREITA: atalho para colocar a música em uma playlist (abre no quadro de cima) -->
    <div class="player-extra">
        <?php if ($musica !== null) { ?>
            <a href="adicionar_playlist.php?musica=<?php echo (int) $musica['id']; ?>" target="conteudo">+ Playlist</a>
        <?php } ?>
    </div>

</div>

</body>
</html>
