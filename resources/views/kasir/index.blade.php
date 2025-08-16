@extends('layouts.app')

@section('content')
<div id="app" 
     data-products='@json($products)' 
     data-store-url="{{ route('kasir.store') }}"
     data-csrf-token="{{ csrf_token() }}"
     data-qris-image-url="{{ asset('images/qris-ibu-yana.jpg') }}"
     data-settings='@json($settings)'
     data-categories='@json($categories)'
     data-midtrans-client-key="{{ config('midtrans.client_key') }}"
     data-midtrans-charge-url="{{ route('payment.midtrans.charge') }}"
     data-products-url="{{ route('products.index') }}">
</div>

<audio id="payment-success-sound" src="{{ asset('audio/success.mp3') }}" preload="auto"></audio>

<script src="https://unpkg.com/react@18/umd/react.development.js"></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

@verbatim
<script type="text/babel">
const { useState, useEffect, useMemo } = React;

// Komponen untuk memformat angka ke Rupiah
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

// Komponen Toast Notification
function Toast({ message, type, onClose }) {
    const isSuccess = type === 'success';
    const bgColor = isSuccess ? 'bg-green-100' : 'bg-red-100';
    const textColor = isSuccess ? 'text-green-500' : 'text-red-500';
    const borderColor = isSuccess ? 'border-green-400' : 'border-red-400';

    useEffect(() => {
        const timer = setTimeout(() => {
            onClose();
        }, 3000);

        return () => clearTimeout(timer);
    }, [onClose]);

    return (
        <div className={`fixed top-5 right-5 z-[100] flex items-center w-auto max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-lg border ${borderColor}`} role="alert">
            <div className={`inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${textColor} ${bgColor} rounded-lg`}>
                {isSuccess ? (
                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M16.707 5.293a1 1 0 01.083 1.32l-.083.094L8.414 15l-4.707-4.707a1 1 0 011.32-1.497l.094.083L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path></svg>
                ) : (
                    <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd"></path></svg>
                )}
            </div>
            <div className="ml-3 text-sm font-normal">{message}</div>
            <button onClick={onClose} type="button" className="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8" aria-label="Close">
                <span className="sr-only">Close</span>✕
            </button>
        </div>
    );
}

// Komponen Receipt Preview
function ReceiptPreview({ receiptData, settings }) {
    const currentDate = new Date().toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    return (
        <pre className="p-0 font-mono text-xs leading-normal" style={{ width: '91%' }}>
            <div style={{ textAlign: 'center', whiteSpace: 'pre-wrap', wordBreak: 'break-word'}}>
                <strong>{settings.store_name || 'NAMA TOKO'}</strong>{'\n'}
                {settings.store_address || 'Alamat Toko'}{'\n'}
                Telp: {settings.store_phone || 'No. Telepon'}
            </div>
            {'----------------------------------------\n'}
            <table style={{ width: '91%' }}>
                <tbody>
                    <tr>
                        <td style={{ textAlign: 'left' }}>No Transaksi</td>
                        <td style={{ textAlign: 'right' }}>#{receiptData.transactionId || 'TRX001'}</td>
                    </tr>
                    <tr>
                        <td style={{ textAlign: 'left' }}>Tanggal</td>
                        <td style={{ textAlign: 'right' }}>{currentDate}</td>
                    </tr>
                    <tr>
                        <td style={{ textAlign: 'left' }}>Kasir</td>
                        <td style={{ textAlign: 'right' }}>Admin</td>
                    </tr>
                </tbody>
            </table>
            {'----------------------------------------\n'}
            <table style={{ width: '91%' }}>
                <tbody>
                    {receiptData.items.map((item, index) => (
                        <React.Fragment key={index}>
                            <tr><td colSpan="2" style={{ textAlign: 'left' }}>{item.name}</td></tr>
                            <tr>
                                <td style={{ textAlign: 'left' }}>&nbsp;&nbsp;{item.quantity} x {formatRupiah(item.price)}</td>
                                <td style={{ textAlign: 'right' }}>{formatRupiah(item.subtotal)}</td>
                            </tr>
                        </React.Fragment>
                    ))}
                </tbody>
            </table>
            {'----------------------------------------\n'}
            <table style={{ width: '91%' }}>
                <tbody>
                    <tr>
                        <td style={{ textAlign: 'left' }}><strong>TOTAL</strong></td>
                        <td style={{ textAlign: 'right' }}><strong>{formatRupiah(receiptData.total)}</strong></td>
                    </tr>
                    <tr>
                        <td style={{ textAlign: 'left' }}>Bayar ({receiptData.paymentMethod === 'cash' ? 'Tunai' : 'QRIS'})</td>
                        <td style={{ textAlign: 'right' }}>{formatRupiah(receiptData.paid)}</td>
                    </tr>
                    {receiptData.change > 0 && (
                        <tr>
                            <td style={{ textAlign: 'left' }}>Kembalian</td>
                            <td style={{ textAlign: 'right' }}>{formatRupiah(receiptData.change)}</td>
                        </tr>
                    )}
                </tbody>
            </table>
            {'----------------------------------------\n'}
            <div style={{ textAlign: 'center', whiteSpace: 'pre-wrap', wordBreak: 'break-word', width: '91%'}}>
                Terima kasih atas kunjungan Anda!{'\n'}
                Barang yang sudah dibeli{'\n'}
                tidak dapat dikembalikan
            </div>
        </pre>
    );
}

// Modal Print Receipt
function PrintReceiptModal({ isOpen, receiptData, onClose, settings }) {
    const handleDownloadPDF = () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            unit: 'mm',
            format: [57, 200] // Ukuran kertas PDF diubah ke 57mm
        });

        // --- VARIABEL UNTUK KONTROL LAYOUT ---
        let yPosition = 10;
        const pageWidth = 57; // Diubah ke 57mm
        const leftMargin = 3;
        const rightMargin = pageWidth - 3; // Posisi rata kanan
        
        // --- FUNGSI BANTU UNTUK GARIS PEMISAH ---
        const drawLine = () => {
            yPosition += 2;
            doc.setLineDashPattern([1, 1], 0); // Garis putus-putus
            doc.line(leftMargin, yPosition, rightMargin, yPosition);
            yPosition += 4;
            doc.setLineDashPattern([], 0); // Kembali ke garis solid
        };

        // --- HEADER ---
        doc.setFont(undefined, 'bold');
        doc.setFontSize(10);
        doc.text(settings.store_name || 'NAMA TOKO', pageWidth / 2, yPosition, { align: 'center' });
        yPosition += 4;
        doc.setFont(undefined, 'normal');
        doc.setFontSize(7);
        doc.text(settings.store_address || 'Alamat Toko', pageWidth / 2, yPosition, { align: 'center' });
        yPosition += 3;
        doc.text(`Telp: ${settings.store_phone || 'No. Telepon'}`, pageWidth / 2, yPosition, { align: 'center' });
        
        drawLine();

        // --- INFO TRANSAKSI (DENGAN RATA KANAN) ---
        const currentDate = new Date().toLocaleDateString('id-ID', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
        
        doc.setFontSize(8);
        // Baris No Transaksi
        doc.text('No Transaksi', leftMargin, yPosition);
        doc.text(`#${receiptData.transactionId || 'TRX001'}`, rightMargin, yPosition, { align: 'right' });
        yPosition += 4;
        // Baris Tanggal
        doc.text('Tanggal', leftMargin, yPosition);
        doc.text(currentDate, rightMargin, yPosition, { align: 'right' });
        yPosition += 4;
        // Baris Kasir
        doc.text('Kasir', leftMargin, yPosition);
        doc.text('Admin', rightMargin, yPosition, { align: 'right' });

        drawLine();

        // --- DAFTAR ITEM ---
        receiptData.items.forEach(item => {
            // Nama item
            doc.text(item.name, leftMargin, yPosition);
            yPosition += 4;
            // Kuantitas & harga satuan (rata kiri)
            doc.text(`  ${item.quantity} x ${formatRupiah(item.price)}`, leftMargin, yPosition);
            // Subtotal (rata kanan)
            doc.text(formatRupiah(item.subtotal), rightMargin, yPosition, { align: 'right' });
            yPosition += 4;
        });

        drawLine();

        // --- TOTALS ---
        // Baris TOTAL
        doc.setFont(undefined, 'bold');
        doc.text('TOTAL', leftMargin, yPosition);
        doc.text(formatRupiah(receiptData.total), rightMargin, yPosition, { align: 'right' });
        yPosition += 4;
        doc.setFont(undefined, 'normal');
        // Baris Bayar
        doc.text(`Bayar (${receiptData.paymentMethod === 'cash' ? 'Tunai' : 'QRIS'})`, leftMargin, yPosition);
        doc.text(formatRupiah(receiptData.paid), rightMargin, yPosition, { align: 'right' });
        yPosition += 4;
        // Baris Kembalian (jika ada)
        if (receiptData.change > 0) {
            doc.text('Kembalian', leftMargin, yPosition);
            doc.text(formatRupiah(receiptData.change), rightMargin, yPosition, { align: 'right' });
            yPosition += 4;
        }
        
        drawLine();

        // --- FOOTER ---
        doc.setFontSize(7);
        doc.text('Terima kasih atas kunjungan Anda!', pageWidth / 2, yPosition, { align: 'center' });
        yPosition += 3;
        doc.text('Barang yang sudah dibeli', pageWidth / 2, yPosition, { align: 'center' });
        yPosition += 3;
        doc.text('tidak dapat dikembalikan', pageWidth / 2, yPosition, { align: 'center' });

        // --- SIMPAN PDF ---
        doc.save(`struk_${receiptData.transactionId || 'TRX001'}.pdf`);
    };

    const handleDirectPrint = () => {
        const printContent = document.getElementById('receipt-only-content').innerHTML;

        const printStyles = `
            <style>
                @media print {
                    @page {
                        size: 57mm auto; /* Ukuran kertas diubah ke 57mm */
                        margin: 0;
                    }
                    html, body {
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    body {
                        width: 57mm; /* Diubah ke 57mm */
                        box-sizing: border-box !important;
                        padding-left: 3mm !important;
                        padding-right: 1mm !important;
                    }
                    body * {
                        font-family: monospace !important;
                        font-size: 10px !important;
                        line-height: 1.2 !important;
                    }
                    table {
                        width: 100%;
                    }
                    strong {
                        font-weight: bold;
                    }
                    /* PERBAIKAN UTAMA: Sembunyikan semua elemen modal saat print */
                    .modal-header,
                    .modal-buttons,
                    .modal-close,
                    .preview-container,
                    .preview-header,
                    .print-actions,
                    h3, h4,
                    button,
                    .grid,
                    .border-dashed,
                    .bg-gray-50,
                    .text-center:has(button),
                    .flex.justify-between,
                    .grid.grid-cols-2 {
                        display: none !important;
                    }
                    /* Pastikan hanya konten struk yang tampil */
                    #receipt-only-content {
                        display: block !important;
                    }
                }
                
                /* Style default untuk preview (tidak berubah) */
                .modal-header,
                .modal-buttons,
                .preview-container,
                .preview-header {
                    display: block;
                }
            </style>
        `;
        
        const iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.left = '-9999px';
        document.body.appendChild(iframe);

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write('<html><head>');
        doc.write(printStyles);
        doc.write('</head><body>');
        doc.write(printContent);
        doc.write('</body></html>');
        doc.close();
        
        setTimeout(() => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }, 100); // Sedikit ditambah delay untuk memastikan styles loaded
        
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 1000); // Ditambah delay untuk cleanup
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
            <div className="bg-white rounded-lg shadow-xl p-6 w-full max-w-md max-h-screen overflow-y-auto">
                <div className="flex justify-between items-center mb-4 modal-header">
                    <h3 className="text-xl font-bold">Cetak Struk</h3>
                    <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-2xl modal-close">&times;</button>
                </div>
                
                {/* Receipt Preview */}
                <div className="border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50 preview-container">
                    <h4 className="text-sm font-semibold mb-2 text-center preview-header">Preview Struk</h4>
                    <div id="receipt-preview" className="border border-dashed border-gray-400 bg-white">
                        <ReceiptPreview receiptData={receiptData} settings={settings} />
                    </div>
                </div>

                {/* Hidden content untuk print - hanya konten struk */}
                <div id="receipt-only-content" style={{ display: 'none' }}>
                    <ReceiptPreview receiptData={receiptData} settings={settings} />
                </div>

                {/* Action Buttons */}
                <div className="grid grid-cols-2 gap-3 modal-buttons">
                    <button 
                        onClick={handleDownloadPDF}
                        className="flex items-center justify-center gap-2 bg-blue-500 text-white font-semibold py-3 px-4 rounded-lg hover:bg-blue-600 transition"
                    >
                        <i className="fa-solid fa-download"></i>
                        Simpan PDF
                    </button>
                    <button 
                        onClick={handleDirectPrint}
                        className="flex items-center justify-center gap-2 bg-green-500 text-white font-semibold py-3 px-4 rounded-lg hover:bg-green-600 transition"
                    >
                        <i className="fa-solid fa-print"></i>
                        Cetak
                    </button>
                </div>
                
                <div className="mt-4 text-center modal-buttons">
                    <button 
                        onClick={onClose}
                        className="text-gray-500 hover:text-gray-700 text-sm"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    );
}

// Komponen Utama Aplikasi Kasir
function App() {
    const appElement = document.getElementById('app');
    const initialProducts = JSON.parse(appElement.dataset.products);
    const storeUrl = appElement.dataset.storeUrl;
    const csrfToken = appElement.dataset.csrfToken;
    const qrisImageUrl = appElement.dataset.qrisImageUrl;
    const settings = JSON.parse(appElement.dataset.settings);
    const categories = JSON.parse(appElement.dataset.categories);
    const midtransClientKey = appElement.dataset.midtransClientKey;
    const midtransChargeUrl = appElement.dataset.midtransChargeUrl;
    const productsUrl = appElement.dataset.productsUrl;

    const [cart, setCart] = useState([]);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isPrintModalOpen, setIsPrintModalOpen] = useState(false);
    const [lastReceiptData, setLastReceiptData] = useState(null);
    const [isMobileCartOpen, setIsMobileCartOpen] = useState(false);
    const [paymentMethod, setPaymentMethod] = useState(null);
    const [amountPaid, setAmountPaid] = useState('');
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [toast, setToast] = useState(null);
    const [isProcessingPayment, setIsProcessingPayment] = useState(false);

    const quickCashOptions = [5000, 10000, 20000, 50000, 100000];

    const total = useMemo(() => cart.reduce((sum, item) => sum + item.price * item.quantity, 0), [cart]);
    const change = useMemo(() => (parseFloat(amountPaid.replace(/\./g, '')) || 0) - total, [amountPaid, total]);

    const handleProductClick = (product) => {
        setCart(prevCart => {
            const existingItem = prevCart.find(item => item.id === product.id);
            if (existingItem) {
                return prevCart.map(item =>
                    item.id === product.id ? { ...item, quantity: item.quantity + 1 } : item
                );
            }
            return [...prevCart, { ...product, quantity: 1 }];
        });
    };

    const handleQuantityChange = (productId, delta) => {
        setCart(prevCart => {
            return prevCart.map(item =>
                item.id === productId ? { ...item, quantity: Math.max(1, item.quantity + delta) } : item
            ).filter(item => item.quantity > 0);
        });
    };
    
    const handleRemoveItem = (productId) => setCart(prevCart => prevCart.filter(item => item.id !== productId));

    const handleOpenPaymentModal = () => {
        if (cart.length > 0) {
            setIsModalOpen(true);
        }
    };

    const handleAmountPaidChange = (value, mode = 'set') => {
        const currentValue = parseFloat(amountPaid.replace(/\./g, '')) || 0;
        const newValue = parseFloat(String(value).replace(/\D/g, ''));

        let finalValue = 0;
        if (mode === 'add') {
            finalValue = currentValue + newValue;
        } else {
            finalValue = newValue;
        }
        
        setAmountPaid(finalValue > 0 ? new Intl.NumberFormat('id-ID').format(finalValue) : '');
    };

    const handleReset = () => {
        setCart([]);
        setAmountPaid('');
        setPaymentMethod(null);
        setIsModalOpen(false);
        setIsMobileCartOpen(false);
    };

    const handleMidtransPayment = () => {
        setIsProcessingPayment(true);

        // Siapkan item_details untuk Midtrans
        const midtransItems = cart.map(item => ({
            id: item.id,
            price: item.price,
            quantity: item.quantity,
            name: item.name
        }));

        fetch(midtransChargeUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ total: total, items: midtransItems })
        })
        .then(res => res.json())
        .then(data => {
            if (data.snap_token) {
                // Buka popup pembayaran Snap
                snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        console.log('success', result);
                        // Simpan transaksi di DB setelah pembayaran berhasil
                        handlePaymentSuccess(result.order_id, result);
                        setIsProcessingPayment(false);
                    },
                    onPending: function(result){
                        console.log('pending', result);
                        // Simpan transaksi di DB dengan status pending
                        handleSubmitOrder(result.order_id, result);
                        setIsProcessingPayment(false);
                    },
                    onError: function(result){
                        console.log('error', result);
                        setToast({ message: 'Pembayaran Gagal.', type: 'error' });
                        setIsProcessingPayment(false);
                    },
                    onClose: function(){
                        // Jika popup ditutup tanpa pembayaran
                        setToast({ message: 'Anda menutup popup pembayaran.', type: 'info' });
                        setIsProcessingPayment(false);
                    }
                });
            } else {
                setToast({ message: data.message || 'Gagal memulai pembayaran.', type: 'error' });
                setIsProcessingPayment(false);
            }
        })
        .catch(err => {
            console.error(err);
            setToast({ message: 'Terjadi kesalahan jaringan.', type: 'error' });
            setIsProcessingPayment(false);
        });
    };

    const handlePaymentSuccess = (orderId, paymentResult) => {
        // Panggil fungsi untuk memainkan suara
        playSound('success');

        // Lanjutkan dengan proses handleSubmitOrder
        handleSubmitOrder(orderId, paymentResult);
    };

    const playSound = (soundType) => {
        const audio = document.getElementById('payment-success-sound');
        if (audio) {
            audio.play().catch(error => {
                console.error("Autoplay was prevented:", error);
                // Anda bisa menambahkan notifikasi visual di sini jika autoplay gagal
            });
        }
    };
    
    const handleSubmitOrder = (orderId, paymentResult = null, event = null) => {
        if (event) {
            event.preventDefault();
        }

        if (paymentMethod === 'cash') {
            playSound('success');
        }
        
        const paidAmount = paymentMethod === 'qris' ? total : (parseFloat(amountPaid.replace(/\./g, '')) || 0);
        const finalChange = paymentMethod === 'qris' ? 0 : change;

        if (paymentMethod === 'cash' && paidAmount < total) {
            setToast({ message: 'Jumlah bayar kurang dari total tagihan.', type: 'error' });
            return;
        }

        const orderData = {
            total: total,
            bayar: paidAmount,
            kembalian: finalChange,
            metode_pembayaran: paymentMethod,
            items: cart.map(item => ({ id: item.id, qty: item.quantity, price: item.price, subtotal: item.price * item.quantity })),
            invoice_number: orderId || `INV-${Date.now()}`,
            status: paymentResult ? paymentResult.transaction_status : 'paid',
        };

        fetch(storeUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) throw data;
            
            // Prepare receipt data
            const receiptData = {
                transactionId: data.transaction_id || Date.now(),
                items: cart.map(item => ({
                    name: item.name,
                    quantity: item.quantity,
                    price: item.price,
                    subtotal: item.price * item.quantity
                })),
                total: total,
                paid: paidAmount,
                change: finalChange,
                paymentMethod: paymentMethod
            };
            
            setLastReceiptData(receiptData);
            setToast({ message: data.message || 'Transaksi berhasil!', type: 'success' });
            setIsModalOpen(false);
            setIsPrintModalOpen(true);
        })
        .catch((error) => {
            console.error('Error:', error);
            setToast({ message: error.message || 'Gagal menyimpan transaksi.', type: 'error' });
        });
    };

    const handleTransactionComplete = () => {
        setIsPrintModalOpen(false);
        handleReset();
    };

    const filteredProducts = useMemo(() => {
        return initialProducts.filter(product => {
            const matchesSearchTerm = product.name.toLowerCase().includes(searchTerm.toLowerCase());
            
            if (selectedCategory === null) {
                return matchesSearchTerm;
            }
            
            return matchesSearchTerm && product.category_id === selectedCategory;
        });
    }, [initialProducts, searchTerm, selectedCategory]);

    // Komponen Keranjang (untuk reusability)
    const CartComponent = ({ isMobile }) => (
        <div className={`w-full p-4 bg-white shadow-lg flex flex-col ${isMobile ? 'h-full' : 'h-screen'}`}>
            <div className="flex items-center justify-between border-b pb-3">
                <h2 className="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">Keranjang</h2>
                {isMobile && <button onClick={() => setIsMobileCartOpen(false)} className="text-gray-500 hover:text-gray-800"><i className="fa-solid fa-times text-xl"></i></button>}
            </div>
            {cart.length === 0 ? (
                <div className="flex-grow flex flex-col items-center justify-center text-gray-400"><i className="fa-solid fa-cart-shopping text-5xl"></i><p className="mt-4">Keranjang masih kosong</p></div>
            ) : (
                <div className="flex-grow overflow-y-auto my-4 pr-2">
                    {cart.map(item => (
                        <div key={item.id} className="flex items-center gap-3 mb-3">
                            <img src={item.image ? `/storage/${item.image}` : 'https://placehold.co/100x100/e2e8f0/64748b?text=...'} alt={item.name} className="w-14 h-14 object-cover rounded-md" />
                            <div className="flex-grow"><p className="text-sm font-semibold text-gray-800">{item.name}</p><p className="text-xs text-gray-500">{formatRupiah(item.price)}</p></div>
                            <div className="flex items-center gap-2"><button onClick={() => handleQuantityChange(item.id, -1)} className="w-6 h-6 bg-gray-200 rounded-full text-gray-700 hover:bg-gray-300 flex items-center justify-center">-</button><span>{item.quantity}</span><button onClick={() => handleQuantityChange(item.id, 1)} className="w-6 h-6 bg-gray-200 rounded-full text-gray-700 hover:bg-gray-300">+</button></div>
                            <p className="w-20 text-right font-semibold">{formatRupiah(item.price * item.quantity)}</p>
                            <button onClick={() => handleRemoveItem(item.id)} className="text-red-500 hover:text-red-700"><i className="fa-solid fa-trash-can"></i></button>
                        </div>
                    ))}
                </div>
            )}
            <div className="border-t pt-4"><div className="flex justify-between items-center text-lg font-bold"><span>Total</span><span>{formatRupiah(total)}</span></div><button onClick={handleOpenPaymentModal} disabled={cart.length === 0} className="w-full mt-4 bg-amber-500 text-white font-semibold py-3 rounded-lg shadow-md hover:bg-amber-600 transition disabled:bg-gray-300 disabled:cursor-not-allowed"><i className="fa-solid fa-money-bill-wave"></i> Bayar</button></div>
        </div>
    );

    return (
        <div className="bg-gray-50 rounded-lg font-jakarta h-screen overflow-hidden relative">
            {toast && <Toast message={toast.message} type={toast.type} onClose={() => setToast(null)} />}
            
            <div className="flex flex-col lg:flex-row h-full">
                {/* Product List */}
                <div className="w-full lg:w-3/5 p-4 flex flex-col h-full">
                    <header className="mb-4">
                        <h1 className="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">Pilih Produk</h1>
                        <div className="relative mt-2">
                            <i className="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Cari produk..." value={searchTerm} onChange={(e) => setSearchTerm(e.target.value)} className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
                        </div>
                        <nav className="mt-4 -mb-2">
                            <div className="flex items-center space-x-2 overflow-x-auto pb-2">
                                {/* Tombol "Semua" */}
                                <button 
                                    onClick={() => setSelectedCategory(null)}
                                    className={`px-4 py-2 text-sm font-semibold rounded-full flex-shrink-0 transition ${selectedCategory === null ? 'bg-amber-500 text-white shadow' : 'bg-white text-gray-700 hover:bg-gray-100 border'}`}>
                                    Semua
                                </button>
                                {/* Tombol untuk setiap kategori */}
                                {categories.map(category => (
                                    <button 
                                        key={category.id} 
                                        onClick={() => setSelectedCategory(category.id)}
                                        className={`px-4 py-2 text-sm font-semibold rounded-full flex-shrink-0 transition ${selectedCategory === category.id ? 'bg-amber-500 text-white shadow' : 'bg-white text-gray-700 hover:bg-gray-100 border'}`}>
                                        {category.name}
                                    </button>
                                ))}
                            </div>
                        </nav>
                    </header>
                    {initialProducts.length === 0 ? (
                        <div className="text-center py-16 bg-white rounded-lg shadow-sm h-full flex flex-col justify-center">
                            <i className="fa-solid fa-box-open text-4xl text-gray-300"></i>
                            <h3 className="mt-2 text-sm font-medium text-gray-900">Belum Ada Produk</h3>
                            <p className="mt-1 text-sm text-gray-500">Silakan tambahkan produk terlebih dahulu.</p>
                            <a href={productsUrl} className="mt-4 text-center text-sm font-medium text-amber-600 hover:text-amber-500">
                                Tambah Produk
                            </a> 
                        </div>
                    ) : filteredProducts.length === 0 ? (
                        // Jika kategori dipilih tapi tidak ada produk
                        <div className="text-center py-16 bg-white rounded-lg shadow-sm h-full flex flex-col justify-center">
                            <i className="fa-solid fa-box-open text-4xl text-gray-300"></i>
                            <h3 className="mt-2 text-sm font-medium text-gray-900">Tidak Ada Produk</h3>
                            <p className="mt-1 text-sm text-gray-500">Tidak ada menu tersedia di kategori ini.</p>
                        </div>
                    ) : (
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            {filteredProducts.map(product => (
                                <div 
                                    key={product.id} 
                                    onClick={() => handleProductClick(product)} 
                                    className="group bg-white rounded-xl shadow-sm border border-gray-200/80 cursor-pointer overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-amber-300"
                                >
                                    <div className="h-28 w-full overflow-hidden">
                                        <img 
                                            src={product.image ? `/storage/${product.image}` : 'https://placehold.co/300x200/e2e8f0/64748b?text=No+Image'} 
                                            alt={product.name} 
                                            className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" 
                                        />
                                    </div>
                                    <div className="p-3 text-center">
                                        <h3 className="text-sm font-semibold text-gray-700 truncate">{product.name}</h3>
                                        <p className="text-base text-amber-500 font-bold mt-1">{formatRupiah(product.price)}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Order Details (Desktop) */}
                <div className="w-full lg:w-2/5 hidden lg:flex">
                    <CartComponent isMobile={false} />
                </div>
            </div>

            {/* Floating Action Button (Mobile) */}
            <div className="lg:hidden fixed bottom-6 right-6 z-30">
                <button onClick={() => setIsMobileCartOpen(true)} className="bg-amber-500 text-white w-16 h-16 rounded-full shadow-lg flex items-center justify-center">
                    <i className="fa-solid fa-cart-shopping text-2xl"></i>
                    {cart.length > 0 && <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center">{cart.length}</span>}
                </button>
            </div>

            {/* Mobile Cart Overlay */}
            <div className={`lg:hidden fixed inset-0 z-40 bg-white transform transition-transform duration-300 ease-in-out ${isMobileCartOpen ? 'translate-x-0' : 'translate-x-full'}`}>
                <CartComponent isMobile={true} />
            </div>

            {/* Payment Modal */}
            {isModalOpen && (
                <div className="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 transition-opacity">
                    <div className="bg-white rounded-lg shadow-xl p-6 w-full max-w-md transform transition-all">
                        <div className="flex justify-between items-center mb-4"><h3 className="text-xl font-bold">Pilih Metode Pembayaran</h3><button onClick={handleReset} className="text-gray-400 hover:text-gray-600">&times;</button></div>
                        {!paymentMethod ? (
                            <div className="grid grid-cols-2 gap-4">
                                <button onClick={() => setPaymentMethod('cash')} className="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-amber-100 border-2 border-transparent hover:border-amber-400 transition"><i className="fa-solid fa-money-bill-1-wave text-4xl text-green-500 mb-2"></i><span className="font-semibold">Tunai</span></button>
                                <button onClick={() => setPaymentMethod('qris')} className="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-amber-100 border-2 border-transparent hover:border-amber-400 transition"><i className="fa-solid fa-qrcode text-4xl text-blue-500 mb-2"></i><span className="font-semibold">QRIS</span></button>
                            </div>
                        ) : paymentMethod === 'qris' ? (
                            <div>
                                <button onClick={() => setPaymentMethod(null)} className="text-sm text-amber-600 mb-2">&larr; Kembali</button>
                                <div className="text-center">
                                    <h4 className="font-semibold">Konfirmasi Pembayaran QRIS</h4>
                                    <p className="text-sm text-gray-500 mb-4">Total: <span className="font-bold">{formatRupiah(total)}</span></p>
                                    <button 
                                        onClick={handleMidtransPayment} 
                                        disabled={isProcessingPayment}
                                        className="w-full bg-blue-500 text-white font-bold py-3 rounded-lg hover:bg-blue-600 transition disabled:bg-gray-400">
                                        {isProcessingPayment ? 'Memproses...' : 'Lanjutkan ke Pembayaran'}
                                    </button>
                                </div>
                            </div>
                        ) : (
                            <div>
                                <button onClick={() => setPaymentMethod(null)} className="text-sm text-amber-600 mb-2">&larr; Kembali</button>
                                <div className="bg-amber-50 p-4 rounded-lg text-center mb-4">
                                    <p className="text-sm text-amber-800">Total Tagihan</p>
                                    <p className="text-3xl font-bold text-amber-900">{formatRupiah(total)}</p>
                                </div>
                                <form onSubmit={(e) => handleSubmitOrder(null, null, e)}>
                                    <div className="mb-2">
                                        <label htmlFor="amountPaid" className="block text-sm font-medium text-gray-700">Jumlah Dibayar</label>
                                        <div className="relative mt-1">
                                            <input 
                                                type="text" 
                                                id="amountPaid" 
                                                value={amountPaid} 
                                                onChange={(e) => handleAmountPaidChange(e.target.value, 'set')} 
                                                placeholder="Masukkan nominal" 
                                                className="block w-full text-center text-lg py-2 border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500" 
                                                autoFocus 
                                            />
                                            {amountPaid && (
                                                <button 
                                                    type="button" 
                                                    onClick={() => setAmountPaid('')} 
                                                    className="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 hover:text-red-500 text-lg font-bold"
                                                >
                                                    &times;
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                    
                                    <div className="flex flex-wrap gap-2 mb-4">
                                        {quickCashOptions.map(amount => (
                                            <button 
                                                type="button" 
                                                key={amount} 
                                                onClick={() => handleAmountPaidChange(amount, 'add')} 
                                                className="flex-1 bg-gray-200 text-gray-700 py-1 px-2 rounded-md hover:bg-gray-300 text-sm"
                                            >
                                                +{formatRupiah(amount)}
                                            </button>
                                        ))}
                                        <button 
                                            type="button" 
                                            onClick={() => handleAmountPaidChange(total, 'set')} 
                                            className="flex-1 bg-amber-200 text-amber-800 py-1 px-2 rounded-md hover:bg-amber-300 text-sm font-semibold"
                                        >
                                            Uang Pas
                                        </button>
                                    </div>
                                    
                                    <div className="flex justify-between items-center text-sm mb-6">
                                        <span className="text-gray-600">Kembalian</span>
                                        <span className={`font-bold ${change < 0 ? 'text-red-500' : 'text-green-600'}`}>{formatRupiah(change)}</span>
                                    </div>
                                    <button type="submit" disabled={change < 0} className="w-full bg-amber-500 text-white font-bold py-2 rounded-lg hover:bg-amber-600 transition disabled:bg-gray-300">
                                        Konfirmasi Pembayaran
                                    </button>
                                </form>
                            </div>
                        )}
                    </div>
                </div>
            )}

            {/* Print Receipt Modal */}
            <PrintReceiptModal 
                isOpen={isPrintModalOpen}
                receiptData={lastReceiptData}
                onClose={handleTransactionComplete}
                settings={settings}
            />
        </div>
    );
}

ReactDOM.render(<App />, document.getElementById('app'));
</script>
@endverbatim

@endsection