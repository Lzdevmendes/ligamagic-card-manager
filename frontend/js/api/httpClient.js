import { API_BASE_URL } from '../config.js';

export class ApiError extends Error {
  constructor(status, message, fields = {}) {
    super(message);
    this.status = status;
    this.fields = fields;
  }
}

/**
 * Cliente HTTP fino sobre fetch. `credentials: 'include'` é obrigatório aqui porque
 * frontend e API estão em origens (portas) diferentes — sem isso o cookie de sessão
 * nunca seria enviado de volta pelo navegador.
 */
export async function request(method, path, body) {
  let response;

  try {
    response = await fetch(`${API_BASE_URL}${path}`, {
      method,
      credentials: 'include',
      headers: body !== undefined ? { 'Content-Type': 'application/json' } : {},
      body: body !== undefined ? JSON.stringify(body) : undefined,
    });
  } catch {
    throw new ApiError(0, 'Não foi possível conectar à API. Verifique sua conexão e tente novamente.');
  }

  let payload = null;
  try {
    payload = await response.json();
  } catch {
    payload = null;
  }

  if (!response.ok) {
    const message = payload?.error?.message ?? 'Ocorreu um erro inesperado.';
    const fields = payload?.error?.fields ?? {};
    throw new ApiError(response.status, message, fields);
  }

  return payload;
}
