-- Seed: massa de dados inicial do Portal de Cartas
-- Executado automaticamente pelo MySQL via docker-entrypoint-initdb.d (após schema.sql)

-- Sem isso, o cliente `mysql` do entrypoint usa seu charset padrão (nem sempre
-- utf8mb4) para interpretar este arquivo, corrompendo os acentos ao inserir.
SET NAMES utf8mb4;

-- Usuário administrador de teste.
-- email: admin@ligamagic.local
-- senha: H9m9fz0MVa5S!8O  (hash bcrypt abaixo, gerado com password_hash())
INSERT INTO users (name, email, password_hash) VALUES
  ('Administrador', 'admin@ligamagic.local', '$2y$10$cnbHU8z1u.FKSh04yZQQdOkr7NTh3VfJeaJoZBc4ZJPXuf1JnT2KW');

INSERT INTO card_games (id, name) VALUES
  ('magic', 'Magic: The Gathering'),
  ('pokemon', 'Pokémon'),
  ('yugioh', 'Yu-Gi-Oh!');

-- Edições verbatim do JSON fornecido no desafio.
INSERT INTO editions (id, card_game_id, name) VALUES
  ('dom', 'magic', 'Dominaria'),
  ('war', 'magic', 'War of the Spark'),
  ('eld', 'magic', 'Throne of Eldraine'),
  ('hob', 'magic', 'The Hobbit'),
  ('msh', 'magic', 'Marvel Super Heroes'),
  ('base1', 'pokemon', 'Base Set'),
  ('swsh1', 'pokemon', 'Sword & Shield'),
  ('sv1', 'pokemon', 'Scarlet & Violet'),
  ('30c', 'pokemon', '30th Celebration'),
  ('cri', 'pokemon', 'Chaos Rising'),
  ('lob', 'yugioh', 'Legend of Blue Eyes White Dragon'),
  ('mrd', 'yugioh', 'Metal Raiders'),
  ('sdy', 'yugioh', 'Starter Deck: Yugi'),
  ('rotd', 'yugioh', 'Rise of the Duelist'),
  ('blzd', 'yugioh', 'Blazing Dominion');

-- Cartas de amostra, para a listagem não nascer vazia.
INSERT INTO cards (name_en, name_pt, card_game_id, edition_id, rarity, image_url) VALUES
  ('Teferi, Hero of Dominaria', 'Teferi, Herói de Dominaria', 'magic', 'dom', 'mythic', 'https://cards.scryfall.io/large/front/3/2/32345d2e-e546-4324-96c1-3f78d4dc0e05.jpg'),
  ('Nicol Bolas, Dragon-God', NULL, 'magic', 'war', 'mythic', 'https://cards.scryfall.io/large/front/f/5/f5c92392-98e5-45fa-a144-3b8f342bd6ba.jpg'),
  ('The Rogue Prince of Wisdom', NULL, 'magic', 'hob', 'rare', NULL),
  ('Pikachu', 'Pikachu', 'pokemon', 'base1', 'common', 'https://images.pokemontcg.io/base1/58_hires.png'),
  ('Charizard', NULL, 'pokemon', 'base1', 'rare', 'https://images.pokemontcg.io/base1/4_hires.png'),
  ('Miraidon ex', NULL, 'pokemon', 'sv1', 'secret', NULL),
  ('Blue-Eyes White Dragon', 'Dragão Branco de Olhos Azuis', 'yugioh', 'lob', 'uncommon', 'https://images.ygoprodeck.com/images/cards/89631139.jpg'),
  ('Dark Magician', NULL, 'yugioh', 'sdy', 'rare', 'https://images.ygoprodeck.com/images/cards/46986414.jpg');
