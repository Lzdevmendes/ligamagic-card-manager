import { authApi } from './api/authApi.js';
import { session } from './state/session.js';
import { addRoute, navigate, setNotFound, start } from './router.js';
import { renderLoginView } from './views/loginView.js';
import { renderCardsListView } from './views/cardsListView.js';
import { renderCardFormView } from './views/cardFormView.js';
import { mount, el } from './utils/dom.js';

const root = document.getElementById('app');

function requireGuest(render) {
  return (params) => {
    if (session.isAuthenticated()) {
      navigate('/cards');
      return;
    }
    render(params);
  };
}

function requireAuth(render) {
  return (params) => {
    if (!session.isAuthenticated()) {
      navigate('/login');
      return;
    }
    render(params);
  };
}

addRoute('/login', requireGuest(() => renderLoginView(root)));
addRoute('/cards', requireAuth(() => renderCardsListView(root)));
addRoute('/cards/new', requireAuth(() => renderCardFormView(root)));
addRoute('/cards/:id/edit', requireAuth(({ id }) => renderCardFormView(root, { id })));
addRoute('/', () => navigate(session.isAuthenticated() ? '/cards' : '/login'));

setNotFound(() => mount(root, el('div', { class: 'page' }, el('p', {}, 'Página não encontrada.'))));

async function bootstrap() {
  mount(root, el('p', { class: 'loading loading--full' }, 'Carregando…'));

  try {
    const { user } = await authApi.me();
    session.setUser(user);
  } catch {
    session.clear();
  }

  start();
}

bootstrap();
