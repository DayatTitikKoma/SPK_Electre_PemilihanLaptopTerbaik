# Update Alternatif ke Mid-Level ke Atas

Script ini digunakan untuk mengupdate data alternatif (laptop) menjadi laptop mid-level ke atas dengan spesifikasi yang lebih tinggi.

## Perubahan yang Dilakukan

### A1: ASUS ROG Strix G15
- **Status**: Tetap High-End Gaming
- **Upgrade**: Prosesor 85→88, GPU 90→92, Baterai 6→7 jam, Harga 15→16.5 juta

### A2: Lenovo ThinkPad X1 Carbon Gen 9
- **Status**: Tetap Premium Business
- **Upgrade**: Prosesor 80→85, GPU 60→70, Baterai 12→13 jam, Harga 20→22 juta

### A3: Acer Predator Helios 300
- **Status**: Upgrade dari Mid-Range ke Mid-High Level Gaming
- **Upgrade Besar**: 
  - RAM: 8GB → **16GB** ✅
  - Storage: 256GB → **512GB** ✅
  - Prosesor: 75 → **82**
  - GPU: 85 → **88**
  - Harga: 12 → 14.5 juta

### A4: HP Pavilion 15 → HP Envy 15
- **Status**: Upgrade dari Entry-Level ke Mid-Level
- **Upgrade Besar**:
  - RAM: 8GB → **16GB** ✅
  - Storage: 256GB → **512GB** ✅
  - Prosesor: 65 → **83**
  - GPU: 50 → **75**
  - Baterai: 7 → 9 jam
  - Harga: 8 → 13.5 juta

### A5: Dell XPS 15 OLED
- **Status**: Tetap Premium
- **Upgrade**: Prosesor 90→92, Baterai 10→11 jam, Harga 25→28 juta

## Spesifikasi Minimum Mid-Level

Setelah update, semua laptop memenuhi kriteria mid-level ke atas:
- ✅ RAM: **Minimal 16GB**
- ✅ Storage: **Minimal 512GB**
- ✅ Prosesor: **Minimal 82/100**
- ✅ GPU: **Minimal 70/100**

## Cara Menggunakan

### Opsi 1: Update Database yang Sudah Ada

1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Pilih database `spk_electre_laptop`
3. Klik tab "SQL"
4. Copy dan paste isi file `update_to_midlevel.sql`
5. Klik "Go" untuk menjalankan script

### Opsi 2: Import Ulang Schema

Jika ingin memulai dari awal dengan data mid-level:

1. Backup database yang ada (opsional)
2. Drop database lama:
   ```sql
   DROP DATABASE spk_electre_laptop;
   ```
3. Import file `schema.sql` yang sudah diupdate
4. Semua data akan otomatis menjadi mid-level ke atas

## Catatan

- Script update aman untuk dijalankan pada database yang sudah ada
- Data alternatif yang sudah ada akan diupdate, bukan dihapus
- Pastikan backup database sebelum menjalankan update (opsional tapi disarankan)

