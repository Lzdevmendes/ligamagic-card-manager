import { el } from '../utils/dom.js';
import { cardGameName, RARITY_LABELS } from '../utils/validators.js';

/** AC-006: lista as cartas com nome em inglês, jogo, edição e raridade. */
export function createCardTable(cards, editionNameById, { onEdit, onDelete }) {
  if (cards.length === 0) {
    return el('div', { class: 'empty-state' }, [
      el('p', {}, 'Nenhuma carta cadastrada ainda.'),
      el('p', { class: 'empty-state__hint' }, 'Use "Nova carta" para começar o acervo.'),
    ]);
  }

  const rows = cards.map((card) =>
    el('tr', {}, [
      el('td', { class: 'cell-thumb' }, card.image_url
        ? el('img', { src: card.image_url, alt: card.name_en, class: 'card-thumb' })
        : el('span', { class: 'card-thumb card-thumb--placeholder' }, '🂠')),
      el('td', {}, [
        el('div', { class: 'cell-title' }, card.name_en),
        card.name_pt ? el('div', { class: 'cell-subtitle' }, card.name_pt) : null,
      ]),
      el('td', {}, cardGameName(card.card_game)),
      el('td', {}, editionNameById.get(card.edition_id) ?? card.edition_id),
      el('td', {}, el('span', { class: `badge badge--${card.rarity}` }, RARITY_LABELS[card.rarity] ?? card.rarity)),
      el('td', { class: 'cell-actions' }, [
        el('button', { type: 'button', class: 'button button--small', onClick: () => onEdit(card) }, 'Editar'),
        el('button', { type: 'button', class: 'button button--small button--danger', onClick: () => onDelete(card) }, 'Excluir'),
      ]),
    ]),
  );

  return el('table', { class: 'card-table' }, [
    el('thead', {}, el('tr', {}, [
      el('th', {}, ''),
      el('th', {}, 'Nome'),
      el('th', {}, 'Card Game'),
      el('th', {}, 'Edição'),
      el('th', {}, 'Raridade'),
      el('th', {}, 'Ações'),
    ])),
    el('tbody', {}, rows),
  ]);
}
