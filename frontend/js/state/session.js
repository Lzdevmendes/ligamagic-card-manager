let currentUser = null;
const subscribers = new Set();

function notify() {
  for (const fn of subscribers) fn(currentUser);
}

export const session = {
  get user() {
    return currentUser;
  },
  isAuthenticated() {
    return currentUser !== null;
  },
  setUser(user) {
    currentUser = user;
    notify();
  },
  clear() {
    currentUser = null;
    notify();
  },
  subscribe(fn) {
    subscribers.add(fn);
    return () => subscribers.delete(fn);
  },
};
