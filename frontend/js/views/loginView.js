import { authApi } from '../api/authApi.js';
import { ApiError } from '../api/httpClient.js';
import { session } from '../state/session.js';
import { el, mount } from '../utils/dom.js';
import { navigate } from '../router.js';

/** AC-001, AC-002: tela de login. */
export function renderLoginView(root) {
  const errorMessage = el('p', { class: 'form-error', role: 'alert', hidden: true }, '');

  const emailInput = el('input', {
    type: 'email', id: 'email', name: 'email', autocomplete: 'username', required: true,
  });
  const passwordInput = el('input', {
    type: 'password', id: 'password', name: 'password', autocomplete: 'current-password', required: true,
  });
  const submitButton = el('button', { type: 'submit', class: 'button button--primary' }, 'Entrar');

  const form = el('form', { class: 'auth-form', novalidate: true }, [
    el('div', { class: 'field' }, [el('label', { for: 'email' }, 'E-mail'), emailInput]),
    el('div', { class: 'field' }, [el('label', { for: 'password' }, 'Senha'), passwordInput]),
    errorMessage,
    submitButton,
  ]);

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    errorMessage.hidden = true;
    submitButton.disabled = true;
    submitButton.textContent = 'Entrando…';

    try {
      const { user } = await authApi.login(emailInput.value, passwordInput.value);
      session.setUser(user);
      navigate('/cards');
    } catch (err) {
      const message = err instanceof ApiError ? err.message : 'Não foi possível entrar. Tente novamente.';
      errorMessage.textContent = message;
      errorMessage.hidden = false;
      passwordInput.value = '';
      passwordInput.focus();
    } finally {
      submitButton.disabled = false;
      submitButton.textContent = 'Entrar';
    }
  });

  const page = el('div', { class: 'auth-page' }, [
    el('div', { class: 'auth-card' }, [
      el('h1', { class: 'auth-title' }, 'Portal de Cartas'),
      el('p', { class: 'auth-subtitle' }, 'Entre com suas credenciais de administrador.'),
      form,
    ]),
  ]);

  mount(root, page);
  emailInput.focus();
}
