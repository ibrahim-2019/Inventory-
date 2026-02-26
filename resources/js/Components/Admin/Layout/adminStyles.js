// ─── Design Tokens ────────────────────────────────────────────────
export const SIDEBAR_W_OPEN  = 260;
export const SIDEBAR_W_CLOSE = 72;
export const MOBILE_BREAKPOINT = 768;

// ─── Route → Page Title map ───────────────────────────────────────
export const ROUTE_NAMES = {
  '/admin/dashboard':            'لوحة التحكم',
  '/admin/products':             'المنتجات',
  '/admin/stock/in':             'إضافة وارد',
  '/admin/stock/out':            'صرف مخزون',
  '/admin/warehouses':           'المخازن',
  '/admin/reports/current-stock':'المخزون الحالي',
  '/admin/reports/expiry':       'تقرير الصلاحية',
};

// ─── Global CSS (injected once via <style> in AdminLayout) ────────
export const CSS_VARS = `
  :root {
    --sidebar-bg:        #0f1117;
    --sidebar-border:    rgba(255,255,255,.06);
    --sidebar-text:      #9ca3af;
    --sidebar-hover-bg:  rgba(255,255,255,.05);
    --sidebar-active-bg: rgba(59,130,246,.12);
    --sidebar-accent:    #3b82f6;
    --topbar-bg:         #ffffff;
    --page-bg:           #f1f5f9;
    --shadow-sm:         0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    --radius:            10px;
    --font-ar:           'Cairo', 'Tajawal', sans-serif;
    --transition:        .22s cubic-bezier(.4,0,.2,1);
  }
  * { box-sizing: border-box; }
  body { font-family: var(--font-ar); direction: rtl; }

  ::-webkit-scrollbar { width: 5px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 99px; }

  .nav-link {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px; border-radius: var(--radius);
    margin: 2px 10px; cursor: pointer; text-decoration: none;
    color: var(--sidebar-text);
    transition: background var(--transition), color var(--transition);
    white-space: nowrap; overflow: hidden;
  }
  .nav-link:hover { background: var(--sidebar-hover-bg); color: #e5e7eb; }
  .nav-link.active {
    background: var(--sidebar-active-bg); color: #60a5fa;
    box-shadow: inset 0 0 0 1px rgba(59,130,246,.2);
  }
  .nav-link .icon {
    font-size: 16px; min-width: 22px; text-align: center;
    transition: transform var(--transition);
  }
  .nav-link:hover .icon { transform: scale(1.1); }

  .nav-section {
    font-size: 10px; font-weight: 700; letter-spacing: .12em;
    text-transform: uppercase; color: #4b5563;
    padding: 16px 26px 4px; user-select: none;
  }

  @keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0 rgba(239,68,68,.45); }
    70%  { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
    100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
  }
  .badge-pulse { animation: pulse-ring 2s infinite; }

  @keyframes flash-in {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .flash-msg { animation: flash-in .3s ease forwards; }

  @keyframes dd-in {
    from { opacity: 0; transform: translateY(-6px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }
  .dropdown-menu { animation: dd-in .18s ease forwards; }

  @keyframes fade-in {
    from { opacity: 0; } to { opacity: 1; }
  }
  .sidebar-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.5);
    z-index: 29; animation: fade-in .2s ease forwards;
    backdrop-filter: blur(2px);
  }

  @keyframes slide-in-right {
    from { transform: translateX(100%); } to { transform: translateX(0); }
  }
  .sidebar-mobile {
    position: fixed !important; top: 0; right: 0; height: 100vh;
    z-index: 30; animation: slide-in-right .25s cubic-bezier(.4,0,.2,1) forwards;
  }

  @media (max-width: 480px) {
    .user-name-text { display: none !important; }
  }
`;