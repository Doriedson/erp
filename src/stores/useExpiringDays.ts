import { defineStore } from 'pinia';

type Scope = 'global' | 'local';

export const useExpiringDays = defineStore('expiringDays', {
  state: () => ({
    value: 30 as number,
    scope: 'global' as Scope,
    loaded: false as boolean,
  }),
  actions: {
    setLocal(value: number) {
      const v = clamp(value, 1, 365);
      this.value = v;
      this.scope = 'local';
    },
    setGlobal(value: number) {
      const v = clamp(value, 1, 365);
      this.value = v;
      this.scope = 'global';
    },
    markLoaded() {
      this.loaded = true;
    },
  },
});

function clamp(n: number, min: number, max: number) {
  return Math.max(min, Math.min(max, n));
}
