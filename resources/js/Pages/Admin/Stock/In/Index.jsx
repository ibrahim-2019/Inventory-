import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ movements, warehouses, products }) {
    const [filters, setFilters] = useState({
        date_from: '',
        date_to: '',
        warehouse_id: '',
        product_id: '',
    });

    const handleFilter = () => {
        router.get('/admin/stock/in', filters, {
            preserveState: true,
        });
    };

    const handleReset = () => {
        setFilters({
            date_from: '',
            date_to: '',
            warehouse_id: '',
            product_id: '',
        });
        router.get('/admin/stock/in');
    };

    return (
        <AdminLayout>
            <Head title="سجل إضافة المخزون" />

            {/* Header */}
            <div className="mb-6 flex items-center justify-between">
                <div>
                    <h2 className="text-2xl font-bold text-gray-800">سجل إضافة المخزون</h2>
                    <p className="text-gray-600 mt-1">عرض جميع عمليات الإضافة</p>
                </div>
                <Link 
                    href="/admin/stock/in/create"
                    className="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center gap-2"
                >
                    <i className="fas fa-plus"></i>
                    إضافة مخزون جديد
                </Link>
            </div>

            {/* Filters */}
            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
                    
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                        <input
                            type="date"
                            value={filters.date_from}
                            onChange={e => setFilters({...filters, date_from: e.target.value})}
                            className="input"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                        <input
                            type="date"
                            value={filters.date_to}
                            onChange={e => setFilters({...filters, date_to: e.target.value})}
                            className="input"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">المخزن</label>
                        <select
                            value={filters.warehouse_id}
                            onChange={e => setFilters({...filters, warehouse_id: e.target.value})}
                            className="input"
                        >
                            <option value="">الكل</option>
                            {warehouses.map(w => (
                                <option key={w.id} value={w.id}>{w.name}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">المنتج</label>
                        <select
                            value={filters.product_id}
                            onChange={e => setFilters({...filters, product_id: e.target.value})}
                            className="input"
                        >
                            <option value="">الكل</option>
                            {products.map(p => (
                                <option key={p.id} value={p.id}>{p.name}</option>
                            ))}
                        </select>
                    </div>

                    <div className="flex items-end gap-2">
                        <button 
                            onClick={handleFilter}
                            className="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
                        >
                            <i className="fas fa-search ml-2"></i>
                            بحث
                        </button>
                        <button 
                            onClick={handleReset}
                            className="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg"
                        >
                            <i className="fas fa-redo"></i>
                        </button>
                    </div>

                </div>
            </div>

            {/* Movements Table */}
            <div className="bg-white rounded-lg shadow overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزن</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التكلفة</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المرجع</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">بواسطة</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {movements.data.length > 0 ? (
                                movements.data.map(movement => (
                                    <tr key={movement.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4 text-sm text-gray-500">#{movement.id}</td>
                                        <td className="px-6 py-4">
                                            <div className="font-medium text-gray-900">{movement.product.name}</div>
                                            <div className="text-sm text-gray-500">{movement.product.sku}</div>
                                        </td>
                                        <td className="px-6 py-4 text-sm">{movement.warehouse.name}</td>
                                        <td className="px-6 py-4">
                                            <span className="font-medium text-green-600">
                                                +{movement.quantity} {movement.product.base_unit.short_name}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-sm">
                                            <div className="font-medium">{parseFloat(movement.total_cost).toFixed(2)} ج</div>
                                            <div className="text-gray-500">{parseFloat(movement.average_unit_cost).toFixed(2)} ج/وحدة</div>
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-500">
                                            {movement.reference_number || '-'}
                                        </td>
                                        <td className="px-6 py-4 text-sm">
                                            {movement.creator?.name || 'النظام'}
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-500">
                                            <div>{new Date(movement.created_at).toLocaleDateString('ar-EG')}</div>
                                            <div className="text-xs">{new Date(movement.created_at).toLocaleTimeString('ar-EG', {hour: '2-digit', minute: '2-digit'})}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <Link
                                                href={`/admin/stock/in/${movement.id}`}
                                                className="text-blue-600 hover:text-blue-800"
                                            >
                                                <i className="fas fa-eye"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="9" className="px-6 py-12 text-center text-gray-500">
                                        <i className="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                                        <p>لا توجد عمليات إضافة</p>
                                    </td>
                                </tr>
                            )}
                        </tbody>

                        {/* Summary */}
                        {movements.data.length > 0 && (
                            <tfoot className="bg-gray-50 font-medium">
                                <tr>
                                    <td colSpan="3" className="px-6 py-4 text-left">الإجمالي</td>
                                    <td className="px-6 py-4 text-green-600">
                                        +{movements.data.reduce((sum, m) => sum + parseFloat(m.quantity), 0).toFixed(2)}
                                    </td>
                                    <td className="px-6 py-4">
                                        {movements.data.reduce((sum, m) => sum + parseFloat(m.total_cost), 0).toFixed(2)} ج
                                    </td>
                                    <td colSpan="4"></td>
                                </tr>
                            </tfoot>
                        )}
                    </table>
                </div>

                {/* Pagination */}
                {movements.links.length > 3 && (
                    <div className="px-6 py-4 border-t">
                        <div className="flex gap-2">
                            {movements.links.map((link, index) => (
                                <Link
                                    key={index}
                                    href={link.url || '#'}
                                    className={`px-3 py-1 rounded ${
                                        link.active 
                                            ? 'bg-blue-600 text-white' 
                                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                    } ${!link.url && 'opacity-50 cursor-not-allowed'}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}