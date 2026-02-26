// ─── StatCard ─────────────────────────────────────────────────────
// Props:
//   title   : string
//   value   : number | string
//   icon    : FA class string  e.g. "fas fa-box"
//   color   : "blue" | "green" | "red" | "orange"
//   trend   : { value: number, label: string }  (optional)

const PALETTE = {
  blue:   { bg: '#eff6ff', icon: '#3b82f6', border: '#bfdbfe' },
  green:  { bg: '#f0fdf4', icon: '#22c55e', border: '#bbf7d0' },
  red:    { bg: '#fef2f2', icon: '#ef4444', border: '#fecaca' },
  orange: { bg: '#fff7ed', icon: '#f97316', border: '#fed7aa' },
};

export function StatCard({ title, value, icon, color = 'blue', trend }) {
  const p = PALETTE[color] ?? PALETTE.blue;
  const isPositiveTrend = trend?.value >= 0;

  return (
    <div style={{
      background: '#fff', borderRadius: 14,
      border: '1px solid #f0f0f0',
      boxShadow: '0 1px 4px rgba(0,0,0,.06)',
      padding: '20px 22px',
      display: 'flex', flexDirection: 'column', gap: 14,
      transition: 'box-shadow .2s, transform .2s',
    }}
      onMouseEnter={e => {
        e.currentTarget.style.boxShadow = '0 6px 20px rgba(0,0,0,.09)';
        e.currentTarget.style.transform = 'translateY(-2px)';
      }}
      onMouseLeave={e => {
        e.currentTarget.style.boxShadow = '0 1px 4px rgba(0,0,0,.06)';
        e.currentTarget.style.transform = 'translateY(0)';
      }}
    >
      {/* Top row */}
      <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between' }}>
        <div>
          <p style={{ fontSize: 12, color: '#6b7280', fontWeight: 600, marginBottom: 6, letterSpacing: '.02em' }}>
            {title}
          </p>
          <p style={{ fontSize: 28, fontWeight: 800, color: '#111827', lineHeight: 1 }}>
            {value ?? '—'}
          </p>
        </div>

        {/* Icon badge */}
        <div style={{
          width: 44, height: 44, borderRadius: 12, flexShrink: 0,
          background: p.bg, border: `1px solid ${p.border}`,
          display: 'flex', alignItems: 'center', justifyContent: 'center',
        }}>
          <i className={icon} style={{ fontSize: 18, color: p.icon }} />
        </div>
      </div>

      {/* Trend row (optional) */}
      {trend && (
        <div style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
          <i
            className={`fas fa-arrow-${isPositiveTrend ? 'up' : 'down'}`}
            style={{ fontSize: 10, color: isPositiveTrend ? '#22c55e' : '#ef4444' }}
          />
          <span style={{ fontSize: 12, color: isPositiveTrend ? '#22c55e' : '#ef4444', fontWeight: 600 }}>
            {Math.abs(trend.value)}%
          </span>
          <span style={{ fontSize: 12, color: '#9ca3af' }}>{trend.label}</span>
        </div>
      )}
    </div>
  );
}