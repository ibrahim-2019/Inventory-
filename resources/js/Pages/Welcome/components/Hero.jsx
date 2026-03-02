import { Link } from '@inertiajs/react';

export default function Hero({ auth }) {
  return (
    <section className="hero">
      <div className="badge">
        <span className="badge-dot" />
        نظام إدارة مخزون متكامل
      </div>

      <h1 className="hero-title">
        تحكم في مخزونك<br />
        <span className="gradient">بذكاء وسهولة</span>
      </h1>

      <p className="hero-sub">
        منصة متكاملة لإدارة المخزون، تتبع الحركات، ومراقبة الصلاحيات
        — كل ما تحتاجه في مكان واحد.
      </p>

      <div className="hero-actions">
        {auth?.user ? (
          <Link href={route('admin.dashboard')} className="btn-hero filled">
            <i className="fas fa-gauge-simple-high" />
            الذهاب للوحة التحكم
          </Link>
        ) : (
          <>
            <Link href={route('register')} className="btn-hero filled">
              <i className="fas fa-rocket" />
              ابدأ الآن مجاناً
            </Link>
            <Link href={route('login')} className="btn-hero outline">
              تسجيل الدخول
            </Link>
          </>
        )}
      </div>
    </section>
  );
}