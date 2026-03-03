import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthLayout from './Auth/AuthLayout';

// هذه الصفحة هي الـ dashboard الافتراضي بتاع Breeze بعد اللوجين
// بترحّل المستخدم لـ admin.dashboard مباشرةً

export default function Dashboard({ auth }) {
  return (
    <AuthLayout title={`أهلاً، ${auth.user.name} 👋`} subtitle="تم تسجيل دخولك بنجاح">
      <Head title="مرحباً" />

      <div style={{ textAlign: 'center' }}>
        {/* Avatar */}
        <div style={{
          width: 64, height: 64, borderRadius: 99, margin: '0 auto 20px',
          background: 'linear-gradient(135deg,#6366f1,#8b5cf6)',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          fontSize: 26, color: '#fff', fontWeight: 800,
          boxShadow: '0 8px 24px rgba(99,102,241,.35)',
        }}>
          {auth.user.name?.[0] ?? 'U'}
        </div>

        <p style={{ color: '#9ca3af', fontSize: 14, lineHeight: 1.7, marginBottom: 28 }}>
          {auth.user.email}
        </p>

        <Link
          href={route('admin.dashboard')}
          style={{
            display: 'inline-flex', alignItems: 'center', gap: 8,
            width: '100%', justifyContent: 'center',
            background: 'linear-gradient(135deg,#3b82f6,#6366f1)',
            color: '#fff', fontFamily: 'Cairo, sans-serif',
            fontWeight: 700, fontSize: 14, padding: '12px',
            borderRadius: 10, textDecoration: 'none',
            boxShadow: '0 4px 14px rgba(59,130,246,.35)',
            transition: 'opacity .2s',
          }}
          onMouseEnter={e => e.currentTarget.style.opacity = '.9'}
          onMouseLeave={e => e.currentTarget.style.opacity = '1'}
        >
          <i className="fas fa-gauge-simple-high" />
          الذهاب للوحة التحكم
        </Link>
      </div>
    </AuthLayout>
  );
}