import { usePage } from '@inertiajs/react';
import { SIDEBAR_W_OPEN, SIDEBAR_W_CLOSE } from './adminStyles';
import { NavLink, NavDivider, NavSection } from './NavLink';

// ─── NAV CONFIG ───────────────────────────────────────────────────
// Add or remove routes here — no need to touch the JSX below
const NAV_ITEMS = [
  { href: '/admin/dashboard',             icon: 'fas fa-gauge-high',         label: 'لوحة التحكم' },
  { href: '/admin/products',              icon: 'fas fa-box-open',            label: 'المنتجات' },
  { section: 'المخزون' },
  { href: '/admin/stock/in',              icon: 'fas fa-download',  label: 'إضافة وارد' },
  { href: '/admin/stock/out',             icon: 'fas fa-upload',  label: 'صرف مخزون' },
  { href: '/admin/warehouses',            icon: 'fas fa-warehouse',           label: 'المخازن' },
  { section: 'التقارير' },
  { href: '/admin/reports/current-stock', icon: 'fas fa-chart-column',        label: 'المخزون الحالي' },
  { href: '/admin/reports/expiry',        icon: 'fas fa-calendar-xmark',      label: 'تقرير الصلاحية' },
];

// ─── Sidebar ──────────────────────────────────────────────────────
export function Sidebar({ open, isMobile, onClose }) {
  const { auth } = usePage().props;
  const collapsed = !open && !isMobile;
  const W = isMobile ? 260 : (open ? SIDEBAR_W_OPEN : SIDEBAR_W_CLOSE);

  return (
    <>
      {/* Overlay backdrop — mobile only */}
      {isMobile && open && (
        <div className="sidebar-overlay" onClick={onClose} />
      )}

      <aside
        className={isMobile ? 'sidebar-mobile' : ''}
        style={{
          width: W,
          minWidth: isMobile ? 'unset' : W,
          background: 'var(--sidebar-bg)',
          borderLeft: '1px solid var(--sidebar-border)',
          display: (isMobile && !open) ? 'none' : 'flex',
          flexDirection: 'column',
          transition: isMobile ? 'none' : `width var(--transition), min-width var(--transition)`,
          overflow: 'hidden',
          zIndex: 30,
          flexShrink: 0,
        }}
      >
        {/* ── Logo ── */}
        <div style={{
          display: 'flex', alignItems: 'center',
          padding: '16px 16px 14px',
          justifyContent: 'space-between',
          borderBottom: '1px solid var(--sidebar-border)',
          flexShrink: 0,
        }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
            <div style={{
              width: 36, height: 36, borderRadius: 10, flexShrink: 0,
              background: 'linear-gradient(135deg,#3b82f6,#6366f1)',
              display: 'flex', alignItems: 'center', justifyContent: 'center',
              boxShadow: '0 4px 14px rgba(59,130,246,.4)',
            }}>
              <i className="fas fa-boxes" style={{ color: '#fff', fontSize: 15 }} />
            </div>

            {!collapsed && (
              <div>
                <div style={{ color: '#f9fafb', fontWeight: 700, fontSize: 15, lineHeight: 1.2 }}>
                  نظام المخزون
                </div>
                <div style={{ color: '#6b7280', fontSize: 11, marginTop: 1 }}>
                  لوحة الإدارة
                </div>
              </div>
            )}
          </div>

          {/* ✕ close — mobile only */}
          {isMobile && (
            <button onClick={onClose} style={{
              background: 'rgba(255,255,255,.06)', border: 'none', cursor: 'pointer',
              width: 30, height: 30, borderRadius: 8, color: '#9ca3af',
              display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0,
            }}>
              <i className="fas fa-xmark" style={{ fontSize: 14 }} />
            </button>
          )}
        </div>

        {/* ── Nav ── */}
        <nav style={{ flex: 1, overflowY: 'auto', overflowX: 'hidden', padding: '10px 0' }}>
          {NAV_ITEMS.map((item, i) => {
            if (item.section) {
              return collapsed
                ? <NavDivider key={i} />
                : <NavSection key={i}>{item.section}</NavSection>;
            }
            return (
              <NavLink
                key={item.href}
                href={item.href}
                icon={item.icon}
                collapsed={collapsed}
                onNavigate={isMobile ? onClose : undefined}
              >
                {item.label}
              </NavLink>
            );
          })}
        </nav>

        {/* ── User strip ── */}
        <div style={{
          borderTop: '1px solid var(--sidebar-border)',
          padding: collapsed ? '14px 0' : '14px 16px',
          display: 'flex',
          alignItems: 'center',
          justifyContent: collapsed ? 'center' : 'flex-start',
          gap: 10, flexShrink: 0,
        }}>
          <div style={{
            width: 34, height: 34, borderRadius: 99, flexShrink: 0,
            background: 'linear-gradient(135deg,#6366f1,#8b5cf6)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: 13, color: '#fff', fontWeight: 700,
          }}>
            {auth.user?.name?.[0] ?? 'U'}
          </div>
          {!collapsed && (
            <div style={{ overflow: 'hidden' }}>
              <div style={{
                color: '#e5e7eb', fontSize: 13, fontWeight: 600,
                whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
              }}>
                {auth.user?.name}
              </div>
              <div style={{ color: '#6b7280', fontSize: 11 }}>مدير النظام</div>
            </div>
          )}
        </div>
      </aside>
    </>
  );
}