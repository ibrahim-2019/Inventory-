import { Head, Link, useForm } from '@inertiajs/react';
import AuthLayout, { Field } from './AuthLayout';

export default function Login({ status, canResetPassword }) {
  const { data, setData, post, processing, errors } = useForm({
    email:    '',
    password: '',
    remember: false,
  });

  function submit(e) {
    e.preventDefault();
    post(route('login'));
  }

  return (
    <AuthLayout title="تسجيل الدخول" subtitle="أهلاً بك، سجّل دخولك للمتابعة">
      <Head title="تسجيل الدخول" />

      {status && (
        <div style={{
          background: 'rgba(34,197,94,.1)', border: '1px solid rgba(34,197,94,.2)',
          borderRadius: 10, padding: '10px 14px',
          color: '#4ade80', fontSize: 13, marginBottom: 20,
        }}>
          {status}
        </div>
      )}

      <form onSubmit={submit}>
        <Field label="البريد الإلكتروني" error={errors.email}>
          <input
            type="email"
            value={data.email}
            onChange={e => setData('email', e.target.value)}
            className={`auth-input${errors.email ? ' error' : ''}`}
            placeholder="example@email.com"
            autoComplete="email"
            autoFocus
          />
        </Field>

        <Field label="كلمة المرور" error={errors.password}>
          <input
            type="password"
            value={data.password}
            onChange={e => setData('password', e.target.value)}
            className={`auth-input${errors.password ? ' error' : ''}`}
            placeholder="••••••••"
            autoComplete="current-password"
          />
        </Field>

        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 24 }}>
          <label style={{ display: 'flex', alignItems: 'center', gap: 8, cursor: 'pointer' }}>
            <input
              type="checkbox"
              className="auth-checkbox"
              checked={data.remember}
              onChange={e => setData('remember', e.target.checked)}
            />
            <span style={{ color: '#9ca3af', fontSize: 13 }}>تذكرني</span>
          </label>

          {canResetPassword && (
            <Link href={route('password.request')} className="auth-link">
              نسيت كلمة المرور؟
            </Link>
          )}
        </div>

        <button type="submit" className="auth-btn" disabled={processing}>
          {processing
            ? <><i className="fas fa-spinner fa-spin" style={{ marginLeft: 8 }} />جاري الدخول...</>
            : 'تسجيل الدخول'
          }
        </button>
      </form>

      <p style={{ textAlign: 'center', color: '#6b7280', fontSize: 13, marginTop: 20 }}>
        ليس لديك حساب؟{' '}
        <Link href={route('register')} className="auth-link" style={{ fontWeight: 700 }}>
          إنشاء حساب
        </Link>
      </p>
    </AuthLayout>
  );
}