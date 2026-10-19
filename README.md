# Aplikasi Manajemen Inventaris

Aplikasi web sederhana untuk mengelola inventaris toko perlengkapan komputer, dibuat dengan **Laravel 13** dan **Filament 5** sebagai tugas Ujian Live Coding Case Study (Pemrograman Berorientasi Objek / Pengembangan Aplikasi Web).

Aplikasi menggantikan pencatatan stok melalui spreadsheet: pemilik toko dapat login, mengelompokkan barang berdasarkan kategori, serta menambah, mengubah, dan menghapus data barang beserta stoknya.

| | |
|---|---|
| **Nama** | Mohamad Rifki Hanifudin |
| **NIM** | 2495114008 |
| **Kelas** | TI-A |

---

## Fitur

- **Authentication**: halaman login, logout, dan proteksi seluruh halaman inventaris dengan middleware `auth` (Filament `Authenticate` + `AuthenticateSession`). Pengguna yang belum login diarahkan ke halaman login.
- **CRUD Kategori**: tambah, lihat, ubah, hapus. Kategori yang masih memiliki barang **tidak dapat dihapus** (muncul notifikasi error, baik untuk hapus satuan maupun hapus massal).
- **CRUD Barang**: tambah, lihat, ubah, hapus. Kategori dipilih dari dropdown data kategori yang ada, dan daftar barang menampilkan **nama kategori** melalui relasi Eloquent.
- **Validasi input** pada form kategori dan barang (lihat tabel di bawah).
- **Migration** dengan foreign key dan **Seeder** data awal (1 pengguna, 3 kategori, 5 barang).
- Pencarian dan pengurutan pada tabel kategori dan barang.

## Teknologi

| Komponen | Versi |
|---|---|
| PHP | ^8.3 |
| Laravel | ^13.17 |
| Filament (admin panel) | ^5.0 |
| Database | MySQL |
| Node.js + npm | untuk build asset Vite |
| Pest | pengujian otomatis |

---

## Struktur Database

```
categories                      items
┌────────────┐ 1          N ┌─────────────────┐
│ id (PK)    │──────────────│ id (PK)         │
│ name       │              │ category_id (FK)│
│ created_at │              │ name            │
│ updated_at │              │ price           │
└────────────┘              │ stock           │
                            │ created_at      │
                            │ updated_at      │
                            └─────────────────┘
```

**Relasi Eloquent**

- `App\Models\Category`: `items()` → `hasMany(Item::class)`
- `App\Models\Item`: `category()` → `belongsTo(Category::class)`

**Migration** (urutan eksekusi sesuai timestamp):

1. `0001_01_01_000000_create_users_table.php`: tabel pengguna
2. `2026_10_18_145724_create_categories_table.php`: tabel `categories`
3. `2026_10_18_150003_create_items_table.php`: tabel `items` dengan foreign key `category_id` ke `categories.id` (dijalankan setelah `categories`)

## Aturan Validasi

| Field | Aturan |
|---|---|
| Nama kategori | wajib diisi |
| Nama barang | wajib diisi |
| Harga | wajib, numerik, lebih besar dari 0 |
| Stok | wajib, bilangan bulat |
| Kategori barang | wajib, dipilih dari data kategori yang tersedia (`category_id` ditolak jika tidak valid karena foreign key) |

## Data Seeder

`php artisan db:seed` membuat:

- **1 akun pengguna** untuk pengujian login
- **3 kategori**: Laptop, Komponen PC, Aksesoris
- **5 barang** yang terhubung ke kategori tersebut

| Barang | Kategori |
|---|---|
| ASUS ROG Strix G16 | Laptop |
| MacBook Pro M3 | Laptop |
| NVIDIA RTX 4070 Ti | Komponen PC |
| Processor Intel i7-14700K | Komponen PC |
| Logitech G Pro X Superlight | Aksesoris |

Harga dan stok barang diisi otomatis oleh `ItemFactory` (nilai acak). Password akun disimpan dalam bentuk hash (cast `hashed` pada model `User`).

**Akun untuk pengujian login**

| | |
|---|---|
| Email | `admin@tokokomputer.id` |
| Password | `password` |

---

## Cara Instalasi

### Prasyarat

- PHP 8.3 atau lebih baru (ekstensi umum Laravel + `intl`, `pdo_mysql` atau `pdo_sqlite`)
- Composer
- Node.js dan npm
- MySQL atau MariaDB

### Langkah

1. **Clone repository**

   ```bash
   git clone https://github.com/frizennwave/Inventory-management-app.git
   cd Inventory-management-app
   ```

2. **Install dependensi**

   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Siapkan file environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Atur koneksi database di `.env`**

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventory_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. **Jalankan migration dan seeder**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Jalankan aplikasi**

   ```bash
   php artisan serve
   ```

7. Buka **http://localhost/admin** dan login dengan akun seeder di atas.

### Membangun ulang database dari awal

```bash
php artisan migrate:fresh --seed
```

> ⚠️ `migrate:fresh` **menghapus seluruh tabel** lalu membuatnya kembali. Gunakan hanya pada database pengembangan atau pengujian.

### Rollback migration

```bash
php artisan migrate:rollback     # batalkan batch terakhir
php artisan migrate:status       # cek status migration
```

### Memeriksa struktur tabel dan relasi

```bash
php artisan db:show --counts
php artisan db:table items
```

Atau lewat `php artisan tinker`:

```php
App\Models\Item::with('category')->get();
App\Models\Category::withCount('items')->get();
```

---

## Daftar Halaman

| URL | Keterangan |
|---|---|
| `/admin/login` | Halaman login |
| `/admin` | Dashboard (butuh login) |
| `/admin/categories` | CRUD kategori (butuh login) |
| `/admin/items` | CRUD barang (butuh login) |

Mengakses `/admin/*` tanpa login akan diarahkan ke `/admin/login`. Setelah logout, sesi lama tidak dapat dipakai untuk mengakses halaman inventaris.

