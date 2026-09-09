import { el, mount, clear } from '../utils/dom.js';

function getRoot() {
  let root = document.getElementById('modal-root');
  if (!root) {
    root = el('div', { id: 'modal-root' });
    document.body.append(root);
  }
  return root;
}

/**
 * AC-011, AC-012: modal de confirmação. Resolve `true` se confirmado, `false` se cancelado.
 */
export function confirmDialog({ title, message, confirmLabel = 'Confirmar', cancelLabel = 'Cancelar' }) {
  return new Promise((resolve) => {
    const root = getRoot();

    function close(result) {
      clear(root);
      document.removeEventListener('keydown', onKeydown);
      resolve(result);
    }

    function onKeydown(e) {
      if (e.key === 'Escape') close(false);
    }

    const dialog = el('div', { class: 'modal-dialog', role: 'alertdialog', 'aria-modal': 'true' }, [
      el('h2', { class: 'modal-title' }, title),
      el('p', { class: 'modal-message' }, message),
      el('div', { class: 'modal-actions' }, [
        el('button', { type: 'button', class: 'button button--ghost', onClick: () => close(false) }, cancelLabel),
        el('button', { type: 'button', class: 'button button--danger', onClick: () => close(true) }, confirmLabel),
      ]),
    ]);

    const overlay = el('div', { class: 'modal-overlay', onClick: (e) => { if (e.target === overlay) close(false); } }, dialog);

    document.addEventListener('keydown', onKeydown);
    mount(root, overlay);
  });
}
