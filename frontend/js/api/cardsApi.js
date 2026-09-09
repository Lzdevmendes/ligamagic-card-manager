import { request } from './httpClient.js';

export const cardsApi = {
  list: () => request('GET', '/cards'),
  get: (id) => request('GET', `/cards/${id}`),
  create: (data) => request('POST', '/cards', data),
  update: (id, data) => request('PUT', `/cards/${id}`, data),
  remove: (id) => request('DELETE', `/cards/${id}`),
};
