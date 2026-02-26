import { Link } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';

// ─── UserMenu ─────────────────────────────────────────────────────
export function UserMenu({ name }) {
  const [open, setOpen]       = useState(false);
  const [pos, setPos]         = useState({ top: 0, left: 0 });
  const btnRef                = useRef();
  const wrapRef               = useRef();

  // Close on outside click
  useEffect(() => {
    function onDoc(e) {
      if (wrapRef.current && !wrapRef.current.contains(e.target)) setOpen(false);
    }
    document.addEventListener('mousedown', onDoc);
    return () => document.removeEventListener('mousedown', onDoc);
  }, []);

  function handleToggle() {
    if (!open && btnRef.current) {
      const rect = btnRef.current.getBoundingClientRect();
      setPos({
        top:  rect.bottom + 6,
        left: Math.max(8, rect.left - (192 - rect.width)),
      });
    }
    setOpen(v => !v);
  }

  const initial = name?.[0] ?? 'U';

  return (
    <div ref={wrapRef}>
      {/* Trigger button */}
      <button ref={btnRef} onClick={handleToggle} style={btnStyle}>
        <Avatar initial={initial} size={28} />
        <span className="user-name-text" style={{ fontSize: 13, color: '#374151', fontWeight: 500 }}>
          {name}
        </span>
        <i className={`fas fa-chevron-${open ? 'up' : 'down'}`} style={{ fontSize: 10, color: '#9ca3af' }} />
      </button>

      {/* Dropdown — fixed position so it never clips on mobile */}
      {open && (
        <div className="dropdown-menu" style={{ ...dropStyle, top: pos.top, left: pos.left }}>
          <DropItem href="/profile"        icon="fas fa-user-circle">الملف الشخصي</DropItem>
          <DropItem href="/admin/settings" icon="fas fa-gear">الإعدادات</DropItem>
          <hr style={{ border: 'none', borderTop: '1px solid #f3f4f6', margin: '4px 0' }} />
          <LogoutBtn />
        </div>
      )}
    </div>
  );
}

// ─── Sub-components ───────────────────────────────────────────────
function Avatar({ initial, size = 28 }) {
  return (
    <div style={{
      width: size, height: size, borderRadius: 99, flexShrink: 0,
      background: 'linear-gradient(135deg,#6366f1,#8b5cf6)',
      display: 'flex', alignItems: 'center', justifyContent: 'center',
      fontSize: size * .43, color: '#fff', fontWeight: 700,
    }}>
      {initial}
    </div>
  );
}

function DropItem({ href, icon, children }) {
  return (
    <Link href={href} style={dropItemStyle}
      onMouseEnter={e => e.currentTarget.style.background = '#f9fafb'}
      onMouseLeave={e => e.currentTarget.style.background = 'transparent'}
    >
      <i className={icon} style={{ width: 16, textAlign: 'center', color: '#9ca3af' }} />
      {children}
    </Link>
  );
}

function LogoutBtn() {
  return (
    <Link href="/logout" method="post" as="button" style={{ ...dropItemStyle, color: '#ef4444', width: '100%', border: 'none' }}
      onMouseEnter={e => e.currentTarget.style.background = '#fef2f2'}
      onMouseLeave={e => e.currentTarget.style.background = 'transparent'}
    >
      <i className="fas fa-right-from-bracket" style={{ width: 16, textAlign: 'center' }} />
      تسجيل الخروج
    </Link>
  );
}

// ─── Styles ───────────────────────────────────────────────────────
const btnStyle = {
  display: 'flex', alignItems: 'center', gap: 8,
  background: 'transparent', border: '1px solid #e5e7eb',
  borderRadius: 8, padding: '4px 10px 4px 6px',
  cursor: 'pointer', transition: 'background .15s',
  fontFamily: 'var(--font-ar)',
};

const dropStyle = {
  position: 'fixed', width: 192,
  background: '#fff', borderRadius: 10,
  boxShadow: '0 8px 30px rgba(0,0,0,.15)', border: '1px solid #f0f0f0',
  overflow: 'hidden', zIndex: 999,
};

const dropItemStyle = {
  display: 'flex', alignItems: 'center', gap: 8,
  padding: '10px 14px', color: '#374151', fontSize: 13,
  textDecoration: 'none', background: 'transparent',
  cursor: 'pointer', transition: 'background .15s',
  fontFamily: 'var(--font-ar)',
};