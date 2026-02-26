import { Card, EmptyState } from "./shared";
// ─── LowStockTable ────────────────────────────────────────────────
// Props: products: Array<{ id, product: { name }, warehouse: { name }, available_quantity, min_quantity? }>

export function LowStockTable({ products = [] }) {
  return (
    <Card title="منتجات منخفضة المخزون" icon="fas fa-exclamation-triangle" iconColor="#ef4444">
      {products.length === 0
        ? <EmptyState message="لا توجد منتجات منخفضة المخزون 🎉" positive />
        : products.map(s => <StockRow key={s.id} stock={s} />)
      }
    </Card>
  );
}

function StockRow({ stock }) {
  // Determine severity
  const qty      = stock.available_quantity ?? 0;
  const minQty   = stock.min_quantity ?? 10;
  const pct      = Math.min((qty / minQty) * 100, 100);
  const severity = qty === 0 ? 'critical' : pct < 50 ? 'low' : 'ok';

  const colors = {
    critical: { bar: '#ef4444', badge: '#fef2f2', text: '#ef4444', label: 'نفد' },
    low:      { bar: '#f97316', badge: '#fff7ed', text: '#f97316', label: 'منخفض' },
    ok:       { bar: '#22c55e', badge: '#f0fdf4', text: '#22c55e', label: 'مقبول' },
  };
  const c = colors[severity];

  return (
    <div style={{
      padding: '10px 12px', borderRadius: 10,
      border: '1px solid #f3f4f6', marginBottom: 8, background: '#fafafa',
    }}>
      {/* Top row */}
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 8 }}>
        <div>
          <p style={{ fontSize: 13, fontWeight: 600, color: '#111827', marginBottom: 2 }}>
            {stock.product?.name}
          </p>
          <p style={{ fontSize: 11, color: '#9ca3af' }}>{stock.warehouse?.name}</p>
        </div>
        <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end', gap: 4 }}>
          <span style={{ fontSize: 16, fontWeight: 800, color: c.text }}>{qty}</span>
          <span style={{
            fontSize: 10, fontWeight: 700, padding: '2px 7px', borderRadius: 99,
            background: c.badge, color: c.text,
          }}>
            {c.label}
          </span>
        </div>
      </div>

      {/* Progress bar */}
      <div style={{ height: 4, background: '#e5e7eb', borderRadius: 99, overflow: 'hidden' }}>
        <div style={{
          height: '100%', width: `${pct}%`,
          background: c.bar, borderRadius: 99,
          transition: 'width .6s ease',
        }} />
      </div>
    </div>
  );
}