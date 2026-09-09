-- Schema: Portal de Cartas (LigaMagic Card Manager)
-- Executado automaticamente pelo MySQL via docker-entrypoint-initdb.d

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS card_games (
  id VARCHAR(20) PRIMARY KEY,
  name VARCHAR(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS editions (
  id VARCHAR(20) PRIMARY KEY,
  card_game_id VARCHAR(20) NOT NULL,
  name VARCHAR(120) NOT NULL,
  CONSTRAINT fk_editions_card_game FOREIGN KEY (card_game_id) REFERENCES card_games(id),
  KEY idx_editions_card_game (card_game_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cards (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name_en VARCHAR(200) NOT NULL,
  name_pt VARCHAR(200) NULL,
  card_game_id VARCHAR(20) NOT NULL,
  edition_id VARCHAR(20) NOT NULL,
  rarity VARCHAR(20) NOT NULL,
  image_url VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_cards_card_game FOREIGN KEY (card_game_id) REFERENCES card_games(id),
  CONSTRAINT fk_cards_edition FOREIGN KEY (edition_id) REFERENCES editions(id),
  KEY idx_cards_card_game (card_game_id),
  KEY idx_cards_edition (edition_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
