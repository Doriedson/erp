<script setup lang="ts">
import ExpiringDaysButton from '@/components/ExpiringDaysButton.vue';
import { useExpiringList } from '@/composables/useExpiringData';
import { useExpiringDays } from '@/stores/useExpiringDays';

const emit = defineEmits<{ close: [] }>();

const store = useExpiringDays();
const { data: list, isLoading, isError } = useExpiringList();

function close() {
  emit('close');
}
</script>

<template>
  <dialog open>
    <header style="display:flex;justify-content:space-between;align-items:center">
      <h3>Vencimentos ({{ store.value }} dias)</h3>
      <ExpiringDaysButton mode="local" :initial="store.value" />
    </header>

    <div v-if="isLoading">Carregando…</div>
    <div v-else-if="isError">Erro ao carregar lista.</div>
    <ul v-else>
      <li v-for="item in list?.items" :key="item.id">
        {{ item.name }} — vence em {{ item.days_to_expire }} dia(s)
      </li>
    </ul>

    <footer style="margin-top:1rem;display:flex;justify-content:flex-end;gap:.5rem">
      <button @click="close">Fechar</button>
    </footer>
  </dialog>
</template>
