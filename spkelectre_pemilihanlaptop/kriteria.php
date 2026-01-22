<?php
/**
 * Halaman Kelola Kriteria
 */

session_start();
require_once 'includes/functions.php';

$conn = getDB();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'update_bobot') {
        foreach ($_POST['bobot'] as $idKriteria => $bobot) {
            $bobot = floatval($bobot);
            $stmt = $conn->prepare("UPDATE kriteria SET bobot_default = ? WHERE id_kriteria = ?");
            $stmt->execute([$bobot, $idKriteria]);
        }
        $message = "Bobot default berhasil diupdate!";
        $messageType = "success";
    }
}

$kriteria = getAllKriteria();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kriteria - SPK Pemilihan Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-check"></i> Daftar Kriteria</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_bobot">
                            
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Kriteria</th>
                                            <th>Tipe</th>
                                            <th>Bobot Default</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($kriteria as $krit): ?>
                                        <tr>
                                            <td><strong><?php echo $krit['kode_kriteria']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($krit['nama_kriteria']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $krit['tipe_kriteria'] == 'benefit' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($krit['tipe_kriteria']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control form-control-sm" 
                                                       name="bobot[<?php echo $krit['id_kriteria']; ?>]"
                                                       value="<?php echo $krit['bobot_default']; ?>"
                                                       min="0" 
                                                       max="1" 
                                                       step="0.01"
                                                       style="width: 100px;">
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php if ($krit['tipe_kriteria'] == 'benefit'): ?>
                                                        Semakin besar nilai semakin baik
                                                    <?php else: ?>
                                                        Semakin kecil nilai semakin baik
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Bobot Default
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Kriteria</h5>
                    </div>
                    <div class="card-body">
                        <h6>Penjelasan Tipe Kriteria:</h6>
                        <ul>
                            <li><strong>Benefit:</strong> Kriteria dimana semakin besar nilainya semakin baik (misalnya: Prosesor, RAM, Storage, GPU, Baterai)</li>
                            <li><strong>Cost:</strong> Kriteria dimana semakin kecil nilainya semakin baik (misalnya: Harga)</li>
                        </ul>
                        <h6 class="mt-3">Catatan:</h6>
                        <p class="text-muted">
                            Bobot default ini akan digunakan sebagai nilai awal pada halaman perhitungan. 
                            Pengguna tetap dapat mengubah bobot saat melakukan perhitungan ELECTRE.
                        </p>
                    </div>
                </div>
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

