// resources/js/lib/axios.ts
import axios from 'axios';

export const api = axios.create({
  headers: { Accept: 'application/json' }, // expectsJson() を確実に
  withCredentials: true,
});

api.defaults.xsrfCookieName = 'XSRF-TOKEN';
api.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

// （任意）トースト連携
type ToastLevel = 'error' | 'info';
type ToastPayload = { message: string; level: ToastLevel };
const listeners: ((p: ToastPayload) => void)[] = [];
export const onToast = (fn: (p: ToastPayload) => void) => listeners.push(fn);
const toast = (m: string, level: ToastLevel = 'error') =>
  listeners.forEach((fn) => fn({ message: m, level }));

api.interceptors.response.use(
  (r) => r,
  (err) => {
    const res = err?.response;
    // ネットワーク等で response が無い時
    if (!res) {
      toast('ネットワークエラー');
      // 正規化して投げる
      throw { status: 0, payload: null, __rateLimited: false, response: null, original: err };
    }

    const status = res.status;
    const payload = res.data ?? {};
    const is429 = status === 429;

    // 429 はUI側でクールダウン表示したいので、トーストは出さずに正規化して投げる
    if (is429) {
      throw { status, payload, __rateLimited: true, response: res, original: err };
    }

    // それ以外（503含む）はトースト表示もしつつ正規化して投げる
    const msg = payload?.message ?? 'エラーが発生しました';
    toast(msg, 'error');
    throw { status, payload, __rateLimited: false, response: res, original: err };
  }
);
