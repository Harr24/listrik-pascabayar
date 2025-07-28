# ⚡ Aplikasi Pembayaran Listrik Pascabayar (PHP Native)

Aplikasi web sederhana yang dibangun menggunakan PHP Native dan database MySQL untuk mengelola tagihan listrik pascabayar. Aplikasi ini memiliki dua peran utama: **Admin** dan **Pelanggan**.

---

### Fitur Aplikasi

**Panel Admin:**

- Dashboard dengan statistik (total pelanggan, tagihan belum lunas, grafik pendapatan).
- Widget keluhan terbaru & daftar teknisi aktif di dashboard untuk aksi cepat.
- Manajemen Pelanggan (CRUD).
- Manajemen Golongan Tarif (CRUD).
- **Manajemen Area Layanan (CRUD)** untuk membatasi area pendaftaran.
- **Manajemen Teknisi (CRUD)** untuk mengelola data teknisi.
- Input Pemakaian & Generate Tagihan Otomatis.
- Manajemen & Detail Semua Tagihan dengan Filter per daya (VA).
- Konfirmasi Pembayaran.
- **Sistem Keluhan** untuk melihat dan membalas tiket dari pelanggan.
- Laporan bulanan yang bisa diekspor ke Excel (.csv), lengkap dengan rekapitulasi total.
- Manajemen Event/Berita di Halaman Utama.
- Ubah Profil & Password.

**Panel Pelanggan:**

- Dashboard modern dengan ringkasan tagihan terbaru.
- Riwayat & Detail Tagihan.
- Proses Pembayaran (upload bukti transfer).
- **Sistem Keluhan** (membuat tiket, mengirim pesan & bukti, melihat riwayat).
- Halaman **Hubungi Teknisi** yang terintegrasi dengan WhatsApp.

**Halaman Publik (Homepage):**

- Desain modern dengan background slider dan animasi saat scroll (AOS).
- Menampilkan statistik dinamis: jumlah pengguna, teknisi, daftar layanan daya, dan **area cakupan** yang ter-update otomatis.
- Sistem registrasi yang dibatasi berdasarkan **Area Layanan**.
- Sistem login dan **Lupa Password**.
- Menampilkan event/berita yang dikelola oleh admin.

---

### Instalasi & Konfigurasi

1.  **Clone Repository**

    ```bash
    git clone [https://github.com/Harr24/listrik-pascabayar.git](https://github.com/Harr24/listrik-pascabayar.git)
    ```

2.  **Pindahkan ke htdocs**
    Pindahkan folder `listrik-pascabayar` ke dalam direktori `htdocs` XAMPP Anda.

3.  **Import Database**

    - Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
    - Buat database baru dengan nama `listrikaja`.
    - Pilih database `listrikaja`, buka tab **Import**.
    - Pilih file `listrikaja.sql` yang ada di dalam repository ini.
    - Klik **Go**.

4.  **Konfigurasi Koneksi**
    - Buka file `config/database.php`.
    - Pastikan detail koneksi (host, user, password, nama db) sudah sesuai dengan pengaturan XAMPP Anda.

---

### Login Admin

Untuk mendapatkan akses login sebagai admin dan mendapatkan file database SQL, silakan hubungi:

- **WhatsApp:** `081326740142`
- **Biaya:** Rp 20.000 (via GoPay)
