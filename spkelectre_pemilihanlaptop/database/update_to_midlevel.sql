-- Script untuk Update Alternatif menjadi Laptop Mid-Level ke Atas
-- Jalankan script ini untuk mengupdate data laptop menjadi mid-level ke atas

USE spk_electre_laptop;

-- Update A1: ASUS ROG Strix G15 (Tetap High-End Gaming)
UPDATE alternatif SET 
    nama_alternatif = 'ASUS ROG Strix G15',
    deskripsi = 'Laptop gaming high-end dengan Ryzen 9 dan RTX 3070'
WHERE id_alternatif = 1;

UPDATE nilai_alternatif SET nilai = 16500000 WHERE id_alternatif = 1 AND id_kriteria = 1; -- Harga: 16.5 juta
UPDATE nilai_alternatif SET nilai = 88 WHERE id_alternatif = 1 AND id_kriteria = 2; -- Prosesor: 88/100
UPDATE nilai_alternatif SET nilai = 16 WHERE id_alternatif = 1 AND id_kriteria = 3; -- RAM: 16 GB
UPDATE nilai_alternatif SET nilai = 512 WHERE id_alternatif = 1 AND id_kriteria = 4; -- Storage: 512 GB
UPDATE nilai_alternatif SET nilai = 92 WHERE id_alternatif = 1 AND id_kriteria = 5; -- GPU: 92/100
UPDATE nilai_alternatif SET nilai = 7 WHERE id_alternatif = 1 AND id_kriteria = 6; -- Baterai: 7 jam

-- Update A2: Lenovo ThinkPad X1 Carbon (Tetap Premium Business)
UPDATE alternatif SET 
    nama_alternatif = 'Lenovo ThinkPad X1 Carbon Gen 9',
    deskripsi = 'Laptop bisnis premium dengan Intel i7 gen 11 dan layar 4K'
WHERE id_alternatif = 2;

UPDATE nilai_alternatif SET nilai = 22000000 WHERE id_alternatif = 2 AND id_kriteria = 1; -- Harga: 22 juta
UPDATE nilai_alternatif SET nilai = 85 WHERE id_alternatif = 2 AND id_kriteria = 2; -- Prosesor: 85/100
UPDATE nilai_alternatif SET nilai = 16 WHERE id_alternatif = 2 AND id_kriteria = 3; -- RAM: 16 GB
UPDATE nilai_alternatif SET nilai = 512 WHERE id_alternatif = 2 AND id_kriteria = 4; -- Storage: 512 GB
UPDATE nilai_alternatif SET nilai = 70 WHERE id_alternatif = 2 AND id_kriteria = 5; -- GPU: 70/100 (integrated)
UPDATE nilai_alternatif SET nilai = 13 WHERE id_alternatif = 2 AND id_kriteria = 6; -- Baterai: 13 jam

-- Update A3: Acer Predator Helios 300 -> Upgrade ke Mid-High Level Gaming
UPDATE alternatif SET 
    nama_alternatif = 'Acer Predator Helios 300',
    deskripsi = 'Laptop gaming mid-high level dengan Intel i7 dan RTX 3060'
WHERE id_alternatif = 3;

UPDATE nilai_alternatif SET nilai = 14500000 WHERE id_alternatif = 3 AND id_kriteria = 1; -- Harga: 14.5 juta
UPDATE nilai_alternatif SET nilai = 82 WHERE id_alternatif = 3 AND id_kriteria = 2; -- Prosesor: 82/100
UPDATE nilai_alternatif SET nilai = 16 WHERE id_alternatif = 3 AND id_kriteria = 3; -- RAM: 16 GB (upgrade dari 8GB)
UPDATE nilai_alternatif SET nilai = 512 WHERE id_alternatif = 3 AND id_kriteria = 4; -- Storage: 512 GB (upgrade dari 256GB)
UPDATE nilai_alternatif SET nilai = 88 WHERE id_alternatif = 3 AND id_kriteria = 5; -- GPU: 88/100
UPDATE nilai_alternatif SET nilai = 6 WHERE id_alternatif = 3 AND id_kriteria = 6; -- Baterai: 6 jam

-- Update A4: HP Pavilion 15 -> Ganti dengan HP Envy 15 (Mid-Level)
UPDATE alternatif SET 
    nama_alternatif = 'HP Envy 15',
    deskripsi = 'Laptop mid-level dengan Intel i7 dan MX450 untuk produktivitas dan desain'
WHERE id_alternatif = 4;

UPDATE nilai_alternatif SET nilai = 13500000 WHERE id_alternatif = 4 AND id_kriteria = 1; -- Harga: 13.5 juta
UPDATE nilai_alternatif SET nilai = 83 WHERE id_alternatif = 4 AND id_kriteria = 2; -- Prosesor: 83/100
UPDATE nilai_alternatif SET nilai = 16 WHERE id_alternatif = 4 AND id_kriteria = 3; -- RAM: 16 GB (upgrade dari 8GB)
UPDATE nilai_alternatif SET nilai = 512 WHERE id_alternatif = 4 AND id_kriteria = 4; -- Storage: 512 GB (upgrade dari 256GB)
UPDATE nilai_alternatif SET nilai = 75 WHERE id_alternatif = 4 AND id_kriteria = 5; -- GPU: 75/100 (upgrade dari 50)
UPDATE nilai_alternatif SET nilai = 9 WHERE id_alternatif = 4 AND id_kriteria = 6; -- Baterai: 9 jam

-- Update A5: Dell XPS 15 (Tetap Premium)
UPDATE alternatif SET 
    nama_alternatif = 'Dell XPS 15 OLED',
    deskripsi = 'Laptop premium untuk creative professional dengan layar OLED 4K'
WHERE id_alternatif = 5;

UPDATE nilai_alternatif SET nilai = 28000000 WHERE id_alternatif = 5 AND id_kriteria = 1; -- Harga: 28 juta
UPDATE nilai_alternatif SET nilai = 92 WHERE id_alternatif = 5 AND id_kriteria = 2; -- Prosesor: 92/100
UPDATE nilai_alternatif SET nilai = 32 WHERE id_alternatif = 5 AND id_kriteria = 3; -- RAM: 32 GB
UPDATE nilai_alternatif SET nilai = 1024 WHERE id_alternatif = 5 AND id_kriteria = 4; -- Storage: 1 TB
UPDATE nilai_alternatif SET nilai = 95 WHERE id_alternatif = 5 AND id_kriteria = 5; -- GPU: 95/100
UPDATE nilai_alternatif SET nilai = 11 WHERE id_alternatif = 5 AND id_kriteria = 6; -- Baterai: 11 jam

