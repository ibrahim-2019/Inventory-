import {
  BarChart, Bar, XAxis, YAxis, CartesianGrid,
  Tooltip, Legend, ResponsiveContainer,
} from 'recharts';
import { Card, EmptyState } from './shared';

// ─── MovementsChart ───────────────────────────────────────────────
// Props:
//   movements: Array<{ movement_type: 'in'|'out', quantity, created_at }>
//
// Derives daily aggregated data from the raw movements array.
// If your backend already sends aggregated data, pass it via `chartData` directly.

export function MovementsChart({ movements = [], chartData }) {
  const data = chartData ?? buildChartData(movements);

  return (
    <Card title="حركات المخزون — آخر 7 أيام" icon="fas fa-chart-bar" iconColor="#3b82f6">
      {data.length === 0
        ? <EmptyState message="لا توجد حركات لعرضها" />
        : (
          <div style={{ height: 240, marginTop: 8 }}>
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={data} barSize={14} barGap={4}
                margin={{ top: 4, right: 8, left: -20, bottom: 0 }}>

                <CartesianGrid strokeDasharray="3 3" stroke="#f3f4f6" vertical={false} />

                <XAxis
                  dataKey="day"
                  tick={{ fontSize: 11, fill: '#9ca3af', fontFamily: 'Cairo, sans-serif' }}
                  axisLine={false} tickLine={false}
                />
                <YAxis
                  tick={{ fontSize: 11, fill: '#9ca3af', fontFamily: 'Cairo, sans-serif' }}
                  axisLine={false} tickLine={false}
                  allowDecimals={false}
                />

                <Tooltip content={<CustomTooltip />} />

                <Legend
                  wrapperStyle={{ fontSize: 12, fontFamily: 'Cairo, sans-serif', paddingTop: 12 }}
                  formatter={(value) => value === 'in' ? 'وارد' : 'صادر'}
                />

                <Bar dataKey="in"  name="in"  fill="#3b82f6" radius={[4, 4, 0, 0]} />
                <Bar dataKey="out" name="out" fill="#f87171" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        )
      }
    </Card>
  );
}

// ─── Custom Tooltip ───────────────────────────────────────────────
function CustomTooltip({ active, payload, label }) {
  if (!active || !payload?.length) return null;
  return (
    <div style={{
      background: '#1f2937', borderRadius: 10, padding: '10px 14px',
      boxShadow: '0 4px 20px rgba(0,0,0,.2)', direction: 'rtl',
    }}>
      <p style={{ color: '#9ca3af', fontSize: 11, marginBottom: 6 }}>{label}</p>
      {payload.map(p => (
        <div key={p.dataKey} style={{ display: 'flex', alignItems: 'center', gap: 6, marginBottom: 3 }}>
          <div style={{ width: 8, height: 8, borderRadius: 99, background: p.fill }} />
          <span style={{ color: '#f9fafb', fontSize: 12 }}>
            {p.dataKey === 'in' ? 'وارد' : 'صادر'}: <strong>{p.value}</strong>
          </span>
        </div>
      ))}
    </div>
  );
}

// ─── Helpers ──────────────────────────────────────────────────────
function buildChartData(movements) {
  // Build last 7 days map
  const days = {};
  for (let i = 6; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    const key = d.toLocaleDateString('ar-EG', { weekday: 'short' });
    days[d.toISOString().slice(0, 10)] = { day: key, in: 0, out: 0 };
  }

  movements.forEach(m => {
    const date = m.created_at?.slice(0, 10);
    if (days[date]) {
      days[date][m.movement_type] += Number(m.quantity ?? 0);
    }
  });

  return Object.values(days);
}