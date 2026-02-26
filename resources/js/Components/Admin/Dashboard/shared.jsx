// ─── Card ─────────────────────────────────────────────────────────
// Shared wrapper used by all dashboard panels
export function Card({ title, icon, iconColor = '#6b7280', children, action }) {
  return (
    <div style={{
      background: '#fff', borderRadius: 14,
      border: '1px solid #f0f0f0',
      boxShadow: '0 1px 4px rgba(0,0,0,.06)',
      overflow: 'hidden',
    }}>
      {/* Header */}
      <div style={{
        display: 'flex', alignItems: 'center', justifyContent: 'space-between',
        padding: '16px 18px',
        borderBottom: '1px solid #f3f4f6',
      }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
          {icon && (
            <i className={icon} style={{ fontSize: 14, color: iconColor }} />
          )}
          <h3 style={{ fontSize: 14, fontWeight: 700, color: '#111827', margin: 0 }}>
            {title}
          </h3>
        </div>
        {action}
      </div>

      {/* Body */}
      <div style={{ padding: '14px 18px' }}>
        {children}
      </div>
    </div>
  );
}

// ─── EmptyState ───────────────────────────────────────────────────
export function EmptyState({ message, positive = false }) {
  return (
    <div style={{
      padding: '28px 0', textAlign: 'center',
      color: positive ? '#22c55e' : '#9ca3af',
      fontSize: 13, fontWeight: 500,
    }}>
      <i
        className={`fas ${positive ? 'fa-circle-check' : 'fa-inbox'}`}
        style={{ fontSize: 28, marginBottom: 10, display: 'block', opacity: .5 }}
      />
      {message}
    </div>
  );
}