import { el } from '../utils/dom.js';

function getRoot() {
  let root = document.getElementById('toast-root');
  if (!root) {
    root = el('div', { id: 'toast-root', class: 'toast-root', role: 'status', 'aria-live': 'polite' });
    document.body.append(root);
  }
  return root;
}

export function showToast(message, { type = 'success', duration = 4000 } = {}) {
  const root = getRoot();
  const toast = el('div', { class: `toast toast--${type}` }, message);
  root.append(toast);

  requestAnimationFrame(() => toast.classList.add('toast--visible'));

  setTimeout(() => {
    toast.classList.remove('toast--visible');
    setTimeout(() => toast.remove(), 200);
  }, duration);
}
