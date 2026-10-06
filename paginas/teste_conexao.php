<?php
// Página só para TESTAR se tudo está funcionando.
// Abra: http://localhost/vinyl/paginas/teste_conexao.php
require_once '../configuracao/conexao.php';

echo '<h2>Conexão OK!</h2>';

// Busca todas as músicas junto com o nome do artista
$consulta  = 'SELECT musicas.titulo, musicas.album, artistas.nome AS artista
              FROM musicas
              JOIN artistas ON artistas.id = musicas.artista_id';
$resultado = $conexao->query($consulta);

// Percorre cada linha do resultado
while ($musica = $resultado->fetch_assoc()) {
    echo $musica['titulo'] . ' - ' . $musica['artista'] . ' (' . $musica['album'] . ')' . '<br>';
}
