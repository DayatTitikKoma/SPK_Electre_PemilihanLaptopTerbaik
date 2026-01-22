/**
 * JavaScript untuk Sistem Pendukung Keputusan Pemilihan Laptop
 */

document.addEventListener('DOMContentLoaded', function() {
    // Hitung total bobot secara real-time
    const bobotInputs = document.querySelectorAll('.bobot-input');
    const totalBobotSpan = document.getElementById('totalBobot');
    
    if (bobotInputs.length > 0 && totalBobotSpan) {
        function updateTotalBobot() {
            let total = 0;
            bobotInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            totalBobotSpan.textContent = total.toFixed(2);
            
            // Ubah warna berdasarkan total
            if (total > 1.0) {
                totalBobotSpan.parentElement.classList.remove('alert-info');
                totalBobotSpan.parentElement.classList.add('alert-danger');
            } else if (total < 1.0) {
                totalBobotSpan.parentElement.classList.remove('alert-danger');
                totalBobotSpan.parentElement.classList.add('alert-warning');
            } else {
                totalBobotSpan.parentElement.classList.remove('alert-danger', 'alert-warning');
                totalBobotSpan.parentElement.classList.add('alert-success');
            }
        }
        
        bobotInputs.forEach(input => {
            input.addEventListener('input', updateTotalBobot);
        });
        
        // Hitung awal
        updateTotalBobot();
    }
    
    // Validasi form bobot
    const formBobot = document.getElementById('formBobot');
    if (formBobot) {
        formBobot.addEventListener('submit', function(e) {
            let total = 0;
            bobotInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            
            if (total <= 0) {
                e.preventDefault();
                alert('Total bobot harus lebih dari 0!');
                return false;
            }
        });
    }
    
    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
    
    // Smooth scroll untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

