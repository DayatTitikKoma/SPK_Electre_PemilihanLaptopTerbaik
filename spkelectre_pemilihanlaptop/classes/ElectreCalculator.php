<?php

class ElectreCalculator {
    private $alternatif;
    private $kriteria;
    private $bobot;
    private $matriksKeputusan;
    private $matriksNormalisasi;
    private $matriksTerbobot;
    private $matriksKonkordansi;
    private $matriksDiskordansi;
    private $matriksDominasiF;
    private $matriksDominasiG;
    private $matriksOutranking;
    private $thresholdC;
    private $thresholdD;

    /**
     * Constructor
     * @param array $alternatif Data alternatif (laptop)
     * @param array $kriteria Data kriteria
     * @param array $bobot Bobot untuk setiap kriteria
     * @param array $matriksKeputusan Matriks keputusan X = [xij]
     */
    public function __construct($alternatif, $kriteria, $bobot, $matriksKeputusan) {
        $this->alternatif = $alternatif;
        $this->kriteria = $kriteria;
        $this->bobot = $bobot;
        $this->matriksKeputusan = $matriksKeputusan;
    }

    /**
     * Langkah 3: Normalisasi Matriks Keputusan
     * Menggunakan normalisasi vektor (Euclidean)
     * Formula: rij = xij / sqrt(∑ xkj²)
     * 
     * @return array Matriks normalisasi
     */
    public function normalisasiMatriks() {
        $matriksNormalisasi = [];
        $jumlahAlternatif = count($this->alternatif);
        $jumlahKriteria = count($this->kriteria);

        // Hitung sum of squares untuk setiap kriteria
        $sumSquares = [];
        for ($j = 0; $j < $jumlahKriteria; $j++) {
            $sum = 0;
            for ($i = 0; $i < $jumlahAlternatif; $i++) {
                $sum += pow($this->matriksKeputusan[$i][$j], 2);
            }
            $sumSquares[$j] = sqrt($sum);
        }

        // Normalisasi setiap elemen
        for ($i = 0; $i < $jumlahAlternatif; $i++) {
            $matriksNormalisasi[$i] = [];
            for ($j = 0; $j < $jumlahKriteria; $j++) {
                if ($sumSquares[$j] != 0) {
                    $matriksNormalisasi[$i][$j] = $this->matriksKeputusan[$i][$j] / $sumSquares[$j];
                } else {
                    $matriksNormalisasi[$i][$j] = 0;
                }
            }
        }

        $this->matriksNormalisasi = $matriksNormalisasi;
        return $matriksNormalisasi;
    }

    /**
     * Langkah 4: Membentuk Matriks Keputusan Terbobot
     * Formula: vij = wj * rij
     * 
     * @return array Matriks terbobot
     */
    public function matriksTerbobot() {
        if (empty($this->matriksNormalisasi)) {
            $this->normalisasiMatriks();
        }

        $matriksTerbobot = [];
        $jumlahAlternatif = count($this->alternatif);
        $jumlahKriteria = count($this->kriteria);

        for ($i = 0; $i < $jumlahAlternatif; $i++) {
            $matriksTerbobot[$i] = [];
            for ($j = 0; $j < $jumlahKriteria; $j++) {
                $matriksTerbobot[$i][$j] = $this->bobot[$j] * $this->matriksNormalisasi[$i][$j];
            }
        }

        $this->matriksTerbobot = $matriksTerbobot;
        return $matriksTerbobot;
    }

    /**
     * Langkah 5 & 6: Menentukan Himpunan Konkordansi dan Menghitung Indeks Konkordansi
     * 
     * Himpunan Konkordansi Ckl = {j | vkj >= vlj} untuk benefit
     *                        Ckl = {j | vkj <= vlj} untuk cost
     * 
     * Indeks Konkordansi: ckl = ∑ wj, j ∈ Ckl
     * 
     * @return array Matriks konkordansi
     */
    public function hitungMatriksKonkordansi() {
        if (empty($this->matriksTerbobot)) {
            $this->matriksTerbobot();
        }

        $jumlahAlternatif = count($this->alternatif);
        $jumlahKriteria = count($this->kriteria);
        $matriksKonkordansi = [];

        for ($k = 0; $k < $jumlahAlternatif; $k++) {
            $matriksKonkordansi[$k] = [];
            for ($l = 0; $l < $jumlahAlternatif; $l++) {
                if ($k == $l) {
                    $matriksKonkordansi[$k][$l] = 0; // Tidak membandingkan dengan diri sendiri
                    continue;
                }

                $ckl = 0;
                // Tentukan himpunan konkordansi
                for ($j = 0; $j < $jumlahKriteria; $j++) {
                    $vkj = $this->matriksTerbobot[$k][$j];
                    $vlj = $this->matriksTerbobot[$l][$j];
                    $tipe = $this->kriteria[$j]['tipe_kriteria'];

                    // Untuk benefit: lebih besar lebih baik
                    // Untuk cost: lebih kecil lebih baik
                    if ($tipe == 'benefit') {
                        if ($vkj >= $vlj) {
                            $ckl += $this->bobot[$j];
                        }
                    } else { // cost
                        if ($vkj <= $vlj) {
                            $ckl += $this->bobot[$j];
                        }
                    }
                }

                $matriksKonkordansi[$k][$l] = $ckl;
            }
        }

        $this->matriksKonkordansi = $matriksKonkordansi;
        return $matriksKonkordansi;
    }

    /**
     * Langkah 7: Menghitung Indeks Diskordansi
     * 
     * Formula: dkl = max|vkj - vlj| (untuk j ∈ Dkl) / max|vkj - vlj| (untuk semua j)
     * 
     * @return array Matriks diskordansi
     */
    public function hitungMatriksDiskordansi() {
        if (empty($this->matriksTerbobot)) {
            $this->matriksTerbobot();
        }

        $jumlahAlternatif = count($this->alternatif);
        $jumlahKriteria = count($this->kriteria);
        $matriksDiskordansi = [];

        for ($k = 0; $k < $jumlahAlternatif; $k++) {
            $matriksDiskordansi[$k] = [];
            for ($l = 0; $l < $jumlahAlternatif; $l++) {
                if ($k == $l) {
                    $matriksDiskordansi[$k][$l] = 0;
                    continue;
                }

                // Tentukan himpunan diskordansi dan hitung max perbedaan
                $maxDiskordansi = 0;
                $maxSemua = 0;

                for ($j = 0; $j < $jumlahKriteria; $j++) {
                    $vkj = $this->matriksTerbobot[$k][$j];
                    $vlj = $this->matriksTerbobot[$l][$j];
                    $tipe = $this->kriteria[$j]['tipe_kriteria'];
                    $selisih = abs($vkj - $vlj);

                    // Update max semua kriteria
                    if ($selisih > $maxSemua) {
                        $maxSemua = $selisih;
                    }

                    // Cek apakah termasuk diskordansi
                    $isDiskordansi = false;
                    if ($tipe == 'benefit') {
                        if ($vkj < $vlj) {
                            $isDiskordansi = true;
                        }
                    } else { // cost
                        if ($vkj > $vlj) {
                            $isDiskordansi = true;
                        }
                    }

                    if ($isDiskordansi && $selisih > $maxDiskordansi) {
                        $maxDiskordansi = $selisih;
                    }
                }

                // Hitung indeks diskordansi
                if ($maxSemua != 0) {
                    $matriksDiskordansi[$k][$l] = $maxDiskordansi / $maxSemua;
                } else {
                    $matriksDiskordansi[$k][$l] = 0;
                }
            }
        }

        $this->matriksDiskordansi = $matriksDiskordansi;
        return $matriksDiskordansi;
    }

    /**
     * Langkah 8: Menentukan Nilai Ambang (Threshold)
     * 
     * c̄ = rata-rata seluruh ckl
     * d̄ = rata-rata seluruh dkl
     * 
     * @return array ['c' => thresholdC, 'd' => thresholdD]
     */
    public function hitungThreshold() {
        if (empty($this->matriksKonkordansi)) {
            $this->hitungMatriksKonkordansi();
        }
        if (empty($this->matriksDiskordansi)) {
            $this->hitungMatriksDiskordansi();
        }

        $jumlahAlternatif = count($this->alternatif);
        $totalC = 0;
        $totalD = 0;
        $count = 0;

        for ($k = 0; $k < $jumlahAlternatif; $k++) {
            for ($l = 0; $l < $jumlahAlternatif; $l++) {
                if ($k != $l) {
                    $totalC += $this->matriksKonkordansi[$k][$l];
                    $totalD += $this->matriksDiskordansi[$k][$l];
                    $count++;
                }
            }
        }

        $this->thresholdC = $count > 0 ? $totalC / $count : 0;
        $this->thresholdD = $count > 0 ? $totalD / $count : 0;

        return [
            'c' => $this->thresholdC,
            'd' => $this->thresholdD
        ];
    }

    /**
     * Langkah 9: Membentuk Matriks Dominasi
     * 
     * Matriks F (konkordansi): fkl = 1 jika ckl >= c̄, else 0
     * Matriks G (diskordansi): gkl = 1 jika dkl <= d̄, else 0
     * 
     * @return array ['F' => matriksF, 'G' => matriksG]
     */
    public function hitungMatriksDominasi() {
        if (empty($this->thresholdC) || empty($this->thresholdD)) {
            $this->hitungThreshold();
        }

        $jumlahAlternatif = count($this->alternatif);
        $matriksF = [];
        $matriksG = [];

        for ($k = 0; $k < $jumlahAlternatif; $k++) {
            $matriksF[$k] = [];
            $matriksG[$k] = [];
            for ($l = 0; $l < $jumlahAlternatif; $l++) {
                // Matriks F: fkl = 1 jika ckl >= thresholdC
                if ($k == $l) {
                    $matriksF[$k][$l] = 0;
                } else {
                    $matriksF[$k][$l] = ($this->matriksKonkordansi[$k][$l] >= $this->thresholdC) ? 1 : 0;
                }

                // Matriks G: gkl = 1 jika dkl <= thresholdD
                if ($k == $l) {
                    $matriksG[$k][$l] = 0;
                } else {
                    $matriksG[$k][$l] = ($this->matriksDiskordansi[$k][$l] <= $this->thresholdD) ? 1 : 0;
                }
            }
        }

        $this->matriksDominasiF = $matriksF;
        $this->matriksDominasiG = $matriksG;

        return [
            'F' => $matriksF,
            'G' => $matriksG
        ];
    }

    /**
     * Langkah 10: Membentuk Matriks Outranking
     * 
     * Formula: ekl = fkl × gkl
     * 
     * @return array Matriks outranking
     */
    public function hitungMatriksOutranking() {
        if (empty($this->matriksDominasiF) || empty($this->matriksDominasiG)) {
            $this->hitungMatriksDominasi();
        }

        $jumlahAlternatif = count($this->alternatif);
        $matriksOutranking = [];

        for ($k = 0; $k < $jumlahAlternatif; $k++) {
            $matriksOutranking[$k] = [];
            for ($l = 0; $l < $jumlahAlternatif; $l++) {
                $matriksOutranking[$k][$l] = $this->matriksDominasiF[$k][$l] * $this->matriksDominasiG[$k][$l];
            }
        }

        $this->matriksOutranking = $matriksOutranking;
        return $matriksOutranking;
    }

    /**
     * Langkah 11: Menentukan Peringkat Laptop Terbaik
     * 
     * Berdasarkan jumlah dominasi keluar (outranking) dan masuk (inranking)
     * 
     * @return array Peringkat alternatif
     */
    public function hitungPeringkat() {
        if (empty($this->matriksOutranking)) {
            $this->hitungMatriksOutranking();
        }

        $jumlahAlternatif = count($this->alternatif);
        $peringkat = [];

        for ($k = 0; $k < $jumlahAlternatif; $k++) {
            $domKeluar = 0; // Jumlah alternatif yang didominasi oleh Ak
            $domMasuk = 0;  // Jumlah alternatif yang mendominasi Ak

            for ($l = 0; $l < $jumlahAlternatif; $l++) {
                if ($k != $l) {
                    if ($this->matriksOutranking[$k][$l] == 1) {
                        $domKeluar++;
                    }
                    if ($this->matriksOutranking[$l][$k] == 1) {
                        $domMasuk++;
                    }
                }
            }

            $netFlow = $domKeluar - $domMasuk;

            $peringkat[] = [
                'alternatif' => $this->alternatif[$k],
                'dom_keluar' => $domKeluar,
                'dom_masuk' => $domMasuk,
                'net_flow' => $netFlow,
                'index' => $k
            ];
        }

        // Urutkan berdasarkan net flow (tertinggi = terbaik)
        usort($peringkat, function($a, $b) {
            return $b['net_flow'] <=> $a['net_flow'];
        });

        return $peringkat;
    }

    /**
     * Menjalankan semua perhitungan ELECTRE secara berurutan
     * 
     * @return array Hasil lengkap semua perhitungan
     */
    public function hitungSemua() {
        $this->normalisasiMatriks();
        $this->matriksTerbobot();
        $this->hitungMatriksKonkordansi();
        $this->hitungMatriksDiskordansi();
        $this->hitungThreshold();
        $this->hitungMatriksDominasi();
        $this->hitungMatriksOutranking();
        $peringkat = $this->hitungPeringkat();

        return [
            'matriks_keputusan' => $this->matriksKeputusan,
            'matriks_normalisasi' => $this->matriksNormalisasi,
            'matriks_terbobot' => $this->matriksTerbobot,
            'matriks_konkordansi' => $this->matriksKonkordansi,
            'matriks_diskordansi' => $this->matriksDiskordansi,
            'threshold' => [
                'c' => $this->thresholdC,
                'd' => $this->thresholdD
            ],
            'matriks_dominasi_F' => $this->matriksDominasiF,
            'matriks_dominasi_G' => $this->matriksDominasiG,
            'matriks_outranking' => $this->matriksOutranking,
            'peringkat' => $peringkat
        ];
    }

    // Getter methods
    public function getMatriksNormalisasi() {
        return $this->matriksNormalisasi;
    }

    public function getMatriksTerbobot() {
        return $this->matriksTerbobot;
    }

    public function getMatriksKonkordansi() {
        return $this->matriksKonkordansi;
    }

    public function getMatriksDiskordansi() {
        return $this->matriksDiskordansi;
    }

    public function getMatriksOutranking() {
        return $this->matriksOutranking;
    }

    public function getPeringkat() {
        return $this->hitungPeringkat();
    }
}
?>

