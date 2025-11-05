import { useQuery, useQueryClient } from '@tanstack/vue-query';
import { useExpiringDays } from '@/stores/useExpiringDays';

type PreferenceResponse = { key: string; value: number };
type ExpiringListResponse = { items: Array<{ id: number | string; name: string; days_to_expire: number }> };
type DashboardCountersResponse = { totals: Record<string, number> };

async function http<T>(input: RequestInfo, init?: RequestInit): Promise<T> {
  const res = await fetch(input, {
    headers: { 'Content-Type': 'application/json' },
    ...init,
  });
  if (!res.ok) {
    throw new Error(`HTTP ${res.status}`);
  }
  return res.json() as Promise<T>;
}

export function useLoadExpiringPreference() {
  const store = useExpiringDays();
  return useQuery({
    queryKey: ['pref', 'expiring_days'],
    queryFn: async () => {
      const data = await http<PreferenceResponse>('/api/me/preferences/expiring_days');
      const v = Number(data?.value ?? 30);
      store.setGlobal(v);
      store.markLoaded();
      return v;
    },
    staleTime: 300_000,
  });
}

export function useUpdateGlobalPreference() {
  const qc = useQueryClient();
  const store = useExpiringDays();
  return async (value: number) => {
    store.setGlobal(value);
    try {
      await http('/api/me/preferences/expiring_days', {
        method: 'PUT',
        body: JSON.stringify({ value }),
      });
      qc.invalidateQueries({ queryKey: ['expiringList'] });
      qc.invalidateQueries({ queryKey: ['dashboardCounters'] });
    } catch (e) {
      await qc.invalidateQueries({ queryKey: ['pref', 'expiring_days'] });
      throw e;
    }
  };
}

export function useExpiringList() {
  const store = useExpiringDays();
  return useQuery({
    queryKey: ['expiringList', store.value],
    queryFn: () => http<ExpiringListResponse>(`/api/products/expiring?within_days=${store.value}`),
    keepPreviousData: true,
  });
}

export function useDashboardCounters() {
  const store = useExpiringDays();
  return useQuery({
    queryKey: ['dashboardCounters', store.value],
    queryFn: () => http<DashboardCountersResponse>(`/api/dashboard/counters?within_days=${store.value}`),
    keepPreviousData: true,
  });
}
