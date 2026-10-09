-- DADOS DE DEMONSTRAÇÃO (Encher Linguiça)

UPDATE musicas
SET total_reproducoes = FLOOR(1000 + RAND() * 9000)
WHERE id > 0;
