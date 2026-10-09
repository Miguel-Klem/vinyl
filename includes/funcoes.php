<?php
// =====================================================
// FUNÇÕES QUE VÁRIAS PÁGINAS USAM
// Como usar:  require_once '../includes/funcoes.php';
//
// Índice:
//   1. Textos e datas
//   2. Banco de dados (buscar_todos, buscar_um, executar, inserir)
//   3. Imagens (mostrar e enviar)
//   4. Músicas (consulta pronta, Top Rock, filas de playlist)
//   5. Cartões e linhas de música (o que aparece na tela)
// =====================================================

// Caminho da pasta "imagens" no computador (usado para salvar e apagar arquivos)
define('PASTA_IMAGENS', __DIR__ . '/../imagens/');


// -----------------------------------------------------
// 1. TEXTOS E DATAS
// -----------------------------------------------------

// Deixa um texto seguro para mostrar na tela.
// (Evita que alguém escreva código HTML no nome e quebre o site.)
// Use sempre que mostrar algo digitado pelo usuário:  echo limpar($nome);
function limpar($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Calcula a idade em anos a partir da data de nascimento (formato 2005-03-20)
function calcular_idade($data_nascimento) {
    $nascimento = new DateTime($data_nascimento);
    $hoje       = new DateTime('today');
    return $nascimento->diff($hoje)->y;   // ->y = quantidade de anos
}

// Confere se o texto é uma data de verdade (ex: 2005-02-30 não existe)
function data_valida($texto) {
    $data = DateTime::createFromFormat('Y-m-d', $texto);
    return $data && $data->format('Y-m-d') === $texto;
}

// Escreve "1 música" ou "5 músicas"
function texto_musicas($quantidade) {
    return $quantidade == 1 ? '1 música' : $quantidade . ' músicas';
}

// Usada no menu: devolve class="ativo" na página em que a pessoa está
function classe_ativo($menu_ativo, $nome) {
    return $menu_ativo === $nome ? ' class="ativo"' : '';
}


// -----------------------------------------------------
// 2. BANCO DE DADOS
// Estas 4 funções evitam repetir o mesmo código toda hora.
// O "?" no SQL é um espaço reservado: o PHP coloca o valor ali com segurança.
// $tipos diz o tipo de cada valor: s = texto, i = número inteiro.
// Exemplo:
//   $usuario = buscar_um($conexao, 'SELECT * FROM usuarios WHERE id = ?', 'i', array($id));
// -----------------------------------------------------

// Devolve VÁRIAS linhas (uma lista). Se não achar nada, devolve lista vazia.
function buscar_todos($conexao, $sql, $tipos = '', $valores = array()) {
    $comando = $conexao->prepare($sql);
    if ($tipos !== '') {
        $comando->bind_param($tipos, ...$valores);
    }
    $comando->execute();
    $resultado = $comando->get_result();
    $linhas    = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : array();
    $comando->close();
    return $linhas;
}

// Devolve UMA linha só. Se não achar nada, devolve null.
function buscar_um($conexao, $sql, $tipos = '', $valores = array()) {
    $linhas = buscar_todos($conexao, $sql, $tipos, $valores);
    return count($linhas) > 0 ? $linhas[0] : null;
}

// Executa um INSERT / UPDATE / DELETE. Devolve quantas linhas foram afetadas.
function executar($conexao, $sql, $tipos = '', $valores = array()) {
    $comando = $conexao->prepare($sql);
    if ($tipos !== '') {
        $comando->bind_param($tipos, ...$valores);
    }
    $comando->execute();
    $afetadas = $comando->affected_rows;
    $comando->close();
    return $afetadas;
}

// Executa um INSERT e devolve o id que o banco criou para a nova linha.
function inserir($conexao, $sql, $tipos = '', $valores = array()) {
    $comando = $conexao->prepare($sql);
    if ($tipos !== '') {
        $comando->bind_param($tipos, ...$valores);
    }
    $comando->execute();
    $novo_id = $comando->insert_id;
    $comando->close();
    return $novo_id;
}


// -----------------------------------------------------
// 3. IMAGENS
// As capas das músicas ficam em imagens/playlists/ (como você organizou).
// As imagens das playlists criadas pelos usuários ficam em imagens/capas_playlists/
// As fotos de perfil ficam em imagens/perfis/
// Se a imagem não existe, aparece o vinil (logo.svg).
// -----------------------------------------------------

// Devolve o endereço da imagem para usar no <img src="...">
function url_imagem($subpasta, $arquivo) {
    if ($arquivo && is_file(PASTA_IMAGENS . $subpasta . '/' . basename($arquivo))) {
        return '../imagens/' . $subpasta . '/' . rawurlencode(basename($arquivo));
    }
    return '../imagens/logo.svg';
}

function url_capa($arquivo)           { return url_imagem('playlists', $arquivo); }
function url_foto_perfil($arquivo)    { return url_imagem('perfis', $arquivo); }
function url_imagem_playlist($arquivo){ return url_imagem('capas_playlists', $arquivo); }

// Recebe uma imagem enviada por formulário (<input type="file" name="...">),
// confere se é mesmo uma imagem e salva na pasta.
//   $campo    = o name do input file
//   $subpasta = pasta dentro de imagens/ (ex: 'perfis')
//   $prefixo  = começo do nome do arquivo salvo
//   $erro     = recebe a mensagem, se algo der errado
// Devolve o nome do arquivo salvo, ou '' se não enviou nada / deu erro.
function salvar_imagem_enviada($campo, $subpasta, $prefixo, &$erro) {
    // Nenhum arquivo escolhido: não é erro, a imagem é opcional
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    $arquivo = $_FILES[$campo];

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Não foi possível enviar a imagem. Tente outra.';
        return '';
    }
    if ($arquivo['size'] > 3 * 1024 * 1024) {
        $erro = 'A imagem pode ter no máximo 3 MB.';
        return '';
    }

    // getimagesize só funciona com imagens de verdade
    $info = @getimagesize($arquivo['tmp_name']);
    $extensoes = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif');
    if (defined('IMAGETYPE_WEBP')) {
        $extensoes[IMAGETYPE_WEBP] = 'webp';
    }
    if (!$info || !isset($extensoes[$info[2]])) {
        $erro = 'Envie uma imagem JPG, PNG, GIF ou WEBP.';
        return '';
    }

    // Cria a pasta se ela ainda não existir
    $pasta = PASTA_IMAGENS . $subpasta . '/';
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    // Nome único para o arquivo não substituir outro
    $nome = $prefixo . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $extensoes[$info[2]];

    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . $nome)) {
        $erro = 'Não foi possível salvar a imagem.';
        return '';
    }
    return $nome;
}

// Apaga uma imagem da pasta (usado ao trocar foto ou excluir playlist)
function apagar_imagem($subpasta, $arquivo) {
    if ($arquivo) {
        $caminho = PASTA_IMAGENS . $subpasta . '/' . basename($arquivo);
        if (is_file($caminho)) {
            unlink($caminho);
        }
    }
}


// -----------------------------------------------------
// 4. MÚSICAS
// -----------------------------------------------------

// Consulta pronta: traz a música junto com o nome do artista.
// Para usar, some um pedaço no final:
//   buscar_todos($conexao, SQL_MUSICAS . ' WHERE m.id = ?', 'i', array($id));
define('SQL_MUSICAS',
    'SELECT m.id, m.titulo, m.album, m.genero, m.capa, m.arquivo_mp3, m.total_reproducoes, a.nome AS artista
     FROM musicas m
     JOIN artistas a ON a.id = m.artista_id');

// Quais gêneros contam como "Rock" no Top 10 Rock.
// Para incluir outro estilo, é só acrescentar aqui (ex: 'Punk').
function generos_rock() {
    return array('Rock', 'Metal', 'Grunge');
}

// Monta o pedaço de SQL: m.genero LIKE '%Rock%' OR m.genero LIKE '%Metal%' ...
function condicao_rock() {
    $partes = array();
    foreach (generos_rock() as $genero) {
        $partes[] = "m.genero LIKE '%" . $genero . "%'";
    }
    return implode(' OR ', $partes);
}

// Endereço que faz o player tocar uma música.
//   $lista = '' (música avulsa), 'curtidas' ou 'playlist' (com $id_lista)
function link_tocar($musica_id, $lista = '', $id_lista = 0) {
    $link = 'player.php?tocar=' . (int) $musica_id;
    if ($lista !== '') {
        $link .= '&lista=' . $lista;
    }
    if ($id_lista > 0) {
        $link .= '&id=' . (int) $id_lista;
    }
    return $link;
}

// Lista (em ordem) os ids das músicas de uma playlist ou das curtidas.
// O player usa isso para saber qual é a próxima música.
// ATENÇÃO: a ordem aqui tem que ser a mesma mostrada em curtidas.php e playlist.php
function ids_da_lista($conexao, $usuario_id, $lista, $id_lista) {
    $linhas = array();

    if ($lista === 'curtidas') {
        $linhas = buscar_todos($conexao,
            'SELECT musica_id FROM curtidas WHERE usuario_id = ? ORDER BY data_curtida DESC, musica_id DESC',
            'i', array($usuario_id));

    } elseif ($lista === 'playlist' && $id_lista > 0) {
        $linhas = buscar_todos($conexao,
            'SELECT musica_id FROM playlist_musicas WHERE playlist_id = ? ORDER BY id',
            'i', array($id_lista));
    }

    $ids = array();
    foreach ($linhas as $linha) {
        $ids[] = (int) $linha['musica_id'];
    }
    return $ids;
}


// -----------------------------------------------------
// 5. CARTÕES E LINHAS (o que aparece na tela)
// Clicar na música manda o endereço para o quadro do player (target="player")
// -----------------------------------------------------

// Cartão quadrado com capa, título e artista (usado nos Top 10 e recentes)
// $posicao > 0 mostra uma etiqueta com o número do ranking
function mostrar_cartao_musica($musica, $lista = '', $id_lista = 0, $posicao = 0) {
    echo '<div class="cartao-musica">';
    if ($posicao > 0) {
        echo '<span class="posicao">' . (int) $posicao . '</span>';
    }
    echo '<a href="' . limpar(link_tocar($musica['id'], $lista, $id_lista)) . '" target="player" title="Tocar">';
    echo '<img src="' . limpar(url_capa($musica['capa'])) . '" alt="Capa de ' . limpar($musica['titulo']) . '">';
    echo '<strong>' . limpar($musica['titulo']) . '</strong>';
    echo '<small>' . limpar($musica['artista']) . '</small>';
    echo '</a></div>';
}

// Linha de lista com capa, título, artista, álbum e ações (usada em busca, playlists, curtidas)
// $extra_html = botões extras no fim da linha (ex: "Remover")
function mostrar_linha_musica($musica, $numero, $lista = '', $id_lista = 0, $extra_html = '') {
    echo '<div class="linha-musica">';
    echo '<span class="linha-numero">' . (int) $numero . '</span>';
    echo '<a class="linha-tocar" href="' . limpar(link_tocar($musica['id'], $lista, $id_lista)) . '" target="player" title="Tocar">';
    echo '<img src="' . limpar(url_capa($musica['capa'])) . '" alt="">';
    echo '<span class="linha-textos"><strong>' . limpar($musica['titulo']) . '</strong>';
    echo '<small>' . limpar($musica['artista']) . '</small></span>';
    echo '</a>';
    echo '<span class="linha-album">' . limpar($musica['album']) . '</span>';
    echo '<div class="linha-acoes">';
    echo '<a href="adicionar_playlist.php?musica=' . (int) $musica['id'] . '" title="Adicionar a uma playlist">+ Playlist</a>';
    echo $extra_html;
    echo '</div>';
    echo '</div>';
}

// Cartão de uma playlist (usado na biblioteca e no perfil)
function mostrar_cartao_playlist($playlist) {
    echo '<div class="cartao-musica">';
    echo '<a href="playlist.php?id=' . (int) $playlist['id'] . '">';
    echo '<img src="' . limpar(url_imagem_playlist($playlist['imagem'])) . '" alt="">';
    echo '<strong>' . limpar($playlist['nome']) . '</strong>';
    if (isset($playlist['total'])) {
        echo '<small>' . texto_musicas($playlist['total']) . '</small>';
    }
    echo '</a></div>';
}

// Cartão de uma pessoa (usado na busca e em "Seguindo")
function mostrar_cartao_pessoa($pessoa) {
    echo '<a class="cartao-pessoa" href="perfil.php?id=' . (int) $pessoa['id'] . '">';
    echo '<img src="' . limpar(url_foto_perfil($pessoa['foto_perfil'])) . '" alt="">';
    echo '<strong>' . limpar($pessoa['nome_exibicao']) . '</strong>';
    echo '<small>Perfil</small>';
    echo '</a>';
}
