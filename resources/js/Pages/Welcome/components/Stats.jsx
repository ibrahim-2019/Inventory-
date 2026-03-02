import React from 'react';

const STATS = [
  { val: '100%', lbl: 'دقة في التتبع' },
  { val: '+50',  lbl: 'نوع تقرير' },
  { val: '24/7', lbl: 'متاح دائماً' },
  { val: '∞',    lbl: 'عدد المنتجات' },
];

export default function Stats() {
  return (
    <div className="stats-row reveal">
      {STATS.map((s, i) => (
        <div className="stat-cell" key={i}>
          <div className="stat-val">{s.val}</div>
          <div className="stat-lbl">{s.lbl}</div>
        </div>
      ))}
    </div>
  );
}