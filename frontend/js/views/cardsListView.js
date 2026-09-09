import { authApi } from '../api/authApi.js';
import { cardsApi } from '../api/cardsApi.js';
import { editionsApi } from '../api/editionsApi.js';
import { ApiError } from '../api/httpClient.js';
import { createCardTable } from '../components/cardTable.js';
import { confirmDialog } from '../components/modal.js';
import { showToast } from '../components/toast.js';
import { session } from '../state/session.js';
import { CARD_GAMES } from '../utils/validators.js';
import { el, mount } from '../utils/dom.js';
import { navigate } from '../router.js';

async function loadEditionNameMap() {
  const map = new Map();
  // allSettled: uma falha isolada ao buscar edições de UM jogo não pode
  // impedir a listagem de exibir as cartas dos outros jogos.
  const results = await Promise.allSettled(CARD_GAMES.map((g) => editionsApi.byGame(g.id)));
  for (const result of results) {
    if (result.status !== 'fulfilled') continue;
    for (const edition of result.value.editions) map.set(edition.id, edition.name);
  }
  return map;
}

/** AC-006, AC-011, AC-012: listagem de cartas com exclusão. */
export async function renderCardsListView(root) {
  const tableContainer = el('div', { class: 'table-container' }, el('p', { class: 'loading' }, 'Carregando cartas…'));

  const page = el('div', { class: 'page' }, [
    el('header', { class: 'page-header' }, [
      el('div', {}, [
        el('h1', {}, 'Cartas'),
        el('p', { class: 'page-subtitle' }, `Sessão de ${session.user?.name ?? ''}`),
      ]),
      el('div', { class: 'page-header__actions' }, [
        el('button', { type: 'button', class: 'button button--primary', onClick: () => navigate('/cards/new') }, '+ Nova carta'),
        el('button', {
          type: 'button',
          class: 'button button--ghost',
          onClick: async () => {
            await authApi.logout().catch(() => {});
            session.clear();
            navigate('/login');
          },
        }, 'Sair'),
      ]),
    ]),
    tableContainer,
  ]);

  mount(root, page);

  async function refresh() {
    tableContainer.replaceChildren(el('p', { class: 'loading' }, 'Carregando cartas…'));

    try {
      const [{ cards }, editionNameById] = await Promise.all([cardsApi.list(), loadEditionNameMap()]);

      const table = createCardTable(cards, editionNameById, {
        onEdit: (card) => navigate(`/cards/${card.id}/edit`),
        onDelete: async (card) => {
          const confirmed = await confirmDialog({
            title: 'Excluir carta',
            message: `Tem certeza que deseja excluir "${card.name_en}"? Essa ação não pode ser desfeita.`,
            confirmLabel: 'Excluir',
          });

          if (!confirmed) return;

          try {
            await cardsApi.remove(card.id);
            showToast('Carta excluída com sucesso.');
            refresh();
          } catch (err) {
            showToast(err instanceof ApiError ? err.message : 'Erro ao excluir carta.', { type: 'error' });
          }
        },
      });

      tableContainer.replaceChildren(table);
    } catch (err) {
      const message = err instanceof ApiError ? err.message : 'Erro ao carregar cartas.';
      tableContainer.replaceChildren(el('p', { class: 'form-error' }, message));
    }
  }

  await refresh();
}
