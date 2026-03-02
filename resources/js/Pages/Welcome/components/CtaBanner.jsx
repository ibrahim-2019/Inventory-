import { Link } from '@inertiajs/react';

export default function CtaBanner({ auth }) {
  return (
    <div className="cta-wrap reveal">
      <div className="cta-inner">
        <div className="cta-orb2" />
        <h2 className="cta-title">جاهز للبدء؟</h2>
        <p className="cta-sub">انضم الآن وابدأ في إدارة مخزونك باحترافية</p>

        {auth?.user ? (
          <Link href={route('admin.dashboard')} className="btn-cta">
            <i className="fas fa-gauge-simple-high" />
            الذهاب للوحة التحكم
          </Link>
        ) : (
          <Link href={route('register')} className="btn-cta">
            <i className="fas fa-arrow-left" />
            إنشاء حساب مجاناً
          </Link>
        )}
      </div>
    </div>
  );
}