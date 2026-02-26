import { Link, usePage } from '@inertiajs/react';

// ─── NavLink ──────────────────────────────────────────────────────
export function NavLink({ href, icon, children, collapsed, onNavigate }) {
  const { url } = usePage();
  const isActive = url.startsWith(href);

  return (
    <Link
      href={href}
      className={`nav-link${isActive ? ' active' : ''}`}
      onClick={onNavigate}
    >
      <i className={`${icon} icon`} />
      {!collapsed && (
        <span style={{ fontSize: 13, fontWeight: isActive ? 600 : 400 }}>
          {children}
        </span>
      )}
    </Link>
  );
}

// ─── NavDivider ───────────────────────────────────────────────────
export function NavDivider() {
  return (
    <div style={{ height: 1, background: 'rgba(255,255,255,.06)', margin: '8px 16px' }} />
  );
}

// ─── NavSection ───────────────────────────────────────────────────
export function NavSection({ children }) {
  return <div className="nav-section">{children}</div>;
}