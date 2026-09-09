import { editionsApi } from '../api/editionsApi.js';
import { el } from '../utils/dom.js';

const EMPTY_HINT = 'Selecione um Card Game primeiro';

/**
 * AC-013, AC-014, AC-015: campo de Edição dependente do Card Game selecionado.
 * - Inicia desabilitado.
 * - Ao trocar o Card Game, busca (fetch) as edições daquele jogo, mostrando loading.
 * - Trocar o Card Game de novo reseta a seleção e recarrega a lista.
 * - Em erro de rede (ASM-005), mostra estado de erro com "tentar novamente" em vez de travar.
 */
export function createEditionSelect({ gameSelect, name = 'edition_id', id = 'edition_id' }) {
  const select = el('select', { id, name, disabled: true, required: true }, [
    el('option', { value: '' }, EMPTY_HINT),
  ]);
  const status = el('p', { class: 'field-hint' }, '');

  let requestToken = 0;

  function setOptions(options, placeholder) {
    select.textContent = '';
    select.append(el('option', { value: '' }, placeholder));
    for (const opt of options) {
      select.append(el('option', { value: opt.id }, opt.name));
    }
  }

  function setStatus(message, isError) {
    status.textContent = '';
    status.classList.toggle('field-hint--error', Boolean(isError));
    if (!message) return;
    status.append(...(Array.isArray(message) ? message : [message]));
  }

  async function loadFor(gameId, { preselect } = {}) {
    const token = ++requestToken;
    select.disabled = true;
    setOptions([], 'Carregando edições…');
    setStatus('');

    try {
      const { editions } = await editionsApi.byGame(gameId);
      if (token !== requestToken) return;
      setOptions(editions, 'Selecione uma Edição');
      select.disabled = false;
      if (preselect) select.value = preselect;
    } catch {
      if (token !== requestToken) return;
      setOptions([], 'Não foi possível carregar as edições');
      const retryButton = el(
        'button',
        { type: 'button', class: 'link-button', onClick: () => loadFor(gameId, { preselect }) },
        'Tentar novamente',
      );
      setStatus(['Falha ao buscar edições. ', retryButton], true);
    }
  }

  function reset() {
    requestToken += 1;
    select.disabled = true;
    setOptions([], EMPTY_HINT);
    setStatus('');
  }

  gameSelect.addEventListener('change', () => {
    const gameId = gameSelect.value;
    select.value = '';
    if (!gameId) {
      reset();
      return;
    }
    loadFor(gameId);
  });

  return { select, status, loadFor, reset };
}
