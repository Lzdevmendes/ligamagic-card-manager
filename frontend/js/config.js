// Frontend (porta 8080) e API (porta 8091) rodam em origens diferentes de propósito
// (ver README) — o backend precisa resolver o host dinamicamente em vez de hardcode.
const API_PORT = 8091;

export const API_BASE_URL = `${location.protocol}//${location.hostname}:${API_PORT}/api`;
