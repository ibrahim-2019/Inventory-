import { Card, EmptyState } from "./shared";
// ─── RecentMovements ──────────────────────────────────────────────
// Props: movements: Array<{ id, product: { name }, warehouse: { name }, movement_type: 'in'|'out', quantity, created_at? }>

export function RecentMovements({ movements = [] }) {
  return (
    <Card title="آخر الحركات" icon="fas fa-clock-rotate-left" iconColor="#6366f1">
      {movements.length === 0
        ? <EmptyState message="لا توجد حركات مخزون بعد" />
        : movements.map(m => <MovementRow key={m.id} movement={m} />)
      }
    </Card>
  );
}

function MovementRow({ movement }) {
  const isIn    = movement.movement_type === 'in';
  const color   = isIn ? '#22c55e' : '#ef4444';
  const bgColor = isIn ? '#f0fdf4' : '#fef2f2';
  const label   = isIn ? 'وارد'   : 'صادر';

  return (
    <div style={{
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      padding: '10px 12px', borderRadius: 10, background: '#fafafa',
      border: '1px solid #f3f4f6', marginBottom: 8,
    }}>
      {/* Left: type badge */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
        <div style={{
          width: 32, height: 32, borderRadius: 8, flexShrink: 0,
          background: bgColor,
          display: 'flex', alignItems: 'center', justifyContent: 'center',
        }}>
          <i
            className={`fas fa-${isIn ? 'download' : 'upload'}`}
            style={{ fontSize: 12, color }}
          />
        </div>
        <div>
          <p style={{ fontSize: 13, fontWeight: 600, color: '#111827', marginBottom: 2 }}>
            {movement.product?.name}
          </p>
          <p style={{ fontSize: 11, color: '#9ca3af' }}>
            {movement.warehouse?.name}
          </p>
        </div>
      </div>

      {/* Right: quantity + badge */}
      <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end', gap: 4 }}>
        <span style={{ fontSize: 15, fontWeight: 800, color }}>
          {isIn ? '+' : '−'}{movement.quantity}
        </span>
        <span style={{
          fontSize: 10, fontWeight: 700, padding: '2px 7px', borderRadius: 99,
          background: bgColor, color, border: `1px solid ${color}22`,
        }}>
          {label}
        </span>
      </div>
    </div>
  );
}