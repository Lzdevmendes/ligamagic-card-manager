const routes = [];
let notFoundHandler = () => {};

function compile(pattern) {
  const paramNames = [];
  const regex = pattern.replace(/:(\w+)/g, (_, name) => {
    paramNames.push(name);
    return '([^/]+)';
  });

  return { regex: new RegExp(`^${regex}$`), paramNames };
}

export function addRoute(pattern, handler) {
  routes.push({ ...compile(pattern), handler });
}

export function setNotFound(handler) {
  notFoundHandler = handler;
}

export function navigate(path) {
  location.hash = path;
}

function currentPath() {
  const hash = location.hash.replace(/^#/, '');
  return hash === '' ? '/' : hash;
}

function render() {
  const path = currentPath();

  for (const route of routes) {
    const match = path.match(route.regex);
    if (match) {
      const params = {};
      route.paramNames.forEach((name, i) => {
        params[name] = match[i + 1];
      });
      route.handler(params);
      return;
    }
  }

  notFoundHandler();
}

export function start() {
  window.addEventListener('hashchange', render);
  render();
}
