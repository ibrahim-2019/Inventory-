import AdminLayout from '@/Layouts/AdminLayout';
import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ products, categories, brands }) {
    const [search, setSearch] = useState('');
    const [categoryFilter, setCategoryFilter] = useState('');
    const [brandFilter, setBrandFilter] = useState('');

    const handleFilter = () => {
        router.get('/admin/products', {
            search,
            category_id: categoryFilter,
            brand_id: brandFilter,
        }, {
            preserveState: true,
        });
    };

    const handleDelete = (id) => {
        if (confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
            router.delete(`/admin/products/${id}`);
        }
    };

    return (
        <AdminLayout>
            <Head title="المنتجات" />
            
            {/* Header */}
            <div className="mb-6 flex items-center justify-between">
                <div>
                    <h2 className="text-2xl font-bold text-gray-800">إدارة المنتجات</h2>
                    <p className="text-gray-600 mt-1">عرض وإدارة جميع المنتجات</p>
                </div>
                <Link 
                    href="/admin/products/create" 
                    className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2"
                >
                    <i className="fas fa-plus"></i>
                    إضافة منتج جديد
                </Link>
            </div>

            {/* Filters */}
            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">بحث</label>
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="اسم المنتج، SKU، Barcode"
                            className="input"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">التصنيف</label>
                        <select 
                            value={categoryFilter}
                            onChange={(e) => setCategoryFilter(e.target.value)}
                            className="input"
                        >
                            <option value="">الكل</option>
                            {categories.map(cat => (
                                <option key={cat.id} value={cat.id}>{cat.name}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">العلامة التجارية</label>
                        <select 
                            value={brandFilter}
                            onChange={(e) => setBrandFilter(e.target.value)}
                            className="input"
                        >
                            <option value="">الكل</option>
                            {brands.map(brand => (
                                <option key={brand.id} value={brand.id}>{brand.name}</option>
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
                            onClick={() => {
                                setSearch('');
                                setCategoryFilter('');
                                setBrandFilter('');
                                router.get('/admin/products');
                            }}
                            className="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg"
                        >
                            <i className="fas fa-redo"></i>
                        </button>
                    </div>

                </div>
            </div>

            {/* Products Table */}
            <div className="bg-white rounded-lg shadow overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التصنيف</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزون</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">السعر</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {products.data.length > 0 ? (
                                products.data.map(product => (
                                    <tr key={product.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-3">
                                                <div className="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                                    <i className="fas fa-box text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <div className="font-medium text-gray-900">{product.name}</div>
                                                    {product.brand && (
                                                        <div className="text-sm text-gray-500">{product.brand.name}</div>
                                                    )}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-500">{product.sku}</td>
                                        <td className="px-6 py-4 text-sm">{product.category?.name || '-'}</td>
                                        <td className="px-6 py-4">
                                            {product.stock.reduce((sum, s) => sum + parseFloat(s.available_quantity), 0)}
                                        </td>
                                        <td className="px-6 py-4 text-sm">
                                            {parseFloat(product.selling_price || 0).toFixed(2)} ج
                                        </td>
                                        <td className="px-6 py-4">
                                            {product.is_active ? (
                                                <span className="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    نشط
                                                </span>
                                            ) : (
                                                <span className="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    غير نشط
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-sm">
                                            <div className="flex items-center gap-2">
                                                <Link 
                                                    href={`/admin/products/${product.id}`}
                                                    className="text-blue-600 hover:text-blue-800"
                                                >
                                                    <i className="fas fa-eye"></i>
                                                </Link>
                                                <Link 
                                                    href={`/admin/products/${product.id}/edit`}
                                                    className="text-yellow-600 hover:text-yellow-800"
                                                >
                                                    <i className="fas fa-edit"></i>
                                                </Link>
                                                <button 
                                                    onClick={() => handleDelete(product.id)}
                                                    className="text-red-600 hover:text-red-800"
                                                >
                                                    <i className="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="7" className="px-6 py-12 text-center text-gray-500">
                                        <i className="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
                                        <p>لا توجد منتجات</p>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {products.links.length > 3 && (
                    <div className="px-6 py-4 border-t">
                        <div className="flex gap-2">
                            {products.links.map((link, index) => (
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