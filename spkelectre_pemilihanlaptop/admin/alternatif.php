<?php
/**
 * Halaman Admin - Kelola Alternatif (Laptop)
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$conn = getDB();
$kriteria = getAllKriteria();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add' || $_POST['action'] == 'edit') {
            $kode = strtoupper(trim($_POST['kode_alternatif']));
            $nama = trim($_POST['nama_alternatif']);
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            
            if ($_POST['action'] == 'add') {
                $stmt = $conn->prepare("INSERT INTO alternatif (kode_alternatif, nama_alternatif, deskripsi) VALUES (?, ?, ?)");
                try {
                    $stmt->execute([$kode, $nama, $deskripsi]);
                    $idAlternatif = $conn->lastInsertId();
                    
                    foreach ($kriteria as $krit) {
                        $nilai = floatval($_POST['nilai'][$krit['id_kriteria']] ?? 0);
                        $stmtNilai = $conn->prepare("INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, nilai) VALUES (?, ?, ?)");
                        $stmtNilai->execute([$idAlternatif, $krit['id_kriteria'], $nilai]);
                    }
                    
                    $message = "Alternatif berhasil ditambahkan!";
                    $messageType = "success";
                } catch (PDOException $e) {
                    $message = "Error: " . $e->getMessage();
                    $messageType = "danger";
                }
            } else {
                $idAlternatif = intval($_POST['id_alternatif']);
                $stmt = $conn->prepare("UPDATE alternatif SET kode_alternatif = ?, nama_alternatif = ?, deskripsi = ? WHERE id_alternatif = ?");
                try {
                    $stmt->execute([$kode, $nama, $deskripsi, $idAlternatif]);
                    
                    foreach ($kriteria as $krit) {
                        $nilai = floatval($_POST['nilai'][$krit['id_kriteria']] ?? 0);
                        $stmtNilai = $conn->prepare("UPDATE nilai_alternatif SET nilai = ? WHERE id_alternatif = ? AND id_kriteria = ?");
                        $stmtNilai->execute([$nilai, $idAlternatif, $krit['id_kriteria']]);
                    }
                    
                    $message = "Alternatif berhasil diupdate!";
                    $messageType = "success";
                } catch (PDOException $e) {
                    $message = "Error: " . $e->getMessage();
                    $messageType = "danger";
                }
            }
        } elseif ($_POST['action'] == 'delete') {
            $idAlternatif = intval($_POST['id_alternatif']);
            $stmt = $conn->prepare("DELETE FROM alternatif WHERE id_alternatif = ?");
            try {
                $stmt->execute([$idAlternatif]);
                $message = "Alternatif berhasil dihapus!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "danger";
            }
        }
    }
}

// Get alternatif untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $idEdit = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM alternatif WHERE id_alternatif = ?");
    $stmt->execute([$idEdit]);
    $editData = $stmt->fetch();
    
    if ($editData) {
        $nilaiEdit = getNilaiAlternatif($idEdit);
        $nilaiMap = [];
        foreach ($nilaiEdit as $nilai) {
            $nilaiMap[$nilai['id_kriteria']] = $nilai['nilai'];
        }
        $editData['nilai'] = $nilaiMap;
    }
}

$alternatif = getAllAlternatif();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Alternatif - Admin SPK ELECTRE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .navbar {
            background: var(--primary-gradient) !important;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .card-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="bi bi-laptop"></i> <strong>SPK ELECTRE</strong> - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="../index.php">
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
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-<?php echo $editData ? 'pencil' : 'plus-circle'; ?>"></i>
                            <?php echo $editData ? 'Edit' : 'Tambah'; ?> Alternatif
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $editData ? 'edit' : 'add'; ?>">
                            <?php if ($editData): ?>
                            <input type="hidden" name="id_alternatif" value="<?php echo $editData['id_alternatif']; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="kode_alternatif" class="form-label">Kode Alternatif</label>
                                <input type="text" class="form-control" id="kode_alternatif" name="kode_alternatif" 
                                       value="<?php echo htmlspecialchars($editData['kode_alternatif'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="nama_alternatif" class="form-label">Nama Laptop</label>
                                <input type="text" class="form-control" id="nama_alternatif" name="nama_alternatif" 
                                       value="<?php echo htmlspecialchars($editData['nama_alternatif'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo htmlspecialchars($editData['deskripsi'] ?? ''); ?></textarea>
                            </div>

                            <hr>
                            <h6>Nilai Kriteria:</h6>
                            <?php foreach ($kriteria as $krit): ?>
                            <div class="mb-3">
                                <label for="nilai_<?php echo $krit['id_kriteria']; ?>" class="form-label">
                                    <?php echo $krit['kode_kriteria']; ?>: <?php echo $krit['nama_kriteria']; ?>
                                    <span class="badge bg-<?php echo $krit['tipe_kriteria'] == 'benefit' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($krit['tipe_kriteria']); ?>
                                    </span>
                                </label>
                                <input type="number" class="form-control" 
                                       id="nilai_<?php echo $krit['id_kriteria']; ?>" 
                                       name="nilai[<?php echo $krit['id_kriteria']; ?>]"
                                       value="<?php echo $editData ? ($editData['nilai'][$krit['id_kriteria']] ?? 0) : 0; ?>" 
                                       step="0.01" min="0" required>
                                <small class="text-muted">
                                    <?php if ($krit['kode_kriteria'] == 'C1'): ?>
                                        Harga dalam Rupiah (contoh: 15000000)
                                    <?php elseif ($krit['kode_kriteria'] == 'C2' || $krit['kode_kriteria'] == 'C5'): ?>
                                        Skor 0-100
                                    <?php elseif ($krit['kode_kriteria'] == 'C3'): ?>
                                        RAM dalam GB (contoh: 8, 16, 32)
                                    <?php elseif ($krit['kode_kriteria'] == 'C4'): ?>
                                        Storage dalam GB (contoh: 256, 512, 1024)
                                    <?php else: ?>
                                        Daya tahan baterai dalam jam
                                    <?php endif; ?>
                                </small>
                            </div>
                            <?php endforeach; ?>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-<?php echo $editData ? 'check' : 'plus'; ?>"></i>
                                    <?php echo $editData ? 'Update' : 'Tambah'; ?> Alternatif
                                </button>
                                <?php if ($editData): ?>
                                <a href="alternatif.php" class="btn btn-outline-secondary">Batal</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Alternatif</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($alternatif)): ?>
                        <div class="alert alert-info">Belum ada alternatif.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Laptop</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alternatif as $alt): ?>
                                    <tr>
                                        <td><strong><?php echo $alt['kode_alternatif']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($alt['nama_alternatif']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($alt['deskripsi'] ?? '-', 0, 50)); ?>...</td>
                                        <td>
                                            <a href="?edit=<?php echo $alt['id_alternatif']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(<?php echo $alt['id_alternatif']; ?>, '<?php echo htmlspecialchars($alt['nama_alternatif']); ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id_alternatif" id="deleteId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, nama) {
            if (confirm('Hapus alternatif "' + nama + '"?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>

