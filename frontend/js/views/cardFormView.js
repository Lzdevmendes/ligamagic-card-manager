import { cardsApi } from '../api/cardsApi.js';
import { ApiError } from '../api/httpClient.js';
import { createEditionSelect } from '../components/editionSelect.js';
import { showToast } from '../components/toast.js';
import { CARD_GAMES, RARITIES, RARITY_LABELS, validateCardForm } from '../utils/validators.js';
import { el, mount } from '../utils/dom.js';
import { navigate } from '../router.js';

function fieldError(fieldsWrap, name) {
  return fieldsWrap.querySelector(`[data-error-for="${name}"]`);
}

/** AC-007, AC-008, AC-009, AC-010, AC-013, AC-014, AC-015: formulário de carta. */
export async function renderCardFormView(root, { id } = {}) {
  const isEdit = Boolean(id);

  const nameEnInput = el('input', { type: 'text', id: 'name_en', name: 'name_en', required: true });
  const namePtInput = el('input', { type: 'text', id: 'name_pt', name: 'name_pt' });

  const gameSelect = el('select', { id: 'card_game', name: 'card_game', required: true }, [
    el('option', { value: '' }, 'Selecione…'),
    ...CARD_GAMES.map((g) => el('option', { value: g.id }, g.name)),
  ]);

  const editionField = createEditionSelect({ gameSelect });

  const raritySelect = el('select', { id: 'rarity', name: 'rarity', required: true }, [
    el('option', { value: '' }, 'Selecione…'),
    ...RARITIES.map((r) => el('option', { value: r }, RARITY_LABELS[r])),
  ]);

  const imageUrlInput = el('input', { type: 'url', id: 'image_url', name: 'image_url', placeholder: 'https://…' });

  const submitButton = el('button', { type: 'submit', class: 'button button--primary' }, isEdit ? 'Salvar alterações' : 'Cadastrar carta');
  const formError = el('p', { class: 'form-error', role: 'alert', hidden: true }, '');

  function field(labelText, control, name, hint) {
    return el('div', { class: 'field' }, [
      el('label', { for: control.id }, labelText),
      control,
      hint ? hint : null,
      el('p', { class: 'field-error', 'data-error-for': name }, ''),
    ]);
  }

  const form = el('form', { class: 'card-form', novalidate: true }, [
    field('Nome em inglês *', nameEnInput, 'name_en'),
    field('Nome em português', namePtInput, 'name_pt'),
    field('Card Game *', gameSelect, 'card_game'),
    field('Edição *', editionField.select, 'edition_id', editionField.status),
    field('Raridade *', raritySelect, 'rarity'),
    field('URL da imagem', imageUrlInput, 'image_url'),
    formError,
    el('div', { class: 'form-actions' }, [
      el('button', { type: 'button', class: 'button button--ghost', onClick: () => navigate('/cards') }, 'Cancelar'),
      submitButton,
    ]),
  ]);

  function clearFieldErrors() {
    form.querySelectorAll('.field-error').forEach((n) => { n.textContent = ''; });
    formError.hidden = true;
  }

  function showFieldErrors(fields) {
    for (const [name, message] of Object.entries(fields)) {
      const target = fieldError(form, name);
      if (target) target.textContent = message;
    }
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearFieldErrors();

    const data = {
      name_en: nameEnInput.value,
      name_pt: namePtInput.value || null,
      card_game: gameSelect.value,
      edition_id: editionField.select.value,
      rarity: raritySelect.value,
      image_url: imageUrlInput.value || null,
    };

    const clientErrors = validateCardForm(data);
    if (Object.keys(clientErrors).length > 0) {
      showFieldErrors(clientErrors);
      return;
    }

    submitButton.disabled = true;
    submitButton.textContent = 'Salvando…';

    try {
      if (isEdit) {
        await cardsApi.update(id, data);
        showToast('Carta atualizada com sucesso.');
      } else {
        await cardsApi.create(data);
        showToast('Carta cadastrada com sucesso.');
      }
      navigate('/cards');
    } catch (err) {
      if (err instanceof ApiError && err.status === 422) {
        showFieldErrors(err.fields);
        formError.textContent = err.message;
        formError.hidden = false;
      } else {
        formError.textContent = err instanceof ApiError ? err.message : 'Erro ao salvar a carta.';
        formError.hidden = false;
      }
    } finally {
      submitButton.disabled = false;
      submitButton.textContent = isEdit ? 'Salvar alterações' : 'Cadastrar carta';
    }
  });

  const page = el('div', { class: 'page' }, [
    el('header', { class: 'page-header' }, el('h1', {}, isEdit ? 'Editar carta' : 'Nova carta')),
    form,
  ]);

  mount(root, page);
  nameEnInput.focus();

  if (isEdit) {
    submitButton.disabled = true;
    try {
      const { card } = await cardsApi.get(id);
      nameEnInput.value = card.name_en;
      namePtInput.value = card.name_pt ?? '';
      gameSelect.value = card.card_game;
      raritySelect.value = card.rarity;
      imageUrlInput.value = card.image_url ?? '';
      await editionField.loadFor(card.card_game, { preselect: card.edition_id });
    } catch (err) {
      formError.textContent = err instanceof ApiError ? err.message : 'Não foi possível carregar a carta.';
      formError.hidden = false;
    } finally {
      submitButton.disabled = false;
    }
  }
}
