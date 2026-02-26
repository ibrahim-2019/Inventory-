import {
  PieChart, Pie, Cell, Tooltip,
  ResponsiveContainer, Legend,
} from 'recharts';
import { Card, EmptyState } from './shared';

// ─── Palette ──────────────────────────────────────────────────────
const COLORS = ['#3b82f6', '#22c55e', '#f97316', '#a855f7', '#06b6d4', '#f43f5e'];

// ─── WarehouseDonut ───────────────────────────────────────────────
// Props:
//   warehouseStock: Array<{ warehouse: { name }, total_quantity }>
//   — OR —
//   chartData: Array<{ name: string, value: number }>

export function WarehouseDonut({ warehouseStock = [], chartData }) {
  const data = chartData ?? buildDonutData(warehouseStock);

  return (
    <Card title="توزيع المخزون على المخازن" icon="fas fa-chart-pie" iconColor="#a855f7">
      {data.length === 0
        ? <EmptyState message="لا توجد بيانات مخازن" />
        : (
          <div style={{ display: 'flex', alignItems: 'center', gap: 0, height: 220 }}>
            {/* Donut */}
            <div style={{ flex: 1, height: '100%' }}>
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={data}
                    cx="50%" cy="50%"
                    innerRadius="52%" outerRadius="78%"
                    paddingAngle={3}
                    dataKey="value"
                    startAngle={90} endAngle={-270}
                  >
                    {data.map((_, i) => (
                      <Cell key={i} fill={COLORS[i % COLORS.length]} stroke="none" />
                    ))}
                  </Pie>
                  <Tooltip content={<DonutTooltip />} />
                </PieChart>
              </ResponsiveContainer>
            </div>

            {/* Legend */}
            <div style={{ width: 140, flexShrink: 0, display: 'flex', flexDirection: 'column', gap: 8 }}>
              {data.map((entry, i) => {
                const total = data.reduce((s, d) => s + d.value, 0);
                const pct   = total > 0 ? Math.round((entry.value / total) * 100) : 0;
                return (
                  <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                    <div style={{
                      width: 10, height: 10, borderRadius: 3, flexShrink: 0,
                      background: COLORS[i % COLORS.length],
                    }} />
                    <div style={{ minWidth: 0 }}>
                      <p style={{
                        fontSize: 11, fontWeight: 600, color: '#374151',
                        whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
                      }}>
                        {entry.name}
                      </p>
                      <p style={{ fontSize: 10, color: '#9ca3af' }}>{pct}% · {entry.value}</p>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )
      }
    </Card>
  );
}

// ─── Custom Tooltip ───────────────────────────────────────────────
function DonutTooltip({ active, payload }) {
  if (!active || !payload?.length) return null;
  const { name, value } = payload[0];
  return (
    <div style={{
      background: '#1f2937', borderRadius: 10, padding: '8px 14px',
      boxShadow: '0 4px 20px rgba(0,0,0,.2)', direction: 'rtl',
    }}>
      <p style={{ color: '#f9fafb', fontSize: 12 }}>
        {name}: <strong>{value}</strong>
      </p>
    </div>
  );
}

// ─── Helper ───────────────────────────────────────────────────────
function buildDonutData(warehouseStock) {
  return warehouseStock
    .map(s => ({
      name:  s.warehouse?.name ?? 'غير محدد',
      value: Number(s.total_quantity ?? 0),
    }))
    .filter(d => d.value > 0);
}