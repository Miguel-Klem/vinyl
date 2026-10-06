-- MÚSICAS DO VINYL (rode depois do vinyl.sql)
-- Para adicionar mais músicas, copie uma linha e troque os dados.

USE vinyl;

-- ARTISTAS
INSERT INTO artistas (nome) VALUES ('Korn');
INSERT INTO artistas (nome) VALUES ('Metallica');
INSERT INTO artistas (nome) VALUES ('Limp Bizkit');
INSERT INTO artistas (nome) VALUES ('Slipknot');
INSERT INTO artistas (nome) VALUES ('Type O Negative');
INSERT INTO artistas (nome) VALUES ('Guns N'' Roses');
INSERT INTO artistas (nome) VALUES ('Gorillaz');
INSERT INTO artistas (nome) VALUES ('Nirvana');
INSERT INTO artistas (nome) VALUES ('AC/DC');
INSERT INTO artistas (nome) VALUES ('Radiohead');
INSERT INTO artistas (nome) VALUES ('Black Sabbath');
INSERT INTO artistas (nome) VALUES ('KISS');
INSERT INTO artistas (nome) VALUES ('Pink Floyd');
INSERT INTO artistas (nome) VALUES ('Queen');
INSERT INTO artistas (nome) VALUES ('Ozzy Osbourne');

-- MÚSICAS:
-- ordem: titulo, artista, genero, album, capa, arquivo_mp3
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Freak On a Leash', (SELECT id FROM artistas WHERE nome = 'Korn'), 'Nu Metal', 'Follow the Leader', 'follow_the_leader.jpg', 'freak_on_a_leash.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Blind', (SELECT id FROM artistas WHERE nome = 'Korn'), 'Nu Metal', 'Korn', 'korn.jpg', 'blind.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Master of Puppets', (SELECT id FROM artistas WHERE nome = 'Metallica'), 'Thrash Metal', 'Master of Puppets', 'master_of_puppets.jpg', 'master_of_puppets.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Enter Sandman', (SELECT id FROM artistas WHERE nome = 'Metallica'), 'Heavy Metal', 'Metallica', 'metallica.jpg', 'enter_sandman.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Nothing Else Matters', (SELECT id FROM artistas WHERE nome = 'Metallica'), 'Heavy Metal', 'Metallica', 'metallica.jpg', 'nothing_else_matters.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('For Whom The Bell Tolls', (SELECT id FROM artistas WHERE nome = 'Metallica'), 'Thrash Metal', 'Ride the Lightning', 'ride_the_lightning.jpg', 'for_whom_the_bell_tolls.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Seek and Destroy', (SELECT id FROM artistas WHERE nome = 'Metallica'), 'Thrash Metal', 'Kill ''Em All', 'kill_em_all.jpg', 'seek_and_destroy.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Hot Dog', (SELECT id FROM artistas WHERE nome = 'Limp Bizkit'), 'Nu Metal', 'Chocolate Starfish and the Hot Dog Flavored Water', 'chocolate_starfish_and_the_hot_dog_flavored_water.jpg', 'hot_dog.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Spit It Out', (SELECT id FROM artistas WHERE nome = 'Slipknot'), 'Nu Metal', 'Slipknot', 'slipknot.jpg', 'spit_it_out.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('I Dont Wanna Be Me', (SELECT id FROM artistas WHERE nome = 'Type O Negative'), 'Gothic Metal', 'Life Is Killing Me', 'life_is_killing_me.jpg', 'i_dont_wanna_be_me.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Sweet Child O'' Mine', (SELECT id FROM artistas WHERE nome = 'Guns N'' Roses'), 'Hard Rock', 'Appetite for Destruction', 'appetite_for_destruction.jpg', 'sweet_child_o_mine.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Clint Eastwood', (SELECT id FROM artistas WHERE nome = 'Gorillaz'), 'Alternative Hip Hop', 'Gorillaz', 'gorillaz.jpg', 'clint_eastwood.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Feel Good Inc', (SELECT id FROM artistas WHERE nome = 'Gorillaz'), 'Alternative Rock', 'Demon Days', 'demon_days.jpg', 'feel_good_inc.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Duality', (SELECT id FROM artistas WHERE nome = 'Slipknot'), 'Nu Metal', 'Vol. 3: (The Subliminal Verses)', 'vol_3_the_subliminal_verses.jpg', 'duality.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Psychosocial', (SELECT id FROM artistas WHERE nome = 'Slipknot'), 'Nu Metal', 'All Hope Is Gone', 'all_hope_is_gone.jpg', 'psychosocial.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Snuff', (SELECT id FROM artistas WHERE nome = 'Slipknot'), 'Alternative Rock', 'All Hope Is Gone', 'all_hope_is_gone.jpg', 'snuff.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Before I Forget', (SELECT id FROM artistas WHERE nome = 'Slipknot'), 'Nu Metal', 'Vol. 3: (The Subliminal Verses)', 'vol_3_the_subliminal_verses.jpg', 'before_i_forget.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Wait and Bleed', (SELECT id FROM artistas WHERE nome = 'Slipknot'), 'Nu Metal', 'Slipknot', 'slipknot.jpg', 'wait_and_bleed.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Come As You Are', (SELECT id FROM artistas WHERE nome = 'Nirvana'), 'Grunge', 'Nevermind', 'nevermind.jpg', 'come_as_you_are.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Highway to Hell', (SELECT id FROM artistas WHERE nome = 'AC/DC'), 'Hard Rock', 'Highway to Hell', 'highway_to_hell.jpg', 'highway_to_hell.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('TNT', (SELECT id FROM artistas WHERE nome = 'AC/DC'), 'Hard Rock', 'T.N.T.', 't_n_t.jpg', 'tnt.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Welcome To The Jungle', (SELECT id FROM artistas WHERE nome = 'Guns N'' Roses'), 'Hard Rock', 'Appetite for Destruction', 'appetite_for_destruction.jpg', 'welcome_to_the_jungle.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Creep', (SELECT id FROM artistas WHERE nome = 'Radiohead'), 'Alternative Rock', 'Pablo Honey', 'pablo_honey.jpg', 'creep.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Thunderstruck', (SELECT id FROM artistas WHERE nome = 'AC/DC'), 'Hard Rock', 'The Razors Edge', 'the_razors_edge.jpg', 'thunderstruck.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Paranoid', (SELECT id FROM artistas WHERE nome = 'Black Sabbath'), 'Heavy Metal', 'Paranoid', 'paranoid.jpg', 'paranoid.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('I Was Made For Lovin You', (SELECT id FROM artistas WHERE nome = 'KISS'), 'Hard Rock / Disco Rock', 'Dynasty', 'dynasty.jpg', 'i_was_made_for_lovin_you.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Iron Man', (SELECT id FROM artistas WHERE nome = 'Black Sabbath'), 'Heavy Metal', 'Paranoid', 'paranoid.jpg', 'iron_man.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Money', (SELECT id FROM artistas WHERE nome = 'Pink Floyd'), 'Progressive Rock', 'The Dark Side of the Moon', 'the_dark_side_of_the_moon.jpg', 'money.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Smells Like Teen Spirit', (SELECT id FROM artistas WHERE nome = 'Nirvana'), 'Grunge', 'Nevermind', 'nevermind.jpg', 'smells_like_teen_spirit.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Bohemian Rhapsody', (SELECT id FROM artistas WHERE nome = 'Queen'), 'Progressive Rock / Hard Rock', 'A Night at the Opera', 'a_night_at_the_opera.jpg', 'bohemian_rhapsody.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Back In Black', (SELECT id FROM artistas WHERE nome = 'AC/DC'), 'Hard Rock', 'Back in Black', 'back_in_black.jpg', 'back_in_black.mp3');
INSERT INTO musicas (titulo, artista_id, genero, album, capa, arquivo_mp3) VALUES ('Crazy Train', (SELECT id FROM artistas WHERE nome = 'Ozzy Osbourne'), 'Heavy Metal', 'Blizzard of Ozz', 'blizzard_of_ozz.jpg', 'crazy_train.mp3');
