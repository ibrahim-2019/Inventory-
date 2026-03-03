import AdminLayout from '@/Layouts/AdminLayout';
import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function Create({ products, warehouses }) {
    const [selectedProduct, setSelectedProduct] = useState(null);
    const [totalCost, setTotalCost] = useState(0);

    const { data, setData, post, processing, errors } = useForm({
        product_id: '',
        warehouse_id: '',
        quantity: '',
        unit_id: '',
        unit_cost: '',
        batch_number: '',
        supplier_name: '',
        purchase_date: new Date().toISOString().split('T')[0],
        expiry_date: '',
        manufacture_date: '',
        reference_number: '',
        notes: '',
    });

    useEffect(() => {
        if (data.quantity && data.unit_cost) {
            setTotalCost((parseFloat(data.quantity) * parseFloat(data.unit_cost)).toFixed(2));
        } else {
            setTotalCost(0);
        }
    }, [data.quantity, data.unit_cost]);

    const handleProductChange = (productId) => {
        const product = products.find(p => p.id === parseInt(productId));
        setSelectedProduct(product);
        setData({
            ...data,
            product_id: productId,
            unit_id: product?.base_unit_id || '',
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/stock/in');
    };

    return (
        <AdminLayout>
            <Head title="إضافة مخزون" />

            <div className="max-w-4xl mx-auto">
                
                <div className="mb-6">
                    <Link 
                        href="/admin/stock/in"
                        className="text-blue-600 hover:text-blue-800 flex items-center gap-2"
                    >
                        <i className="fas fa-arrow-right"></i>
                        رجوع لقائمة الإضافات
                    </Link>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    
                    {/* Product Selection */}
                    <div className="card">
                        <h3 className="text-lg font-semibold mb-4">معلومات المنتج</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    المنتج <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={data.product_id}
                                    onChange={e => handleProductChange(e.target.value)}
                                    className="input"
                                    required
                                >
                                    <option value="">اختر المنتج</option>
                                    {products.map(product => (
                                        <option key={product.id} value={product.id}>
                                            {product.name} ({product.sku})
                                        </option>
                                    ))}
                                </select>
                                {errors.product_id && <p className="text-red-600 text-sm mt-1">{errors.product_id}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    المخزن <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={data.warehouse_id}
                                    onChange={e => setData('warehouse_id', e.target.value)}
                                    className="input"
                                    required
                                >
                                    <option value="">اختر المخزن</option>
                                    {warehouses.map(warehouse => (
                                        <option key={warehouse.id} value={warehouse.id}>
                                            {warehouse.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.warehouse_id && <p className="text-red-600 text-sm mt-1">{errors.warehouse_id}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">رقم المرجع</label>
                                <input
                                    type="text"
                                    value={data.reference_number}
                                    onChange={e => setData('reference_number', e.target.value)}
                                    className="input"
                                    placeholder="اختياري"
                                />
                            </div>

                        </div>
                    </div>

                    {/* Quantity & Pricing */}
                    <div className="card">
                        <h3 className="text-lg font-semibold mb-4">الكمية والتسعير</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    الكمية <span className="text-red-500">*</span>
                                </label>
                                <div className="flex gap-2">
                                    <input
                                        type="number"
                                        step="0.01"
                                        value={data.quantity}
                                        onChange={e => setData('quantity', e.target.value)}
                                        className="flex-1 input"
                                        required
                                    />
                                    <div className="w-24 px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-center">
                                        {selectedProduct?.base_unit.short_name || '-'}
                                    </div>
                                </div>
                                {errors.quantity && <p className="text-red-600 text-sm mt-1">{errors.quantity}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    سعر الوحدة <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    value={data.unit_cost}
                                    onChange={e => setData('unit_cost', e.target.value)}
                                    className="input"
                                    required
                                />
                                {errors.unit_cost && <p className="text-red-600 text-sm mt-1">{errors.unit_cost}</p>}
                            </div>

                            <div className="md:col-span-2 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div className="flex items-center justify-between">
                                    <span className="text-blue-800 font-medium">إجمالي التكلفة:</span>
                                    <span className="text-2xl font-bold text-blue-900">{totalCost} ج</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    {/* Batch Info */}
                    <div className="card">
                        <h3 className="text-lg font-semibold mb-4">معلومات الدفعة (Batch)</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">رقم الدفعة</label>
                                <input
                                    type="text"
                                    value={data.batch_number}
                                    onChange={e => setData('batch_number', e.target.value)}
                                    className="input"
                                    placeholder="سيتم التوليد تلقائياً"
                                />
                                <p className="text-xs text-gray-500 mt-1">اتركه فارغاً للتوليد التلقائي</p>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">اسم المورد</label>
                                <input
                                    type="text"
                                    value={data.supplier_name}
                                    onChange={e => setData('supplier_name', e.target.value)}
                                    className="input"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">تاريخ الشراء</label>
                                <input
                                    type="date"
                                    value={data.purchase_date}
                                    onChange={e => setData('purchase_date', e.target.value)}
                                    className="input"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">تاريخ الإنتاج</label>
                                <input
                                    type="date"
                                    value={data.manufacture_date}
                                    onChange={e => setData('manufacture_date', e.target.value)}
                                    className="input"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">تاريخ الصلاحية</label>
                                <input
                                    type="date"
                                    value={data.expiry_date}
                                    onChange={e => setData('expiry_date', e.target.value)}
                                    className="input"
                                />
                            </div>

                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                                <textarea
                                    value={data.notes}
                                    onChange={e => setData('notes', e.target.value)}
                                    rows="3"
                                    className="input"
                                />
                            </div>

                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-4">
                        <Link href="/admin/stock/in" className="btn-secondary">
                            إلغاء
                        </Link>
                        <button 
                            type="submit" 
                            disabled={processing}
                            className="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg disabled:opacity-50"
                        >
                            <i className="fas fa-check ml-2"></i>
                            حفظ الإضافة
                        </button>
                    </div>

                </form>

            </div>
        </AdminLayout>
    );
}