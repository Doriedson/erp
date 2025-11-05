<script setup lang="ts">
import { useLoadExpiringPreference, useExpiringList, useDashboardCounters } from '@/composables/useExpiringData';
import ExpiringDaysButton from '@/components/ExpiringDaysButton.vue';
import { useExpiringDays } from '@/stores/useExpiringDays';

const { data: prefLoaded } = useLoadExpiringPreference();
const { data: list, isLoading: listLoading, isError: listError } = useExpiringList();
const { data: counters, isLoading: cntLoading, isError: cntError } = useDashboardCounters();
const store = useExpiringDays();
</script>

<template>
  <section>
    <header style="display:flex;justify-content:space-between;align-items:center">
      <h2>Produtos a vencer ({{ store.value }} dias)</h2>
      <ExpiringDaysButton mode="global" />
    </header>

    <div v-if="listLoading || cntLoading">Carregando…</div>
    <div v-else-if="listError || cntError">Erro ao carregar dados.</div>
    <div v-else>
      <div style="margin:.75rem 0">
        <strong>Contadores</strong>
        <pre>{{ counters?.totals }}</pre>
      </div>
      <ul>
        <li v-for="item in list?.items" :key="item.id">
          {{ item.name }} — vence em {{ item.days_to_expire }} dia(s)
        </li>
      </ul>
    </div>
  </section>
</template>
