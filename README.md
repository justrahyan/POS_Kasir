# POS Kasir - Toko Ibu Yana

Aplikasi Point of Sale (POS) berbasis web yang modern dan ringan, dibangun menggunakan Laravel sebagai backend dan React.js sebagai frontend. Sistem ini dirancang untuk memfasilitasi transaksi penjualan yang cepat, manajemen produk, serta mendukung pembayaran tunai dan digital (QRIS) melalui integrasi dengan Midtrans.

## Fitur Utama

-   **🖥️ Halaman Kasir Interaktif:** Tampilan produk berbasis *grid*, keranjang belanja dinamis, pencarian, dan filter kategori horizontal yang responsif.
-   **⚙️ Pengaturan Dinamis:** Halaman khusus untuk mengelola informasi toko (Nama, Alamat, No. Telepon) yang akan tampil di struk.
-   **💳 Multi-Metode Pembayaran:**
    -   **Tunai:** Dengan input jumlah bayar, tombol *quick cash* akumulatif, dan perhitungan kembalian otomatis.
    -   **QRIS:** Integrasi dengan **Midtrans Snap** untuk menampilkan *popup* pembayaran QRIS yang bisa di-scan oleh semua aplikasi e-wallet dan m-banking.
-   **📄 Struk & PDF:**
    -   Cetak struk termal dengan format 57mm yang rapi.
    -   Unduh bukti transaksi dalam format PDF.
-   **📊 Laporan Transaksi:**
    -   Menampilkan riwayat transaksi dengan **pagination**.
    -   Fitur **sorting** berdasarkan No. Invoice, Tanggal, atau Total.
    -   Fitur **filter** berdasarkan metode pembayaran (Semua, Tunai, QRIS).
    -   Tampilan detail item untuk setiap transaksi.

## Tumpukan Teknologi (Tech Stack)

-   **Backend:** Laravel 11
-   **Frontend:** React.js (via CDN di dalam Blade)
-   **Database:** MySQL
-   **Styling:** Tailwind CSS & Alpine.js
-   **Payment Gateway:** Midtrans (Snap API)
-   **PDF Generation:** jsPDF

---

## Setup & Instalasi Proyek

#### 1. Persyaratan
-   PHP 8.2+
-   Composer
-   Node.js & NPM
-   Database MySQL

#### 2. Instalasi Backend (Laravel)
1.  **Clone repositori:**
    ```bash
    git clone [URL_REPOSITORY_ANDA]
    cd [NAMA_FOLDER_PROYEK]
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    npm install
    npm run build
    ```

3.  **Setup file `.env`:**
    * Salin `.env.example` menjadi `.env`.
        ```bash
        cp .env.example .env
        ```
    * Hasilkan kunci aplikasi:
        ```bash
        php artisan key:generate
        ```
    * Konfigurasi koneksi database Anda (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    * Konfigurasi Midtrans:
        ```env
        MIDTRANS_MERCHANT_ID=...
        MIDTRANS_CLIENT_KEY=...
        MIDTRANS_SERVER_KEY=...
        MIDTRANS_IS_PRODUCTION=false
        ```

4.  **Jalankan Migrasi Database:**
    Perintah ini akan membuat semua tabel yang dibutuhkan.
    ```bash
    php artisan migrate
    ```

5.  **Jalankan Server Lokal:**
    ```bash
    php artisan serve
    ```
    Aplikasi Anda sekarang berjalan di `http://127.0.0.1:8000`.

---

## Alur Kerja Sistem

### Backend

#### Database & Model
-   **`transactions`**: Menyimpan data induk transaksi, termasuk `invoice_number`, `metode_pembayaran`, dan `status` (`pending`, `paid`, `failed`).
-   **`transaction_details`**: Menyimpan item-item yang dibeli dalam satu transaksi.
-   **`settings`**: Menyimpan pengaturan toko (nama, alamat, dll) dengan sistem `key-value`.
-   Model lain yang digunakan: `Product`, `Category`, `User`.

#### Routes (`routes/web.php`)
-   `/kasir`: (`TransactionController@index`) Menampilkan halaman kasir.
-   `/kasir/store`: (`TransactionController@store`) Menyimpan transaksi tunai.
-   `/payment/midtrans/charge`: (`PaymentController@charge`) Endpoint untuk membuat Snap Token Midtrans.
-   `/payment/midtrans/notification`: (`PaymentController@notificationHandler`) Endpoint **webhook** untuk menerima notifikasi dari Midtrans (dikecualikan dari CSRF & `auth`).
-   `/laporan`: (`TransactionController@laporan`) Menampilkan halaman laporan.
-   `/settings`: (`SettingController`) Mengelola halaman pengaturan toko.

#### Controllers
-   `TransactionController`: Mengelola logika untuk halaman kasir dan laporan (mengambil data, filter, sorting, pagination).
-   `PaymentController`: Menangani semua komunikasi dengan API Midtrans, mulai dari pembuatan token hingga penanganan notifikasi *webhook*.
-   `SettingController`: Mengelola penyimpanan data dari form Pengaturan Toko.

### Frontend (React di Blade)

Logika frontend utama terletak di `resources/views/kasir/index.blade.php`.

-   **Data Passing:** Data dari Laravel (produk, kategori, settings, URL) dilewatkan ke React melalui *data attributes* pada `<div id="app">`.
-   **State Management:** Menggunakan hook `useState` untuk mengelola data dinamis seperti keranjang (`cart`), filter, dan status modal.
-   **Alur Pembayaran QRIS:**
    1.  Frontend memanggil endpoint `/payment/midtrans/charge`.
    2.  Backend merespons dengan `snap_token`.
    3.  Frontend memanggil `snap.pay(snap_token)` untuk membuka popup Midtrans.
    4.  Setelah pembayaran berhasil (`onSuccess`), `handleSubmitOrder` dipanggil untuk menyimpan transaksi ke database lokal.
    5.  Secara terpisah, *webhook* dari Midtrans akan meng-update status transaksi di database dari `pending` menjadi `paid`.
-   **Cetak Struk:**
    -   **Print:** Membuat `iframe` tersembunyi, menyuntikkan HTML struk dan CSS khusus printer, lalu memanggil `window.print()`.
    -   **PDF:** Menggunakan `jsPDF` untuk membuat dokumen PDF baris per baris secara manual untuk layout yang presisi.