import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { VueQueryPlugin, QueryClient, type VueQueryPluginOptions } from '@tanstack/vue-query';
import App from './App.vue';
import router from './router'; // se existir

const pinia = createPinia();

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 30_000,
      gcTime: 300_000,
      retry: 2,
      refetchOnWindowFocus: false,
      refetchOnReconnect: true,
    },
  },
});

const vueQueryOptions: VueQueryPluginOptions = { queryClient };

createApp(App)
  .use(router)
  .use(pinia)
  .use(VueQueryPlugin, vueQueryOptions)
  .mount('#app');
