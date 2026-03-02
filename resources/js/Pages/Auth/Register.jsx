import { Head, Link, useForm } from '@inertiajs/react';
import AuthLayout, { Field } from './AuthLayout';

export default function Register() {
  const { data, setData, post, processing, errors } = useForm({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
  });

  function submit(e) {
    e.preventDefault();
    post(route('register'));
  }

  return (
    <AuthLayout title="إنشاء حساب" subtitle="أنشئ حسابك للبدء في استخدام النظام">
      <Head title="إنشاء حساب" />

      <form onSubmit={submit}>
        <Field label="الاسم الكامل" error={errors.name}>
          <input
            type="text"
            value={data.name}
            onChange={e => setData('name', e.target.value)}
            className={`auth-input${errors.name ? ' error' : ''}`}
            placeholder="محمد أحمد"
            autoComplete="name"
            autoFocus
          />
        </Field>

        <Field label="البريد الإلكتروني" error={errors.email}>
          <input
            type="email"
            value={data.email}
            onChange={e => setData('email', e.target.value)}
            className={`auth-input${errors.email ? ' error' : ''}`}
            placeholder="example@email.com"
            autoComplete="email"
          />
        </Field>

        <Field label="كلمة المرور" error={errors.password}>
          <input
            type="password"
            value={data.password}
            onChange={e => setData('password', e.target.value)}
            className={`auth-input${errors.password ? ' error' : ''}`}
            placeholder="٨ أحرف على الأقل"
            autoComplete="new-password"
          />
        </Field>

        <Field label="تأكيد كلمة المرور" error={errors.password_confirmation}>
          <input
            type="password"
            value={data.password_confirmation}
            onChange={e => setData('password_confirmation', e.target.value)}
            className={`auth-input${errors.password_confirmation ? ' error' : ''}`}
            placeholder="أعد كتابة كلمة المرور"
            autoComplete="new-password"
          />
        </Field>

        <button type="submit" className="auth-btn" disabled={processing} style={{ marginTop: 8 }}>
          {processing
            ? <><i className="fas fa-spinner fa-spin" style={{ marginLeft: 8 }} />جاري الإنشاء...</>
            : 'إنشاء الحساب'
          }
        </button>
      </form>

      <p style={{ textAlign: 'center', color: '#6b7280', fontSize: 13, marginTop: 20 }}>
        لديك حساب بالفعل؟{' '}
        <Link href={route('login')} className="auth-link" style={{ fontWeight: 700 }}>
          تسجيل الدخول
        </Link>
      </p>
    </AuthLayout>
  );
}