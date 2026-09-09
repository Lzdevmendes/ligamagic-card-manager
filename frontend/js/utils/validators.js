export const RARITIES = ['common', 'uncommon', 'rare', 'mythic', 'secret'];

export const RARITY_LABELS = {
  common: 'Comum',
  uncommon: 'Incomum',
  rare: 'Rara',
  mythic: 'Mítica',
  secret: 'Secreta',
};

export const CARD_GAMES = [
  { id: 'magic', name: 'Magic: The Gathering' },
  { id: 'pokemon', name: 'Pokémon' },
  { id: 'yugioh', name: 'Yu-Gi-Oh!' },
];

export function cardGameName(id) {
  return CARD_GAMES.find((g) => g.id === id)?.name ?? id;
}

/**
 * Validação client-side do formulário de carta, espelhando as regras do backend (AC-008).
 * @returns {Record<string,string>} mapa de campo => mensagem de erro (vazio se válido)
 */
export function validateCardForm(data) {
  const errors = {};

  if (!data.name_en || data.name_en.trim() === '') {
    errors.name_en = 'Nome da carta em inglês é obrigatório.';
  }

  if (!data.card_game || !CARD_GAMES.some((g) => g.id === data.card_game)) {
    errors.card_game = 'Selecione um Card Game.';
  }

  if (!data.edition_id || data.edition_id.trim() === '') {
    errors.edition_id = 'Selecione uma Edição.';
  }

  if (!data.rarity || !RARITIES.includes(data.rarity)) {
    errors.rarity = 'Selecione uma Raridade.';
  }

  return errors;
}
