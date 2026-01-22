<?php
/**
 * Halaman Hasil Perhitungan ELECTRE
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'classes/ElectreCalculator.php';

requireLogin();
$user = getCurrentUser();

// Ambil data dari form
if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['bobot'])) {
    header('Location: index.php');
    exit;
}

$bobotInput = $_POST['bobot'];
$data = buildMatriksKeputusan();
$alternatif = $data['alternatif'];
$kriteria = $data['kriteria'];
$matriksKeputusan = $data['matriks'];

// Siapkan bobot sesuai urutan kriteria
$bobot = [];
foreach ($kriteria as $krit) {
    $bobot[] = floatval($bobotInput[$krit['id_kriteria']] ?? 0);
}

// Normalisasi bobot
$bobot = normalisasiBobot($bobot);

// Inisialisasi dan jalankan perhitungan ELECTRE
$calculator = new ElectreCalculator($alternatif, $kriteria, $bobot, $matriksKeputusan);
$hasil = $calculator->hitungSemua();
$peringkat = $hasil['peringkat'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan ELECTRE - SPK Pemilihan Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .table-matrix {
            font-size: 0.85rem;
        }
        .table-matrix th, .table-matrix td {
            text-align: center;
            padding: 0.5rem;
        }
        .ranking-card {
            border-left: 4px solid;
        }
        .ranking-1 { border-left-color: #ffd700; }
        .ranking-2 { border-left-color: #c0c0c0; }
        .ranking-3 { border-left-color: #cd7f32; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-laptop"></i> SPK ELECTRE - Pemilihan Laptop
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="index.php">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Rekomendasi Terbaik -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow ranking-card ranking-1">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0"><i class="bi bi-trophy"></i> REKOMENDASI LAPTOP TERBAIK</h3>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="text-primary"><?php echo htmlspecialchars($peringkat[0]['alternatif']['nama_alternatif']); ?></h2>
                                <p class="lead"><?php echo htmlspecialchars($peringkat[0]['alternatif']['deskripsi'] ?? ''); ?></p>
                                <p class="mb-0">
                                    <strong>Kode:</strong> <?php echo $peringkat[0]['alternatif']['kode_alternatif']; ?> | 
                                    <strong>Net Flow:</strong> <?php echo formatAngka($peringkat[0]['net_flow'], 2); ?> | 
                                    <strong>Dominasi Keluar:</strong> <?php echo $peringkat[0]['dom_keluar']; ?> | 
                                    <strong>Dominasi Masuk:</strong> <?php echo $peringkat[0]['dom_masuk']; ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="display-1 text-success">1</div>
                                <p class="text-muted">Peringkat Teratas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peringkat Lengkap -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-list-ol"></i> Peringkat Lengkap Semua Alternatif</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Kode</th>
                                        <th>Nama Laptop</th>
                                        <th>Dominasi Keluar</th>
                                        <th>Dominasi Masuk</th>
                                        <th>Net Flow</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($peringkat as $index => $p): ?>
                                    <tr class="<?php echo $index == 0 ? 'table-success' : ''; ?>">
                                        <td><strong><?php echo $index + 1; ?></strong></td>
                                        <td><?php echo $p['alternatif']['kode_alternatif']; ?></td>
                                        <td><?php echo htmlspecialchars($p['alternatif']['nama_alternatif']); ?></td>
                                        <td><?php echo $p['dom_keluar']; ?></td>
                                        <td><?php echo $p['dom_masuk']; ?></td>
                                        <td><strong><?php echo formatAngka($p['net_flow'], 2); ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Keputusan -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-table"></i> Matriks Keputusan (X = [xij])</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-matrix">
                                <thead>
                                    <tr>
                                        <th>Alternatif</th>
                                        <?php foreach ($kriteria as $krit): ?>
                                        <th><?php echo $krit['kode_kriteria']; ?><br>
                                            <small><?php echo $krit['nama_kriteria']; ?></small>
                                        </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt): ?>
                                    <tr>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php foreach ($matriksKeputusan[$i] as $nilai): ?>
                                        <td><?php echo formatAngka($nilai, 2); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Normalisasi -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Matriks Normalisasi (rij = xij / sqrt(∑ xkj²))</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-matrix">
                                <thead>
                                    <tr>
                                        <th>Alternatif</th>
                                        <?php foreach ($kriteria as $krit): ?>
                                        <th><?php echo $krit['kode_kriteria']; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt): ?>
                                    <tr>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php foreach ($hasil['matriks_normalisasi'][$i] as $nilai): ?>
                                        <td><?php echo formatAngka($nilai, 4); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Terbobot -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Matriks Terbobot (vij = wj × rij)</h5>
                        <small class="text-muted">Bobot: 
                            <?php foreach ($kriteria as $i => $krit): ?>
                                <?php echo $krit['kode_kriteria']; ?> = <?php echo formatAngka($bobot[$i], 3); ?><?php echo $i < count($kriteria) - 1 ? ', ' : ''; ?>
                            <?php endforeach; ?>
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-matrix">
                                <thead>
                                    <tr>
                                        <th>Alternatif</th>
                                        <?php foreach ($kriteria as $krit): ?>
                                        <th><?php echo $krit['kode_kriteria']; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt): ?>
                                    <tr>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php foreach ($hasil['matriks_terbobot'][$i] as $nilai): ?>
                                        <td><?php echo formatAngka($nilai, 4); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Konkordansi -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Matriks Konkordansi (ckl = ∑ wj, j ∈ Ckl)</h5>
                        <small class="text-muted">Threshold c̄ = <?php echo formatAngka($hasil['threshold']['c'], 4); ?></small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-matrix">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php foreach ($alternatif as $alt): ?>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt): ?>
                                    <tr>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php foreach ($hasil['matriks_konkordansi'][$i] as $nilai): ?>
                                        <td class="<?php echo $nilai >= $hasil['threshold']['c'] ? 'table-success' : ''; ?>">
                                            <?php echo formatAngka($nilai, 4); ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Diskordansi -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Matriks Diskordansi (dkl)</h5>
                        <small class="text-muted">Threshold d̄ = <?php echo formatAngka($hasil['threshold']['d'], 4); ?></small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-matrix">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php foreach ($alternatif as $alt): ?>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt): ?>
                                    <tr>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php foreach ($hasil['matriks_diskordansi'][$i] as $nilai): ?>
                                        <td class="<?php echo $nilai <= $hasil['threshold']['d'] ? 'table-success' : ''; ?>">
                                            <?php echo formatAngka($nilai, 4); ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Dominasi F -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Matriks Dominasi F (fkl = 1 jika ckl ≥ c̄)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-matrix">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php foreach ($alternatif as $alt): ?>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt): ?>
                                    <tr>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php foreach ($hasil['matriks_dominasi_F'][$i] as $nilai): ?>
                                        <td class="<?php echo $nilai == 1 ? 'table-success' : ''; ?>">
                                            <?php echo $nilai; ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Dominasi G -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Matriks Dominasi G (gkl = 1 jika dkl ≤ d̄)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-matrix">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php foreach ($alternatif as $alt): ?>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt): ?>
                                    <tr>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php foreach ($hasil['matriks_dominasi_G'][$i] as $nilai): ?>
                                        <td class="<?php echo $nilai == 1 ? 'table-success' : ''; ?>">
                                            <?php echo $nilai; ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Outranking -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Matriks Outranking (ekl = fkl × gkl)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-matrix">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php foreach ($alternatif as $alt): ?>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $i => $alt): ?>
                                    <tr>
                                        <th><?php echo $alt['kode_alternatif']; ?></th>
                                        <?php foreach ($hasil['matriks_outranking'][$i] as $nilai): ?>
                                        <td class="<?php echo $nilai == 1 ? 'table-warning' : ''; ?>">
                                            <?php echo $nilai; ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong>Keterangan:</strong> Nilai 1 menunjukkan bahwa alternatif pada baris mendominasi alternatif pada kolom.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penjelasan Hasil -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Penjelasan Hasil</h5>
                    </div>
                    <div class="card-body">
                        <h6>Metode ELECTRE:</h6>
                        <p>Metode ELECTRE menentukan alternatif terbaik berdasarkan:</p>
                        <ul>
                            <li><strong>Konkordansi:</strong> Jumlah kriteria dimana alternatif A lebih baik dari alternatif B</li>
                            <li><strong>Diskordansi:</strong> Besarnya perbedaan dimana alternatif A lebih buruk dari alternatif B</li>
                            <li><strong>Outranking:</strong> Alternatif A mendominasi B jika memenuhi syarat konkordansi dan diskordansi</li>
                        </ul>
                        <h6 class="mt-3">Interpretasi Hasil:</h6>
                        <ul>
                            <li><strong>Net Flow:</strong> Selisih antara dominasi keluar dan dominasi masuk. Semakin tinggi net flow, semakin baik alternatif tersebut.</li>
                            <li><strong>Dominasi Keluar:</strong> Jumlah alternatif yang didominasi oleh alternatif ini.</li>
                            <li><strong>Dominasi Masuk:</strong> Jumlah alternatif yang mendominasi alternatif ini.</li>
                        </ul>
                        <p class="mt-3"><strong>Rekomendasi:</strong> Laptop dengan net flow tertinggi adalah pilihan terbaik berdasarkan kriteria dan bobot yang Anda tentukan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 text-center">
                <a href="index.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary btn-lg ms-2">
                    <i class="bi bi-printer"></i> Cetak Hasil
                </button>
            </div>
        </div>
    </div>

    <footer class="mt-5 py-4 bg-light">
        <div class="container text-center">
            <p class="text-muted mb-0">
                Sistem Pendukung Keputusan Pemilihan Laptop menggunakan Metode ELECTRE
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

