<?php
/**
 * Helper Functions untuk Sistem Pendukung Keputusan
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Mendapatkan koneksi database
 */
function getDB() {
    $database = new Database();
    return $database->getConnection();
}

/**
 * Mendapatkan semua kriteria
 */
function getAllKriteria() {
    $conn = getDB();
    $stmt = $conn->query("SELECT * FROM kriteria ORDER BY id_kriteria");
    return $stmt->fetchAll();
}

/**
 * Mendapatkan semua alternatif (laptop)
 */
function getAllAlternatif() {
    $conn = getDB();
    $stmt = $conn->query("SELECT * FROM alternatif ORDER BY id_alternatif");
    return $stmt->fetchAll();
}

/**
 * Mendapatkan nilai alternatif untuk semua kriteria
 */
function getNilaiAlternatif($idAlternatif) {
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM nilai_alternatif WHERE id_alternatif = ?");
    $stmt->execute([$idAlternatif]);
    return $stmt->fetchAll();
}

/**
 * Membuat matriks keputusan dari database
 */
function buildMatriksKeputusan() {
    $alternatif = getAllAlternatif();
    $kriteria = getAllKriteria();
    $matriks = [];

    foreach ($alternatif as $alt) {
        $row = [];
        $nilaiAlt = getNilaiAlternatif($alt['id_alternatif']);
        
        // Buat array asosiatif untuk memudahkan pencarian
        $nilaiMap = [];
        foreach ($nilaiAlt as $nilai) {
            $nilaiMap[$nilai['id_kriteria']] = $nilai['nilai'];
        }

        foreach ($kriteria as $krit) {
            $row[] = isset($nilaiMap[$krit['id_kriteria']]) ? floatval($nilaiMap[$krit['id_kriteria']]) : 0;
        }

        $matriks[] = $row;
    }

    return [
        'alternatif' => $alternatif,
        'kriteria' => $kriteria,
        'matriks' => $matriks
    ];
}

/**
 * Validasi dan normalisasi bobot kriteria
 * Memastikan total bobot = 1
 */
function normalisasiBobot($bobot) {
    $total = array_sum($bobot);
    if ($total == 0) {
        return $bobot;
    }
    
    $normalized = [];
    foreach ($bobot as $b) {
        $normalized[] = $b / $total;
    }
    
    return $normalized;
}

/**
 * Format angka untuk ditampilkan
 */
function formatAngka($angka, $decimal = 4) {
    return number_format($angka, $decimal, '.', ',');
}

/**
 * Format mata uang Rupiah
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

