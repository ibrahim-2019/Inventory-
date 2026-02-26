import { useState, useEffect } from 'react';

// ─── FlashBanner ──────────────────────────────────────────────────
export function FlashBanner({ flash }) {
  const [visible, setVisible] = useState(true);

  // Reset visibility whenever a new flash message arrives
  useEffect(() => { setVisible(true); }, [flash]);

  if (!visible || (!flash?.success && !flash?.error)) return null;

  const isSuccess = !!flash.success;
  const message   = flash.success ?? flash.error;

  return (
    <div className="flash-msg" style={{
      margin: '12px 24px 0', padding: '12px 16px', borderRadius: 10,
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      background: isSuccess ? '#f0fdf4' : '#fef2f2',
      border: `1px solid ${isSuccess ? '#bbf7d0' : '#fecaca'}`,
      color:  isSuccess ? '#166534'  : '#991b1b',
      fontSize: 13, fontWeight: 500,
    }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
        <i
          className={`fas ${isSuccess ? 'fa-circle-check' : 'fa-circle-exclamation'}`}
          style={{ fontSize: 15, color: isSuccess ? '#22c55e' : '#ef4444' }}
        />
        {message}
      </div>

      <button
        onClick={() => setVisible(false)}
        style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'inherit', opacity: .6, fontSize: 14 }}
      >
        <i className="fas fa-xmark" />
      </button>
    </div>
  );
}