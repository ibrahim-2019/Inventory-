import React from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, useForm, Link } from '@inertiajs/react';

export default function Create({ categories, brands, units }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        sku: '',
        barcode: '',
        category_id: '',
        brand_id: '',
        base_unit_id: '',
        description: '',
        cost_price: '',
        selling_price: '',
        tax_percentage: 0,
        track_batches: true,
        has_expiry_date: false,
        withdrawal_strategy: 'fifo',
        alert_quantity: 10,
        expiry_alert_days: 30,
        auto_block_expired: true,
        is_active: true,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/products');
    };

    return (
        <AdminLayout>
            <Head title="إضافة منتج جديد" />

            <div className="max-w-4xl mx-auto">
                
                <div className="mb-6">
                    <Link 
                        href="/admin/products"
                        className="text-blue-600 hover:text-blue-800 flex items-center gap-2"
                    >
                        <i className="fas fa-arrow-right"></i>
                        رجوع للمنتجات
                    </Link>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    
                    {/* Basic Info */}
                    <div className="card">
                        <h3 className="text-lg font-semibold mb-4">المعلومات الأساسية</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    اسم المنتج <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="input"
                                    required
                                />
                                {errors.name && <p className="text-red-600 text-sm mt-1">{errors.name}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    SKU <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.sku}
                                    onChange={e => setData('sku', e.target.value)}
                                    className="input"
                                    required
                                />
                                {errors.sku && <p className="text-red-600 text-sm mt-1">{errors.sku}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                                <input
                                    type="text"
                                    value={data.barcode}
                                    onChange={e => setData('barcode', e.target.value)}
                                    className="input"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    التصنيف <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={data.category_id}
                                    onChange={e => setData('category_id', e.target.value)}
                                    className="input"
                                    required
                                >
                                    <option value="">اختر التصنيف</option>
                                    {categories.map(cat => (
                                        <option key={cat.id} value={cat.id}>{cat.name}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">العلامة التجارية</label>
                                <select
                                    value={data.brand_id}
                                    onChange={e => setData('brand_id', e.target.value)}
                                    className="input"
                                >
                                    <option value="">اختر العلامة</option>
                                    {brands.map(brand => (
                                        <option key={brand.id} value={brand.id}>{brand.name}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    الوحدة الأساسية <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={data.base_unit_id}
                                    onChange={e => setData('base_unit_id', e.target.value)}
                                    className="input"
                                    required
                                >
                                    <option value="">اختر الوحدة</option>
                                    {units.map(unit => (
                                        <option key={unit.id} value={unit.id}>
                                            {unit.name} ({unit.short_name})
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                                <textarea
                                    value={data.description}
                                    onChange={e => setData('description', e.target.value)}
                                    rows="3"
                                    className="input"
                                />
                            </div>

                        </div>
                    </div>

                    {/* Pricing */}
                    <div className="card">
                        <h3 className="text-lg font-semibold mb-4">التسعير</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">سعر التكلفة</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    value={data.cost_price}
                                    onChange={e => setData('cost_price', e.target.value)}
                                    className="input"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">سعر البيع</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    value={data.selling_price}
                                    onChange={e => setData('selling_price', e.target.value)}
                                    className="input"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">نسبة الضريبة (%)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    value={data.tax_percentage}
                                    onChange={e => setData('tax_percentage', e.target.value)}
                                    className="input"
                                />
                            </div>

                        </div>
                    </div>

                    {/* Stock Settings */}
                    <div className="card">
                        <h3 className="text-lg font-semibold mb-4">إعدادات المخزون</h3>
                        
                        <div className="space-y-4">
                            
                            <label className="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    checked={data.track_batches}
                                    onChange={e => setData('track_batches', e.target.checked)}
                                    className="w-4 h-4"
                                />
                                <span className="text-sm font-medium text-gray-700">تتبع بالدفعات (Batches)</span>
                            </label>

                            <label className="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    checked={data.has_expiry_date}
                                    onChange={e => setData('has_expiry_date', e.target.checked)}
                                    className="w-4 h-4"
                                />
                                <span className="text-sm font-medium text-gray-700">له تاريخ صلاحية</span>
                            </label>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        استراتيجية السحب
                                    </label>
                                    <select
                                        value={data.withdrawal_strategy}
                                        onChange={e => setData('withdrawal_strategy', e.target.value)}
                                        className="input"
                                    >
                                        <option value="fifo">FIFO (الأقدم أولاً)</option>
                                        <option value="fefo">FEFO (الأقرب للانتهاء أولاً)</option>
                                        <option value="manual">يدوي</option>
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        الحد الأدنى للتنبيه
                                    </label>
                                    <input
                                        type="number"
                                        value={data.alert_quantity}
                                        onChange={e => setData('alert_quantity', e.target.value)}
                                        className="input"
                                    />
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        التنبيه قبل انتهاء الصلاحية (يوم)
                                    </label>
                                    <input
                                        type="number"
                                        value={data.expiry_alert_days}
                                        onChange={e => setData('expiry_alert_days', e.target.value)}
                                        className="input"
                                    />
                                </div>

                            </div>

                            <label className="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    checked={data.is_active}
                                    onChange={e => setData('is_active', e.target.checked)}
                                    className="w-4 h-4"
                                />
                                <span className="text-sm font-medium text-gray-700">نشط</span>
                            </label>

                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-4">
                        <Link href="/admin/products" className="btn-secondary">
                            إلغاء
                        </Link>
                        <button 
                            type="submit" 
                            disabled={processing}
                            className="btn-primary disabled:opacity-50"
                        >
                            حفظ المنتج
                        </button>
                    </div>

                </form>

            </div>
        </AdminLayout>
    );
}