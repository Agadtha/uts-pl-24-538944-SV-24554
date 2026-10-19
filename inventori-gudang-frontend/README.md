# Inventori Gudang — Frontend (Vue 3 + Vite)

Frontend untuk API Laravel `inventori-gudang-api`.

## Fitur
- Halaman login; token Sanctum disimpan di `localStorage` dan dikirim sebagai
  `Authorization: Bearer <token>` pada setiap permintaan (interceptor axios di `src/api/http.js`).
- Halaman barang: tampil (paginasi + pencarian), tambah, ubah, hapus lewat axios.
- Error validasi 422 ditampilkan per kolom; token ditolak (401) otomatis kembali ke login.
- Alamat API **tidak** ditulis di komponen, melainkan lewat variabel lingkungan `VITE_API_BASE_URL`.

## Menjalankan

Prasyarat: Node.js 18+ dan backend Laravel sudah berjalan (`php artisan serve`).

```bash
npm install
cp .env.example .env     # ubah VITE_API_BASE_URL jika alamat API berbeda
npm run dev
```

Buka http://localhost:5173 dan login dengan `admin@example.com` / `password123`.

## Konfigurasi

| Variabel | Contoh | Keterangan |
|----------|--------|------------|
| `VITE_API_BASE_URL` | `http://localhost:8000/api` | Alamat dasar API. Restart `npm run dev` setelah mengubah `.env`. |

Untuk produksi: isi `VITE_API_BASE_URL` dengan alamat API produksi, lalu `npm run build`.

## Jika muncul error CORS
Di proyek Laravel jalankan `php artisan config:publish cors`, lalu pastikan di `config/cors.php`:
`'paths' => ['api/*']` dan `'allowed_origins' => ['http://localhost:5173']`.
