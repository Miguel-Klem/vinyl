-- BD do VINYL

CREATE DATABASE IF NOT EXISTS vinyl
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE vinyl;

-- USUÁRIOS:
CREATE TABLE IF NOT EXISTS usuarios (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  email            VARCHAR(150) NOT NULL UNIQUE,
  senha            VARCHAR(255) NOT NULL,
  nome_exibicao    VARCHAR(60)  NOT NULL,
  data_nascimento  DATE         NOT NULL,
  foto_perfil      VARCHAR(255) NULL,
  data_cadastro    DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ARTISTAS:
CREATE TABLE IF NOT EXISTS artistas (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL
);

-- MÚSICAS:
CREATE TABLE IF NOT EXISTS musicas (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  titulo            VARCHAR(150) NOT NULL,
  artista_id        INT NOT NULL,
  genero            VARCHAR(50)  NULL,        
  album             VARCHAR(150) NULL,        
  capa              VARCHAR(255) NULL,        
  arquivo_mp3       VARCHAR(255) NOT NULL,    
  total_reproducoes INT NOT NULL DEFAULT 0,   
  FOREIGN KEY (artista_id) REFERENCES artistas(id)
);

-- PLAYLISTS:
CREATE TABLE IF NOT EXISTS playlists (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id    INT NOT NULL,
  nome          VARCHAR(100) NOT NULL,
  imagem        VARCHAR(255) NULL,
  data_criacao  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Quais músicas estão em cada playlist:
CREATE TABLE IF NOT EXISTS playlist_musicas (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  playlist_id   INT NOT NULL,
  musica_id     INT NOT NULL,
  data_adicao   DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (playlist_id, musica_id),
  FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
  FOREIGN KEY (musica_id)   REFERENCES musicas(id)   ON DELETE CASCADE
);

-- CURTIDAS/VINIL:
CREATE TABLE IF NOT EXISTS curtidas (
  usuario_id    INT NOT NULL,
  musica_id     INT NOT NULL,
  data_curtida  DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (usuario_id, musica_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (musica_id)   REFERENCES musicas(id)   ON DELETE CASCADE
);

-- SEGUIDORES:
CREATE TABLE IF NOT EXISTS seguidores (
  seguidor_id  INT NOT NULL,
  seguido_id   INT NOT NULL,
  PRIMARY KEY (seguidor_id, seguido_id),
  FOREIGN KEY (seguidor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (seguido_id)  REFERENCES usuarios(id) ON DELETE CASCADE
);

-- HISTÓRICO:
CREATE TABLE IF NOT EXISTS historico (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id   INT NOT NULL,
  musica_id    INT NOT NULL,
  data_ouvida  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (musica_id)   REFERENCES musicas(id)   ON DELETE CASCADE
);