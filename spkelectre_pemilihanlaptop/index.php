<?php
/**
 * Halaman Utama - Sistem Pendukung Keputusan Pemilihan Laptop
 * Metode: ELECTRE
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit;
}

// Helper function untuk membersihkan nama dari "Demo"
function cleanUserName($nama) {
    if (empty($nama)) return 'User';
    return trim(str_replace(' Demo', '', str_replace('Demo', '', $nama)));
}

$data = buildMatriksKeputusan();
$kriteria = $data['kriteria'];
$alternatif = $data['alternatif'];

// Get preset kebutuhan
$conn = getDB();
$stmt = $conn->query("SELECT * FROM preset_kebutuhan ORDER BY nama_preset");
$presetKebutuhan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SPK ELECTRE Pemilihan Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        body {
            background: #f8f9fa;
        }
        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            border: none;
        }
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .preset-card {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .preset-card:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }
        .preset-card.active {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-laptop"></i> <strong>SPK ELECTRE</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house"></i> Beranda
                        </a>
                    </li>
                    <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/alternatif.php">
                            <i class="bi bi-gear"></i> Kelola Data
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars(cleanUserName($user['nama_lengkap'] ?? 'User')); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Welcome Card -->
        <div class="card mb-4" style="background: var(--primary-gradient); color: white; border-radius: 15px;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">Selamat Datang, <?php echo htmlspecialchars(cleanUserName($user['nama_lengkap'] ?? 'User')); ?>!</h2>
                        <p class="mb-0">Sistem Pendukung Keputusan Pemilihan Laptop menggunakan Metode ELECTRE</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="bi bi-laptop" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Kebutuhan (Hanya untuk User) -->
        <?php if (isUser()): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Pilih Kebutuhan Anda</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Pilih preset kebutuhan untuk mendapatkan bobot kriteria yang sesuai:</p>
                <div class="row g-3" id="presetContainer">
                    <?php foreach ($presetKebutuhan as $preset): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card preset-card h-100" data-preset-id="<?php echo $preset['id_preset']; ?>">
                            <div class="card-body text-center">
                                <i class="bi bi-<?php 
                                    echo $preset['nama_preset'] == 'Gaming' ? 'joystick' : 
                                        ($preset['nama_preset'] == 'Editing' ? 'camera-video' : 
                                        ($preset['nama_preset'] == 'Kantor' ? 'briefcase' : 
                                        ($preset['nama_preset'] == 'Mahasiswa' ? 'book' : 
                                        ($preset['nama_preset'] == 'Desain Grafis' ? 'palette' : 'code-slash')))); 
                                ?>" style="font-size: 2.5rem; color: #667eea;"></i>
                                <h6 class="mt-3 mb-2"><?php echo htmlspecialchars($preset['nama_preset']); ?></h6>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($preset['deskripsi']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Input Bobot -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-sliders"></i> Tentukan Bobot Kriteria</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="hasil.php" id="formBobot">
                    <div class="row">
                        <?php foreach ($kriteria as $index => $krit): ?>
                        <div class="col-md-6 mb-3">
                            <label for="bobot_<?php echo $krit['id_kriteria']; ?>" class="form-label">
                                <strong><?php echo $krit['kode_kriteria']; ?>: <?php echo $krit['nama_kriteria']; ?></strong>
                                <span class="badge badge-modern bg-<?php echo $krit['tipe_kriteria'] == 'benefit' ? 'success' : 'danger'; ?> ms-2">
                                    <?php echo ucfirst($krit['tipe_kriteria']); ?>
                                </span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-<?php echo $krit['tipe_kriteria'] == 'benefit' ? 'arrow-up' : 'arrow-down'; ?>"></i></span>
                                <input type="number" 
                                       class="form-control bobot-input" 
                                       id="bobot_<?php echo $krit['id_kriteria']; ?>"
                                       name="bobot[<?php echo $krit['id_kriteria']; ?>]"
                                       value="<?php echo $krit['bobot_default']; ?>"
                                       min="0" 
                                       max="1" 
                                       step="0.01"
                                       required>
                                <span class="input-group-text">(0-1)</span>
                            </div>
                            <small class="text-muted">
                                <?php if ($krit['tipe_kriteria'] == 'benefit'): ?>
                                    <i class="bi bi-info-circle"></i> Semakin besar nilai semakin baik
                                <?php else: ?>
                                    <i class="bi bi-info-circle"></i> Semakin kecil nilai semakin baik
                                <?php endif; ?>
                            </small>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="alert alert-info mt-3 d-flex align-items-center">
                        <i class="bi bi-info-circle me-2" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>Total Bobot: <span id="totalBobot">0.00</span></strong>
                            <br><small>Total akan dinormalisasi otomatis menjadi 1.0</small>
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-calculator"></i> Hitung dengan Metode ELECTRE
                        </button>
                        <?php if (isAdmin()): ?>
                        <a href="admin/alternatif.php" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-gear"></i> Kelola Data
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Alternatif -->
        <div class="card">
            <div class="card-header" style="background: var(--success-gradient);">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Alternatif (Laptop)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($alternatif)): ?>
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    <p class="mt-2">Belum ada alternatif. <?php if (isAdmin()): ?><a href="admin/alternatif.php">Tambah alternatif</a><?php endif; ?></p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Laptop</th>
                                <th>Deskripsi</th>
                                <?php if (isAdmin()): ?>
                                <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alternatif as $alt): ?>
                            <tr>
                                <td><strong class="text-primary"><?php echo $alt['kode_alternatif']; ?></strong></td>
                                <td><?php echo htmlspecialchars($alt['nama_alternatif']); ?></td>
                                <td><?php echo htmlspecialchars($alt['deskripsi'] ?? '-'); ?></td>
                                <?php if (isAdmin()): ?>
                                <td>
                                    <a href="admin/alternatif.php?edit=<?php echo $alt['id_alternatif']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="mt-5 py-4 bg-light">
        <div class="container text-center">
            <p class="text-muted mb-0">
                <i class="bi bi-laptop"></i> Sistem Pendukung Keputusan Pemilihan Laptop menggunakan Metode ELECTRE
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preset kebutuhan handler
        const presetCards = document.querySelectorAll('.preset-card');
        const bobotInputs = document.querySelectorAll('.bobot-input');
        const presetData = <?php echo json_encode($presetKebutuhan); ?>;
        
        presetCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove active class from all
                presetCards.forEach(c => c.classList.remove('active'));
                // Add active to clicked
                this.classList.add('active');
                
                const presetId = parseInt(this.dataset.presetId);
                const preset = presetData.find(p => p.id_preset == presetId);
                
                if (preset) {
                    // Set bobot values
                    bobotInputs[0].value = parseFloat(preset.bobot_harga).toFixed(2);
                    bobotInputs[1].value = parseFloat(preset.bobot_prosesor).toFixed(2);
                    bobotInputs[2].value = parseFloat(preset.bobot_ram).toFixed(2);
                    bobotInputs[3].value = parseFloat(preset.bobot_storage).toFixed(2);
                    bobotInputs[4].value = parseFloat(preset.bobot_gpu).toFixed(2);
                    bobotInputs[5].value = parseFloat(preset.bobot_baterai).toFixed(2);
                    
                    // Trigger update
                    bobotInputs.forEach(input => input.dispatchEvent(new Event('input')));
                }
            });
        });
        
        // Calculate total bobot
        const totalBobotSpan = document.getElementById('totalBobot');
        
        function updateTotalBobot() {
            let total = 0;
            bobotInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            totalBobotSpan.textContent = total.toFixed(2);
            
            const alertDiv = totalBobotSpan.closest('.alert');
            alertDiv.classList.remove('alert-danger', 'alert-warning', 'alert-success');
            if (total > 1.0) {
                alertDiv.classList.add('alert-danger');
            } else if (total < 1.0) {
                alertDiv.classList.add('alert-warning');
            } else {
                alertDiv.classList.add('alert-success');
            }
        }
        
        bobotInputs.forEach(input => {
            input.addEventListener('input', updateTotalBobot);
        });
        
        updateTotalBobot();
    </script>
</body>
</html>
