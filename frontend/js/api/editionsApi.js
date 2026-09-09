import { request } from './httpClient.js';

export const editionsApi = {
  byGame: (gameId) => request('GET', `/editions?game=${encodeURIComponent(gameId)}`),
};
