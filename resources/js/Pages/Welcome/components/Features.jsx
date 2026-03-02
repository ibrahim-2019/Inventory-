const FEATURES = [
  { icon: 'fas fa-boxes',          title: 'إدارة المنتجات',   desc: 'تتبع جميع منتجاتك بتفاصيلها الكاملة من مكان واحد بسهولة.' },
  { icon: 'fas fa-warehouse',      title: 'تعدد المخازن',     desc: 'إدارة أكثر من مخزن في آنٍ واحد مع رؤية شاملة لكل موقع.' },
  { icon: 'fas fa-download',       title: 'حركات الوارد',     desc: 'تسجيل عمليات استلام البضاعة مع تتبع الكميات والتواريخ.' },
  { icon: 'fas fa-upload',         title: 'حركات الصادر',     desc: 'متابعة عمليات الصرف والتوزيع مع سجل كامل لكل حركة.' },
  { icon: 'fas fa-chart-bar',      title: 'تقارير تفصيلية',   desc: 'تقارير شاملة للمخزون الحالي وحركات البضاعة بلمحة بصرية.' },
  { icon: 'fas fa-calendar-times', title: 'تنبيهات الصلاحية', desc: 'تنبيهات تلقائية للمنتجات قريبة من انتهاء الصلاحية.' },
];

export default function Features() {
  return (
    <section className="section">
      <div className="sec-eyebrow reveal">المميزات</div>
      <h2 className="sec-title reveal">كل ما تحتاجه في مكان واحد</h2>
      <p className="sec-sub reveal">نظام متكامل يغطي جميع احتياجات إدارة المخزون الحديثة</p>

      <div className="grid-6">
        {FEATURES.map((f, i) => (
          <div className="feat-card" key={i} style={{ animationDelay: `${i * 0.08}s` }}>
            <div className="feat-icon">
              <i className={f.icon} style={{ color: 'var(--blue)', fontSize: 17 }} />
            </div>
            <div className="feat-title">{f.title}</div>
            <div className="feat-desc">{f.desc}</div>
          </div>
        ))}
      </div>
    </section>
  );
}