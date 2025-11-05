<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import { useExpiringDays } from '@/stores/useExpiringDays';
import { useUpdateGlobalPreference } from '@/composables/useExpiringData';

const props = defineProps<{
  mode?: 'global' | 'local';
  initial?: number | null;
}>();

const store = useExpiringDays();
const updateGlobal = useUpdateGlobalPreference();

const open = ref(false);
const temp = ref<number>(props.initial ?? store.value);
watchEffect(() => {
  if (props.initial != null) temp.value = props.initial;
});

function clamp(n: number, min: number, max: number) {
  return Math.max(min, Math.min(max, n));
}

async function save() {
  const next = clamp(temp.value, 1, 365);
  if (props.mode === 'local') {
    store.setLocal(next);
  } else {
    await updateGlobal(next);
  }
  open.value = false;
}
</script>

<template>
  <div>
    <button @click="open = true">Vencimento: {{ store.value }} dias</button>
    <dialog v-if="open">
      <form @submit.prevent="save">
        <h3>Ajustar dias de vencimento</h3>
        <input type="number" v-model.number="temp" min="1" max="365" />
        <div style="display:flex; gap:.5rem; margin-top:.75rem">
          <button type="submit">Salvar</button>
          <button type="button" @click="open=false">Cancelar</button>
        </div>
        <p style="font-size:.85rem; opacity:.8; margin-top:.5rem">
          Aplicando: <strong>{{ props.mode ?? 'global' }}</strong>
        </p>
      </form>
    </dialog>
  </div>
</template>
