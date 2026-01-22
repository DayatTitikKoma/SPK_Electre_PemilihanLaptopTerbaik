<?php
/**
 * Halaman Kelola Alternatif (Laptop)
 */

session_start();
require_once 'includes/functions.php';

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
                // Insert alternatif
                $stmt = $conn->prepare("INSERT INTO alternatif (kode_alternatif, nama_alternatif, deskripsi) VALUES (?, ?, ?)");
                try {
                    $stmt->execute([$kode, $nama, $deskripsi]);
                    $idAlternatif = $conn->lastInsertId();
                    
                    // Insert nilai untuk setiap kriteria
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
                // Update alternatif
                $idAlternatif = intval($_POST['id_alternatif']);
                $stmt = $conn->prepare("UPDATE alternatif SET kode_alternatif = ?, nama_alternatif = ?, deskripsi = ? WHERE id_alternatif = ?");
                try {
                    $stmt->execute([$kode, $nama, $deskripsi, $idAlternatif]);
                    
                    // Update nilai untuk setiap kriteria
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Alternatif - SPK Pemilihan Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .alternatif-card {
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }
        .alternatif-card:hover {
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2) !important;
            transform: translateY(-5px);
        }
        .modal-dialog-scrollable .modal-body {
            max-height: calc(100vh - 200px);
        }
        #formModal .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        #formModal .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        #formModal small.text-muted {
            font-size: 0.75rem;
            display: block;
            margin-top: 0.25rem;
        }
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
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Header dengan Tombol Tambah -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <h2 class="mb-0">
                <i class="bi bi-laptop"></i> Kelola Alternatif (Laptop)
            </h2>
            <button type="button" class="btn btn-primary btn-lg w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#formModal" onclick="resetForm()">
                <i class="bi bi-plus-circle"></i> Tambah Alternatif Baru
            </button>
        </div>

        <!-- Daftar Alternatif dalam Grid Card -->
        <?php if (empty($alternatif)): ?>
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                <h4 class="mt-3 text-muted">Belum Ada Alternatif</h4>
                <p class="text-muted">Silakan tambah alternatif baru untuk memulai</p>
                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#formModal" onclick="resetForm()">
                    <i class="bi bi-plus-circle"></i> Tambah Alternatif Pertama
                </button>
            </div>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($alternatif as $alt): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100 alternatif-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">
                                    <span class="badge bg-primary"><?php echo $alt['kode_alternatif']; ?></span>
                                </h5>
                                <h6 class="text-dark mb-0"><?php echo htmlspecialchars($alt['nama_alternatif']); ?></h6>
                            </div>
                            <div class="btn-group" role="group">
                                <a href="?edit=<?php echo $alt['id_alternatif']; ?>" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Edit alternatif">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete(<?php echo $alt['id_alternatif']; ?>, '<?php echo htmlspecialchars($alt['nama_alternatif']); ?>')"
                                        title="Hapus alternatif">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="card-text text-muted small mb-0">
                            <?php echo htmlspecialchars($alt['deskripsi'] ?? 'Tidak ada deskripsi'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal Form Tambah/Edit Alternatif -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-<?php echo $editData ? 'warning' : 'success'; ?> text-white">
                    <h5 class="modal-title" id="formModalLabel">
                        <i class="bi bi-<?php echo $editData ? 'pencil' : 'plus-circle'; ?>"></i>
                        <?php echo $editData ? 'Edit' : 'Tambah'; ?> Alternatif
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="alternatifForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editData ? 'edit' : 'add'; ?>" id="formAction">
                        <?php if ($editData): ?>
                        <input type="hidden" name="id_alternatif" value="<?php echo $editData['id_alternatif']; ?>" id="formIdAlternatif">
                        <?php endif; ?>

                        <!-- Informasi Dasar -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Informasi Dasar</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="kode_alternatif" class="form-label">Kode Alternatif <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kode_alternatif" name="kode_alternatif" 
                                           value="<?php echo htmlspecialchars($editData['kode_alternatif'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nama_alternatif" class="form-label">Nama Laptop <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_alternatif" name="nama_alternatif" 
                                           value="<?php echo htmlspecialchars($editData['nama_alternatif'] ?? ''); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2" 
                                              placeholder="Masukkan deskripsi laptop (opsional)"><?php echo htmlspecialchars($editData['deskripsi'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Nilai Kriteria -->
                        <div class="mb-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-sliders"></i> Nilai Kriteria</h6>
                            <div class="row g-3">
                                <?php foreach ($kriteria as $krit): ?>
                                <div class="col-md-6">
                                    <label for="nilai_<?php echo $krit['id_kriteria']; ?>" class="form-label">
                                        <strong><?php echo $krit['kode_kriteria']; ?>:</strong> <?php echo $krit['nama_kriteria']; ?>
                                        <span class="badge bg-<?php echo $krit['tipe_kriteria'] == 'benefit' ? 'success' : 'danger'; ?> ms-1">
                                            <?php echo ucfirst($krit['tipe_kriteria']); ?>
                                        </span>
                                    </label>
                                    <input type="number" class="form-control" 
                                           id="nilai_<?php echo $krit['id_kriteria']; ?>" 
                                           name="nilai[<?php echo $krit['id_kriteria']; ?>]"
                                           value="<?php echo $editData ? ($editData['nilai'][$krit['id_kriteria']] ?? 0) : 0; ?>" 
                                           step="<?php echo $krit['kode_kriteria'] == 'C1' ? '1' : '0.01'; ?>" 
                                           min="0" 
                                           required>
                                    <small class="text-muted">
                                        <?php if ($krit['kode_kriteria'] == 'C1'): ?>
                                            <i class="bi bi-currency-exchange"></i> Harga (Rp), contoh: 15000000
                                        <?php elseif ($krit['kode_kriteria'] == 'C2' || $krit['kode_kriteria'] == 'C5'): ?>
                                            <i class="bi bi-star"></i> Skor 0-100
                                        <?php elseif ($krit['kode_kriteria'] == 'C3'): ?>
                                            <i class="bi bi-memory"></i> RAM (GB), contoh: 8, 16, 32
                                        <?php elseif ($krit['kode_kriteria'] == 'C4'): ?>
                                            <i class="bi bi-hdd"></i> Storage (GB), contoh: 256, 512
                                        <?php else: ?>
                                            <i class="bi bi-battery-full"></i> Baterai (jam), contoh: 6, 8, 12
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                                <?php if ($editData): ?>onclick="window.location.href='alternatif.php'"<?php endif; ?>>
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-<?php echo $editData ? 'warning' : 'success'; ?>">
                            <i class="bi bi-<?php echo $editData ? 'check' : 'plus'; ?>"></i>
                            <?php echo $editData ? 'Update' : 'Simpan'; ?> Alternatif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Delete (hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id_alternatif" id="deleteId">
    </form>

    <footer class="mt-5 py-4 bg-light">
        <div class="container text-center">
            <p class="text-muted mb-0">
                Sistem Pendukung Keputusan Pemilihan Laptop menggunakan Metode ELECTRE
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, nama) {
            if (confirm('Apakah Anda yakin ingin menghapus alternatif "' + nama + '"?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        function resetForm() {
            document.getElementById('alternatifForm').reset();
            document.getElementById('formAction').value = 'add';
            const formId = document.getElementById('formIdAlternatif');
            if (formId) formId.remove();
            
            // Reset modal title and header
            const modalTitle = document.getElementById('formModalLabel');
            const modalHeader = document.querySelector('#formModal .modal-header');
            const submitBtn = document.querySelector('#formModal button[type="submit"]');
            
            modalTitle.innerHTML = '<i class="bi bi-plus-circle"></i> Tambah Alternatif';
            modalHeader.className = 'modal-header bg-success text-white';
            submitBtn.className = 'btn btn-success';
            submitBtn.innerHTML = '<i class="bi bi-plus"></i> Simpan Alternatif';
        }

        // Auto-open modal jika sedang edit mode
        <?php if ($editData): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('formModal'));
            modal.show();
        });
        <?php endif; ?>

        // Smooth scroll untuk card hover effect
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.alternatif-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.transition = 'transform 0.3s ease';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>

