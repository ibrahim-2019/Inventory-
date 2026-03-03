import React from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import { StatCard }          from '@/Components/Admin/Dashboard/StatCard';
import { RecentMovements }   from '@/Components/Admin/Dashboard/RecentMovements';
import { LowStockTable }     from '@/Components/Admin/Dashboard/LowStockTable';
import { ExpiringBatches }   from '@/Components/Admin/Dashboard/ExpiringBatches';
import { MovementsChart }    from '@/Components/Admin/Dashboard/MovementsChart';
import { WarehouseDonut }    from '@/Components/Admin/Dashboard/WarehouseDonut';

// ─── Dashboard ────────────────────────────────────────────────────
// warehouseStock prop — أضيف في الـ controller:
// $warehouseStock = Stock::with('warehouse')
//     ->selectRaw('warehouse_id, sum(quantity) as total_quantity')
//     ->groupBy('warehouse_id')->get();
export default function Dashboard({ summary, recentMovements, lowStockProducts, expiringBatches, warehouseStock }) {
  return (
    <AdminLayout>
      <Head title="لوحة التحكم" />

      {/* ── Page header ── */}
      <div style={{ marginBottom: 24 }}>
        <h2 style={{ fontSize: 22, fontWeight: 800, color: '#111827', margin: 0 }}>
          لوحة التحكم
        </h2>
        <p style={{ color: '#6b7280', fontSize: 13, marginTop: 4 }}>
          نظرة عامة على النظام
        </p>
      </div>

      {/* ── Stat cards ── */}
      <div style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
        gap: 16, marginBottom: 24,
      }}>
        <StatCard
          title="إجمالي المنتجات"
          value={summary?.total_products}
          icon="fas fa-box"
          color="blue"
        />
        <StatCard
          title="المخازن"
          value={summary?.total_warehouses}
          icon="fas fa-warehouse"
          color="green"
        />
        <StatCard
          title="منتجات منخفضة"
          value={summary?.low_stock_count}
          icon="fas fa-exclamation-triangle"
          color="red"
        />
        <StatCard
          title="قريبة من الانتهاء"
          value={summary?.expiring_soon}
          icon="fas fa-calendar-times"
          color="orange"
        />
      </div>

      {/* ── Charts row ── */}
      <div style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
        gap: 16, marginBottom: 24,
      }}>
        <MovementsChart movements={recentMovements} />
        <WarehouseDonut warehouseStock={warehouseStock} />
      </div>

      {/* ── Content grid ── */}
      <div style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))',
        gap: 16,
      }}>
        <RecentMovements movements={recentMovements} />
        <LowStockTable   products={lowStockProducts} />
        <ExpiringBatches batches={expiringBatches} />
      </div>

    </AdminLayout>
  );
}