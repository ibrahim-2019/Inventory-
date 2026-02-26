import { useState, useEffect } from 'react';
import { MOBILE_BREAKPOINT } from './adminStyles';

// ─── useMediaQuery ────────────────────────────────────────────────
// Returns true when window width is below the given breakpoint
export function useMediaQuery(maxWidth = MOBILE_BREAKPOINT) {
  const [matches, setMatches] = useState(
    () => typeof window !== 'undefined' && window.innerWidth < maxWidth
  );

  useEffect(() => {
    const mq = window.matchMedia(`(max-width: ${maxWidth - 1}px)`);
    const handler = (e) => setMatches(e.matches);
    mq.addEventListener('change', handler);
    return () => mq.removeEventListener('change', handler);
  }, [maxWidth]);

  return matches;
}

// ─── useSidebar ───────────────────────────────────────────────────
// Encapsulates all sidebar open/close logic for both desktop & mobile
export function useSidebar() {
  const isMobile = useMediaQuery();
  const [open, setOpen] = useState(() => !isMobile);

  // Sync open state when screen size crosses the breakpoint
  useEffect(() => {
    setOpen(!isMobile);
  }, [isMobile]);

  const openSidebar  = () => setOpen(true);
  const closeSidebar = () => setOpen(false);
  const toggleSidebar = () => setOpen(v => !v);

  return { open, isMobile, openSidebar, closeSidebar, toggleSidebar };
}