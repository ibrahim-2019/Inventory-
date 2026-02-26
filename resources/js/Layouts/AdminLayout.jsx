import { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { CSS_VARS } from '../Components/Admin/Layout/adminStyles';
import { useSidebar } from '../Components/Admin/Layout/adminHooks';
import { Sidebar } from '../Components/Admin/Layout/Sidebar';
import { Topbar } from '../Components/Admin/Layout/Topbar';
import { FlashBanner } from '../Components/Admin/Layout/FlashBanner';

// ─── Font loader (Cairo via Google Fonts) ─────────────────────────
function useFontLoader() {
  useEffect(() => {
    if (document.getElementById('cairo-font')) return;
    const link = document.createElement('link');
    link.id   = 'cairo-font';
    link.rel  = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap';
    document.head.appendChild(link);
  }, []);
}

// ─── AdminLayout ──────────────────────────────────────────────────
export default function AdminLayout({ children }) {
  const { flash } = usePage().props;
  const { open, isMobile, toggleSidebar, closeSidebar } = useSidebar();

  useFontLoader();

  return (
    <>
      <style dangerouslySetInnerHTML={{ __html: CSS_VARS }} />

      <div style={{ display: 'flex', height: '100vh', background: 'var(--page-bg)', direction: 'rtl' }}>

        <Sidebar open={open} isMobile={isMobile} onClose={closeSidebar} />

        <div style={{ flex: 1, display: 'flex', flexDirection: 'column', overflow: 'hidden', minWidth: 0 }}>

          <Topbar onToggleSidebar={toggleSidebar} notifCount={3} />

          <FlashBanner flash={flash} />

          <main style={{ flex: 1, overflowY: 'auto', overflowX: 'hidden', padding: 24 }}>
            {children}
          </main>

        </div>
      </div>
    </>
  );
}