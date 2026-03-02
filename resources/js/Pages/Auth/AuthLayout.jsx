import { Link } from '@inertiajs/react';

const CSS = `
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:       #0d1117;
  --surface:  #161b24;
  --surface2: #1c2333;
  --border:   rgba(255,255,255,.08);
  --blue:     #3b82f6;
  --blue-lt:  #60a5fa;
  --indigo:   #6366f1;
  --text:     #f0f6ff;
  --muted:    #8b98b0;
  --muted2:   #4b5668;
  --font:     'Cairo', sans-serif;
}

html { scroll-behavior: smooth; }

body {
  background: var(--bg);
  font-family: var(--font);
  direction: rtl;
  color: var(--text);
  min-height: 100vh;
}

/* ── Animated grid background ── */
.dot-bg {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background-image:
    linear-gradient(rgba(59,130,246,.07) 1px, transparent 1px),
    linear-gradient(90deg, rgba(59,130,246,.07) 1px, transparent 1px);
  background-size: 44px 44px;
  animation: grid-shift 20s linear infinite;
}
.dot-bg::after {
  content: '';
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse 70% 55% at 50% -5%,  rgba(59,130,246,.13) 0%, transparent 60%),
    radial-gradient(ellipse 45% 45% at 95% 90%,  rgba(99,102,241,.10) 0%, transparent 55%),
    radial-gradient(ellipse 40% 30% at 5%  80%,  rgba(59,130,246,.07) 0%, transparent 50%);
}
@keyframes grid-shift {
  0%   { background-position: 0 0, 0 0; }
  100% { background-position: 44px 44px, 44px 44px; }
}

/* ── Root wrapper ── */
.auth-root {
  position: relative; z-index: 1;
  min-height: 100vh;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 40px 16px;
}

/* ── Logo ── */
.auth-logo-link {
  display: flex; align-items: center; gap: 10px;
  text-decoration: none; margin-bottom: 28px;
  opacity: 0; transform: translateY(14px);
  animation: fade-up .45s ease forwards;
}
.auth-logo-icon {
  width: 42px; height: 42px; border-radius: 12px;
  background: linear-gradient(135deg, var(--blue), var(--indigo));
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 6px 18px rgba(59,130,246,.35);
}
.auth-logo-text {
  color: var(--text); font-weight: 800; font-size: 17px;
}

/* ── Card ── */
.auth-card {
  width: 100%; max-width: 420px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 20px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 24px 64px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.05);
  opacity: 0; transform: translateY(18px);
  animation: fade-up .5s .1s ease forwards;
}

/* Gradient glow line at top of card */
.auth-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, transparent, var(--blue), var(--indigo), transparent);
  opacity: .8;
}

.auth-card-header {
  padding: 32px 32px 0;
  text-align: center;
}
.auth-card-title {
  font-size: 22px; font-weight: 900;
  color: var(--text); margin-bottom: 6px;
}
.auth-card-subtitle {
  font-size: 13px; color: var(--muted); line-height: 1.6;
}
.auth-card-body {
  padding: 28px 32px 32px;
}

/* ── Field ── */
.auth-field { margin-bottom: 18px; }
.auth-label {
  display: block;
  font-size: 13px; font-weight: 700;
  color: var(--muted); margin-bottom: 7px;
}
.auth-error-msg {
  display: flex; align-items: center; gap: 5px;
  color: #f87171; font-size: 12px; margin-top: 5px;
}

/* ── Inputs ── */
.auth-input {
  width: 100%;
  background: var(--surface2);
  border: 1.5px solid var(--border);
  border-radius: 10px;
  padding: 11px 14px;
  font-family: var(--font);
  font-size: 14px;
  color: var(--text);
  outline: none;
  direction: ltr; text-align: right;
  transition: border-color .18s, box-shadow .18s, background .18s;
}
.auth-input::placeholder { color: var(--muted2); }
.auth-input:focus {
  border-color: rgba(59,130,246,.5);
  box-shadow: 0 0 0 3px rgba(59,130,246,.12);
  background: rgba(28,35,51,.9);
}
.auth-input.error {
  border-color: rgba(239,68,68,.45);
  box-shadow: 0 0 0 3px rgba(239,68,68,.08);
}

/* ── Checkbox ── */
.auth-checkbox {
  width: 16px; height: 16px;
  accent-color: var(--blue);
  cursor: pointer; flex-shrink: 0;
}

/* ── Link ── */
.auth-link {
  color: var(--blue-lt);
  text-decoration: none; font-size: 13px; font-weight: 600;
  transition: color .15s;
}
.auth-link:hover { color: #fff; }

/* ── Submit button ── */
.auth-btn {
  width: 100%;
  padding: 13px 20px; border-radius: 11px;
  font-family: var(--font); font-size: 15px; font-weight: 800;
  color: #fff;
  background: linear-gradient(135deg, var(--blue), var(--indigo));
  border: none; cursor: pointer;
  box-shadow: 0 6px 22px rgba(59,130,246,.3);
  transition: opacity .18s, transform .15s, box-shadow .18s;
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.auth-btn:hover:not(:disabled) {
  opacity: .9; transform: translateY(-1px);
  box-shadow: 0 10px 28px rgba(59,130,246,.38);
}
.auth-btn:disabled { opacity: .55; cursor: not-allowed; }

/* ── Footer note ── */
.auth-footer-note {
  color: var(--muted2); font-size: 12px; margin-top: 24px;
  opacity: 0; transform: translateY(10px);
  animation: fade-up .5s .22s ease forwards;
}

/* ── Animations ── */
@keyframes fade-up {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── Responsive ── */
@media (max-width: 480px) {
  .auth-card-header { padding: 24px 20px 0; }
  .auth-card-body   { padding: 20px 20px 24px; }
}
`;

export default function AuthLayout({ title, subtitle, children }) {
  return (
    <>
      <style dangerouslySetInnerHTML={{ __html: CSS }} />

      {/* Animated grid background — same as welcome page */}
      <div className="dot-bg" />

      <div className="auth-root">
        {/* Logo */}
        <Link href="/" className="auth-logo-link">
          <div className="auth-logo-icon">
            <i className="fas fa-boxes" style={{ color: '#fff', fontSize: 16 }} />
          </div>
          <span className="auth-logo-text">نظام المخزون</span>
        </Link>

        {/* Card */}
        <div className="auth-card">
          <div className="auth-card-header">
            <h1 className="auth-card-title">{title}</h1>
            {subtitle && <p className="auth-card-subtitle">{subtitle}</p>}
          </div>
          <div className="auth-card-body">
            {children}
          </div>
        </div>

        <p className="auth-footer-note">
          © {new Date().getFullYear()} نظام المخزون — جميع الحقوق محفوظة
        </p>
      </div>
    </>
  );
}

export function Field({ label, error, children }) {
  return (
    <div className="auth-field">
      {label && <label className="auth-label">{label}</label>}
      {children}
      {error && (
        <p className="auth-error-msg">
          <i className="fas fa-exclamation-circle" /> {error}
        </p>
      )}
    </div>
  );
}