import { Card, EmptyState } from "./shared";
// ─── ExpiringBatches ──────────────────────────────────────────────
// Props: batches: Array<{ id, product: { name }, warehouse: { name }, expiry_date, quantity }>

export function ExpiringBatches({ batches = [] }) {
  return (
    <Card title="قريبة من انتهاء الصلاحية" icon="fas fa-calendar-times" iconColor="#f97316">
      {batches.length === 0
        ? <EmptyState message="لا توجد منتجات قريبة من الانتهاء 🎉" positive />
        : batches.map(b => <BatchRow key={b.id} batch={b} />)
      }
    </Card>
  );
}

function BatchRow({ batch }) {
  const daysLeft = batch.expiry_date
    ? Math.ceil((new Date(batch.expiry_date) - new Date()) / 86_400_000)
    : null;

  const urgency =
    daysLeft === null ? 'unknown'
    : daysLeft <= 0   ? 'expired'
    : daysLeft <= 7   ? 'critical'
    : daysLeft <= 30  ? 'warning'
    : 'ok';

  const colors = {
    expired:  { text: '#ef4444', bg: '#fef2f2', label: 'منتهي' },
    critical: { text: '#ef4444', bg: '#fef2f2', label: `${daysLeft} يوم` },
    warning:  { text: '#f97316', bg: '#fff7ed', label: `${daysLeft} يوم` },
    ok:       { text: '#22c55e', bg: '#f0fdf4', label: `${daysLeft} يوم` },
    unknown:  { text: '#9ca3af', bg: '#f9fafb', label: 'غير محدد' },
  };
  const c = colors[urgency];

  return (
    <div style={{
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      padding: '10px 12px', borderRadius: 10,
      border: '1px solid #f3f4f6', marginBottom: 8, background: '#fafafa',
    }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
        {/* Urgency dot */}
        <div style={{
          width: 8, height: 8, borderRadius: 99, flexShrink: 0,
          background: c.text,
          boxShadow: urgency === 'critical' || urgency === 'expired'
            ? `0 0 0 3px ${c.bg}` : 'none',
        }} />
        <div>
          <p style={{ fontSize: 13, fontWeight: 600, color: '#111827', marginBottom: 2 }}>
            {batch.product?.name}
          </p>
          <p style={{ fontSize: 11, color: '#9ca3af' }}>
            {batch.warehouse?.name}
            {batch.expiry_date && (
              <> &nbsp;·&nbsp; {new Date(batch.expiry_date).toLocaleDateString('ar-EG')}</>
            )}
          </p>
        </div>
      </div>

      <span style={{
        fontSize: 11, fontWeight: 700, padding: '3px 9px', borderRadius: 99,
        background: c.bg, color: c.text,
      }}>
        {c.label}
      </span>
    </div>
  );
}