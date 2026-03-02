import { Link } from '@inertiajs/react';

export default function Navbar({ auth }) {
  return (
    <nav className="nav">
      <a href="/" className="nav-logo">
        <div className="nav-logo-icon">
          <i className="fas fa-boxes" style={{ color: '#fff', fontSize: 15 }} />
        </div>
        <span className="nav-logo-text">نظام المخزون</span>
      </a>

      <div className="nav-actions">
        {auth?.user ? (
          <Link href={route('admin.dashboard')} className="btn-primary">
            <i className="fas fa-gauge-simple-high" style={{ marginLeft: 6 }} />
            لوحة التحكم
          </Link>
        ) : (
          <>
            <Link href={route('login')}    className="btn-ghost">تسجيل الدخول</Link>
            <Link href={route('register')} className="btn-primary">ابدأ مجاناً</Link>
          </>
        )}
      </div>
    </nav>
  );
}