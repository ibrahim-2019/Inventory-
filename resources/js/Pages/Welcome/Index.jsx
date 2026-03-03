import React from 'react';
import { Head } from '@inertiajs/react';
import { useEffect } from 'react';
import './welcome.css';

import Navbar    from './components/Navbar';
import Hero      from './components/Hero';
import Stats     from './components/Stats';
import Features  from './components/Features';
import CtaBanner from './components/CtaBanner';

export default function Welcome({ auth }) {

  // ── Scroll reveal ──────────────────────────────────────────────
  useEffect(() => {
    const els = document.querySelectorAll('.reveal, .feat-card');
    const io  = new IntersectionObserver(
      entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
      { threshold: 0.12 }
    );
    els.forEach(el => io.observe(el));
    return () => io.disconnect();
  }, []);

  return (
    <>
      <Head title="نظام المخزون — إدارة ذكية" />

      {/* Animated grid background */}
      <div className="dot-bg" />

      <Navbar    auth={auth} />
      <Hero      auth={auth} />
      <Stats />
      <Features />
      <CtaBanner auth={auth} />

      {/* Footer */}
      <footer className="footer">
        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
          <div className="footer-logo">
            <i className="fas fa-boxes" style={{ color: '#fff', fontSize: 10 }} />
          </div>
          <span style={{ fontWeight: 600, color: '#8b98b0' }}>نظام المخزون</span>
        </div>
        <span>© {new Date().getFullYear()} جميع الحقوق محفوظة</span>
      </footer>
    </>
  );
}