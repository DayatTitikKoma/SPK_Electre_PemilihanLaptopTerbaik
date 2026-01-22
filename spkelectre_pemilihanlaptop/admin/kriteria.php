<?php
/**
 * Halaman Admin - Kelola Kriteria
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$conn = getDB();
$message = '';
$messageType = '';

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
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kriteria - Admin SPK ELECTRE</title>
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

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Kelola Kriteria</h5>
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
                        <a href="../index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

