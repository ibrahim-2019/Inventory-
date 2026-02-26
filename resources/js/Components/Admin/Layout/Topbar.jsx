import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import { UserMenu } from './UserMenu';
import { ROUTE_NAMES } from './adminStyles';

// ─── Topbar ───────────────────────────────────────────────────────
export function Topbar({ onToggleSidebar, notifCount = 0 }) {
  const { auth } = usePage().props;

  return (
    <header style={{
      background: 'var(--topbar-bg)',
      borderBottom: '1px solid #e5e7eb',
      padding: '0 24px', height: 60,
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      boxShadow: 'var(--shadow-sm)', zIndex: 20, flexShrink: 0,
    }}>
      {/* Hamburger */}
      <IconBtn onClick={onToggleSidebar} icon="fas fa-bars" />

      {/* Page title */}
      <PageTitle />

      {/* Actions */}
      <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
        <NotifBtn count={notifCount} />
        <UserMenu name={auth.user?.name} />
      </div>
    </header>
  );
}

// ─── PageTitle ────────────────────────────────────────────────────
function PageTitle() {
  const { url } = usePage();
  const title = ROUTE_NAMES[url] ?? 'الصفحة';
  return (
    <div style={{ color: '#111827', fontWeight: 700, fontSize: 15 }}>{title}</div>
  );
}

// ─── NotifBtn ─────────────────────────────────────────────────────
function NotifBtn({ count }) {
  return (
    <IconBtn icon="fas fa-bell">
      {count > 0 && (
        <span className="badge-pulse" style={{
          position: 'absolute', top: 5, right: 5,
          width: 8, height: 8, borderRadius: 99,
          background: '#ef4444', border: '1.5px solid #fff',
        }} />
      )}
    </IconBtn>
  );
}

// ─── Reusable icon button ─────────────────────────────────────────
function IconBtn({ icon, onClick, children }) {
  return (
    <button
      onClick={onClick}
      style={{
        width: 36, height: 36, border: 'none', cursor: 'pointer',
        background: '#f3f4f6', borderRadius: 8, position: 'relative',
        display: 'flex', alignItems: 'center', justifyContent: 'center',
        color: '#374151', transition: 'background .15s',
      }}
      onMouseEnter={e => e.currentTarget.style.background = '#e5e7eb'}
      onMouseLeave={e => e.currentTarget.style.background = '#f3f4f6'}
    >
      <i className={icon} style={{ fontSize: 14 }} />
      {children}
    </button>
  );
}