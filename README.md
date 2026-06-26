<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 🏕️ OutdoorKriss - Sistem Manajemen Penyewaan Perlengkapan Outdoor

OutdoorKriss merupakan aplikasi berbasis web yang dikembangkan untuk mendukung pengelolaan penyewaan perlengkapan outdoor pada setiap kantor cabang. Sistem ini dibangun untuk mengatasi proses penyewaan yang sebelumnya masih dilakukan secara manual melalui pencatatan di buku dan pemesanan melalui WhatsApp.

Melalui sistem ini, penyewa dapat melakukan reservasi secara online, sedangkan admin cabang dapat mengelola penyewaan, stok cabang, permintaan alat ke pusat, serta laporan penyewaan secara terintegrasi. Selain itu, sistem juga dilengkapi dengan fitur notifikasi WhatsApp sebagai pengingat pengembalian barang.

---

## 🚀 Fitur Utama

| 👤 Penyewa                                     | 👨‍💼 Admin Cabang                     |
| ---------------------------------------------- | -------------------------------------- |
| 🔐 Registrasi & Login                          | 📊 Dashboard                           |
| 🔍 Melihat katalog dan ketersediaan produk     | 👥 Mengelola data penyewa              |
| 🛒 Melakukan penyewaan secara online           | 📦 Mengelola stok produk cabang        |
| 💳 Memilih metode pembayaran (Transfer / Cash) | 📋 Mengelola transaksi penyewaan       |
| 📜 Melihat status dan riwayat penyewaan        | 🔄 Mengelola pengembalian barang       |
| 👤 Mengelola profil akun                       | 📤 Mengirim permintaan produk ke pusat |
|                                                | 📈 Melihat laporan penyewaan           |
|                                                | ⚙️ Mengelola profil admin              |

---

## 🔄 Alur Sistem

```text
═══════════════════════════════════════════════
                    Admin Cabang
═══════════════════════════════════════════════
                        │
                        ▼
     Kelola Produk / Tambah Paket / Kelola Stok
                        │
                        ▼
      (Opsional) Mengajukan Permintaan Alat ke Pusat
                        │
                        ▼
═══════════════════════════════════════════════
                    PENYEWA
═══════════════════════════════════════════════
                        │
                        ▼
               Registrasi / Login
                        │
                        ▼
       Melihat Katalog & Ketersediaan Produk
                        │
                        ▼
            Melakukan Penyewaan Online
                        │
                        ▼
         Memilih Metode Pembayaran
                        │
                        ▼
═══════════════════════════════════════════════
                  ADMIN CABANG
═══════════════════════════════════════════════
                        │
                        ▼
            Verifikasi Penyewaan
                        │
                        ▼
         Stok Diperbarui Otomatis
                        │
                        ▼
              Barang Disiapkan
                        │
                        ▼
         Penyewa Menggunakan Barang
                        │
                        ▼
📲 Notifikasi WhatsApp Pengingat Pengembalian
                        │
                        ▼
          Proses Pengembalian Barang
                        │
                        ▼
     Riwayat Transaksi & Laporan Penyewaan
```

---

## 🛠️ Teknologi yang Digunakan

| Teknologi                  | Kegunaan                      |
| -------------------------- | ----------------------------- |
| **Laravel**                | Framework backend             |
| **PHP**                    | Bahasa pemrograman            |
| **MySQL**                  | Database                      |
| **HTML, CSS & JavaScript** | Antarmuka pengguna            |
| **API Fonnte**             | Integrasi notifikasi WhatsApp |

## 📂 Modul Sistem

```text
Dashboard
│
├── Autentikasi
│   ├── Login
│   └── Registrasi
│
├── Penyewa
│   ├── Profil
│   ├── Katalog Produk
│   ├── Penyewaan
│   └── Riwayat Penyewaan
│
├── Admin Cabang
│   ├── Data Penyewa
│   ├── Produk Cabang
│   ├── Paket
│   ├── Penyewaan
│   ├── Riwayat Penyewaan
│   ├── Permintaan Produk
│   └── Laporan
```

---

## 💳 Metode Pembayaran

* Transfer
* Cash

---
## 🚀 Cara Menjalankan Project

### Clone Repository

```bash
git clone https://github.com/TRPL-JBI/TA2026-362258302090-NurWeldaSari.git
```

Masuk ke folder project

```bash
cd TA2026-362258302090-NurWeldaSari
```

Install dependency

```bash
composer install
```

Copy file environment

```bash
cp .env.example .env
```

Generate key aplikasi

```bash
php artisan key:generate
```

Konfigurasi database pada file `.env`

```env
DB_DATABASE=outdoor
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi

```bash
php artisan migrate
```

Jalankan seeder

```bash
php artisan db:seed
```

Jalankan server

```bash
php artisan serve
```

Akses aplikasi melalui

```
http://127.0.0.1:8000
```

---

## 📸 Tampilan Sistem

- Halaman Login
- Dashboard Admin
- Dashboard Penyewa
- Katalog Produk
- Penyewaan
- Pembayaran
- Pengembalian
- Laporan

---

## 🎯 Tujuan Sistem

- Mempermudah proses penyewaan perlengkapan outdoor.
- Membantu admin dalam mengelola transaksi penyewaan.
- Mengurangi pencatatan manual.
- Menyediakan laporan penyewaan secara cepat dan akurat.

---

## 👩‍💻 Pengembang

**Nur Welda Sari**

Program Studi D4 Teknologi Rekayasa Perangkat Lunak

Politeknik Negeri Banyuwangi

---