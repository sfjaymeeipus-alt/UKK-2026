
<?php

include "middleware/auth.php";
include "config/koneksi.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if (!in_array($role, ['admin', 'guru'])) {
    exit("Anda tidak memiliki akses");
}

// ======================================
// AMBIL IDENTITAS GURU YANG LOGIN
// ======================================

$guruLoginId = 0;
$namaGuruLogin = '';

if ($role == 'guru') {

    $stmtGuruLogin = mysqli_prepare($koneksi, "
        SELECT id, nama
        FROM t_guru
        WHERE user_id = ?
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmtGuruLogin, "i", $user_id);
    mysqli_stmt_execute($stmtGuruLogin);

    $resultGuruLogin = mysqli_stmt_get_result($stmtGuruLogin);
    $dataGuruLogin = mysqli_fetch_assoc($resultGuruLogin);

    mysqli_stmt_close($stmtGuruLogin);

    if (!$dataGuruLogin) {
        exit("Data guru tidak ditemukan untuk akun yang sedang login.");
    }

    $guruLoginId = (int) $dataGuruLogin['id'];
    $namaGuruLogin = $dataGuruLogin['nama'];
}

// ======================================
// CEK WALI KELAS
// ======================================

$isWaliKelas = false;

if ($role == 'guru') {

    $stmt = mysqli_prepare($koneksi, "
        SELECT wk.id
        FROM t_wali_kelas wk
        JOIN t_guru g ON g.id = wk.guru_id
        WHERE g.user_id = ?
          AND wk.status_aktif = 1
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $isWaliKelas = mysqli_stmt_get_result($stmt)->num_rows > 0;

    mysqli_stmt_close($stmt);
}

// ======================================
// NOTIFIKASI
// ======================================

function setPesan($pesan, $jenis = 'success') {
    $_SESSION['pesan_hapus'] = $pesan;
    $_SESSION['jenis_pesan'] = $jenis;
}

// ======================================
// MEMBERSIHKAN NAMA PELANGGARAN
// ======================================

function bersihkanNamaPelanggaran($nama) {
    return trim(preg_replace('/\s+[0-9]+$/', '', $nama));
}

// ======================================
// DATA SISWA AKTIF
// ======================================

$dataSiswa = [];

$result = mysqli_query($koneksi, "
    SELECT
        s.id AS siswa_id,
        s.nama AS nama_siswa,
        ks.kelas_id,
        k.nama AS nama_kelas,
        ks.tahun_ajaran_id
    FROM t_siswa s
    JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
    JOIN t_kelas k ON k.id = ks.kelas_id
    WHERE ks.status_aktif = 1
      AND ks.id = (
          SELECT MAX(ks2.id)
          FROM t_kelas_siswa ks2
          WHERE ks2.siswa_id = s.id
            AND ks2.status_aktif = 1
      )
    ORDER BY s.nama
");

while ($row = mysqli_fetch_assoc($result)) {
    $dataSiswa[] = $row;
}

// ======================================
// DATA PELANGGARAN
// ======================================

$dataPelanggaran = [];
$namaTerpakai = [];

$result = mysqli_query($koneksi, "
    SELECT
        p.id,
        p.pelanggaran_kategori_id,
        p.poin,
        pk.nama AS nama_kategori
    FROM t_pelanggaran p
    JOIN t_pelanggaran_kategori pk
        ON pk.id = p.pelanggaran_kategori_id
    WHERE p.status_aktif = 1
      AND pk.status_aktif = 1
    ORDER BY pk.id, p.id
");

while ($row = mysqli_fetch_assoc($result)) {

    $namaPelanggaran = bersihkanNamaPelanggaran($row['nama_kategori']);

    $key = strtolower(trim($namaPelanggaran));

    if (isset($namaTerpakai[$key])) {
        continue;
    }

    $namaTerpakai[$key] = true;
    $row['nama'] = $namaPelanggaran;

    $dataPelanggaran[] = $row;
}

// ======================================
// DATA GURU
// ADMIN: SEMUA GURU
// GURU: HANYA GURU YANG LOGIN
// ======================================

$dataGuru = [];

if ($role == 'admin') {

    $result = mysqli_query($koneksi, "
        SELECT id, nama
        FROM t_guru
        WHERE status_aktif = 1
        ORDER BY nama ASC, id ASC
    ");

    $namaGuruTerpakai = [];

    while ($row = mysqli_fetch_assoc($result)) {

        $key = strtolower(trim($row['nama']));

        if (isset($namaGuruTerpakai[$key])) {
            continue;
        }

        $namaGuruTerpakai[$key] = true;
        $dataGuru[] = $row;
    }

} else {

    $dataGuru[] = [
        'id' => $guruLoginId,
        'nama' => $namaGuruLogin
    ];
}

// ======================================
// FUNGSI MENGAMBIL DATA BERDASARKAN ID
// ======================================

function ambilData($koneksi, $query, $id) {

    $stmt = mysqli_prepare($koneksi, $query);

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $data = mysqli_stmt_get_result($stmt)->fetch_assoc();

    mysqli_stmt_close($stmt);

    return $data ?: false;
}

// ======================================
// MENGAMBIL DATA SISWA
// ======================================

function ambilDataSiswa($koneksi, $id) {

    return ambilData($koneksi, "
        SELECT
            s.id AS siswa_id,
            s.nama AS nama_siswa,
            ks.kelas_id,
            k.nama AS nama_kelas,
            ks.tahun_ajaran_id
        FROM t_siswa s
        JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
        JOIN t_kelas k ON k.id = ks.kelas_id
        WHERE s.id = ?
          AND ks.status_aktif = 1
        ORDER BY ks.id DESC
        LIMIT 1
    ", $id);
}

// ======================================
// MENGAMBIL DATA PELANGGARAN
// ======================================

function ambilDataPelanggaran($koneksi, $id) {

    $data = ambilData($koneksi, "
        SELECT
            p.id,
            p.pelanggaran_kategori_id,
            p.poin,
            pk.nama AS nama_kategori
        FROM t_pelanggaran p
        JOIN t_pelanggaran_kategori pk
            ON pk.id = p.pelanggaran_kategori_id
        WHERE p.id = ?
          AND p.status_aktif = 1
          AND pk.status_aktif = 1
        LIMIT 1
    ", $id);

    if ($data) {
        $data['nama'] = bersihkanNamaPelanggaran($data['nama_kategori']);
    }

    return $data;
}

// ======================================
// MENGAMBIL DATA GURU
// ======================================

function ambilDataGuru($koneksi, $id) {

    return ambilData($koneksi, "
        SELECT id, nama
        FROM t_guru
        WHERE id = ?
          AND status_aktif = 1
        LIMIT 1
    ", $id);
}

// ======================================
// PROSES PERBARUI STATUS CATATAN
// ======================================

if (isset($_POST['perbarui_status'])) {

    try {

        $catatan_id = (int) ($_POST['catatan_id'] ?? 0);
        $status_baru = trim($_POST['status_baru'] ?? '');

        $statusDiizinkan = [
            'Diproses',
            'Ditindaklanjuti',
            'Selesai'
        ];

        if ($catatan_id <= 0) {
            throw new Exception("ID catatan tidak valid.");
        }

        if (!in_array($status_baru, $statusDiizinkan, true)) {
            throw new Exception("Status yang dipilih tidak valid.");
        }

        if ($role == 'guru') {

            // GURU HANYA BOLEH MEMPERBARUI CATATAN MILIKNYA

            $stmt = mysqli_prepare($koneksi, "
                UPDATE t_pelanggaran_siswa
                SET status = ?
                WHERE id = ?
                  AND guru_id = ?
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "sii",
                $status_baru,
                $catatan_id,
                $guruLoginId
            );

        } else {

            // ADMIN BOLEH MEMPERBARUI SEMUA CATATAN

            $stmt = mysqli_prepare($koneksi, "
                UPDATE t_pelanggaran_siswa
                SET status = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $status_baru,
                $catatan_id
            );

        }

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {

            setPesan("Status catatan pelanggaran berhasil diperbarui.");

        } else {

            setPesan(
                "Tidak ada perubahan status atau catatan tidak ditemukan.",
                "warning"
            );

        }

        mysqli_stmt_close($stmt);

    } catch (Throwable $e) {

        setPesan(
            "Gagal memperbarui status: " . $e->getMessage(),
            "danger"
        );

    }

    header("Location: data_catatan_pelanggaran.php");
    exit;
}

// ======================================
// PROSES SIMPAN CATATAN PELANGGARAN
// ======================================

if (isset($_POST['simpan_tambah'])) {

    try {

        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);

        if ($role == 'guru') {
            $guru_id = $guruLoginId;
        } else {
            $guru_id = (int) ($_POST['guru_id'] ?? 0);
        }

        $tanggal = trim($_POST['tanggal'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $tindakan = trim($_POST['tindakan'] ?? '');
        $status = trim($_POST['status'] ?? '');

        if (
            $siswa_id <= 0 ||
            $pelanggaran_id <= 0 ||
            $guru_id <= 0 ||
            $tanggal == '' ||
            $status == ''
        ) {
            throw new Exception("Semua data wajib diisi.");
        }

        $siswa = ambilDataSiswa($koneksi, $siswa_id);

        $pelanggaran = ambilDataPelanggaran(
            $koneksi,
            $pelanggaran_id
        );

        $guru = ambilDataGuru($koneksi, $guru_id);

        if (!$siswa) {
            throw new Exception("Data siswa tidak ditemukan.");
        }

        if (!$pelanggaran) {
            throw new Exception("Data pelanggaran tidak ditemukan.");
        }

        if (!$guru) {
            throw new Exception("Data guru tidak ditemukan atau tidak aktif.");
        }

        $tahun_ajaran_id = (int) $siswa['tahun_ajaran_id'];
        $nama_siswa = $siswa['nama_siswa'];
        $kelas_id = (int) $siswa['kelas_id'];
        $nama_kelas = $siswa['nama_kelas'];
        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) $pelanggaran['pelanggaran_kategori_id'];
        $poin = (int) $pelanggaran['poin'];
        $nama_guru = $guru['nama'];

        $sql = "
            INSERT INTO t_pelanggaran_siswa (
                tahun_ajaran_id,
                siswa_id,
                nama_siswa,
                kelas_id,
                nama_kelas,
                pelanggaran_id,
                nama_pelanggaran,
                pelanggaran_kategori_id,
                guru_id,
                nama_guru,
                tanggal,
                keterangan,
                poin,
                tindakan,
                status
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ";

        $stmt = mysqli_prepare($koneksi, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "iisisiisiississ",
            $tahun_ajaran_id,
            $siswa_id,
            $nama_siswa,
            $kelas_id,
            $nama_kelas,
            $pelanggaran_id,
            $nama_pelanggaran,
            $kategori_id,
            $guru_id,
            $nama_guru,
            $tanggal,
            $keterangan,
            $poin,
            $tindakan,
            $status
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        setPesan("Catatan pelanggaran berhasil ditambahkan.");

    } catch (Throwable $e) {

        setPesan(
            "Gagal menyimpan catatan: " . $e->getMessage(),
            "danger"
        );
    }

    header("Location: data_catatan_pelanggaran.php");
    exit;
}

// ======================================
// DATA TABEL CATATAN PELANGGARAN
// ADMIN: SELURUH DATA
// GURU: DATA YANG DICATAT SENDIRI
// ======================================

$dataCatatan = [];

$sqlCatatan = "
    SELECT
        ps.id,
        ps.nama_siswa,
        ps.nama_kelas,

        COALESCE(
            NULLIF(pk.nama, ''),
            NULLIF(p.nama, ''),
            NULLIF(ps.nama_pelanggaran, '0'),
            '-'
        ) AS nama_pelanggaran,

        COALESCE(
            NULLIF(g.nama, ''),
            NULLIF(ps.nama_guru, '0'),
            '-'
        ) AS nama_guru,

        ps.tanggal,
        ps.poin,
        ps.tindakan,
        ps.status

    FROM t_pelanggaran_siswa ps

    LEFT JOIN t_pelanggaran p
        ON p.id = ps.pelanggaran_id

    LEFT JOIN t_pelanggaran_kategori pk
        ON pk.id = ps.pelanggaran_kategori_id

    LEFT JOIN t_guru g
        ON g.id = ps.guru_id

    WHERE LOWER(TRIM(ps.status)) <> 'selesai'
";

if ($role == 'guru') {
    $sqlCatatan .= " AND ps.guru_id = " . $guruLoginId;
}

$sqlCatatan .= "
    ORDER BY ps.tanggal DESC, ps.id DESC
";

$result = mysqli_query($koneksi, $sqlCatatan);

while ($row = mysqli_fetch_assoc($result)) {
    $dataCatatan[] = $row;
}

// ======================================
// DAFTAR TINDAKAN
// ======================================

$daftarTindakan = [
    "Teguran",
    "Teguran Lisan",
    "Teguran Tertulis",
    "Pembinaan",
    "Konseling",
    "Panggilan Orang Tua",
    "Pemanggilan Orang Tua",
    "Skorsing"
];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catatan Pelanggaran - Sistem Informasi Pelanggaran Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container-fluid">

<div class="row">

    <!-- SIDEBAR -->

    <aside class="col-md-3 col-lg-2 bg-dark text-white p-3 position-sticky top-0 vh-100 overflow-auto align-self-start">

        <h5 class="fw-bold mb-1">Sistem Informasi</h5>

        <h6 class="fw-bold">Pelanggaran Siswa</h6>

        <p class="text-secondary small mb-4">
            SMK Muhammadiyah Kota Tasikmalaya
        </p>

        <p class="text-secondary small fw-bold mb-2">
            MENU NAVIGASI
        </p>

        <nav class="nav flex-column gap-1">

            <a href="dashboard.php"
               class="nav-link text-white rounded py-2">
                Dashboard
            </a>

            <a href="data_catatan_pelanggaran.php"
               class="nav-link active bg-primary text-white rounded py-2">
                Catatan Pelanggaran
            </a>

            <?php if ($role == 'admin') { ?>

                <a href="data_tindakan.php"
                   class="nav-link text-white rounded py-2">
                    Tindakan
                </a>

                <a href="walikelas/data_siswa.php"
                   class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="walikelas/data_laporan.php"
                   class="nav-link text-white rounded py-2">
                    Laporan
                </a>

            <?php } elseif ($isWaliKelas) { ?>

                <a href="walikelas/data_siswa.php"
                   class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

            <?php } else { ?>

                <a href="data_tindakan.php"
                   class="nav-link text-white rounded py-2">
                    Tindakan
                </a>

            <?php } ?>

            <a href="data_riwayat.php"
               class="nav-link text-white rounded py-2">
                Riwayat
            </a>

            <a href="data_rekap_poin.php"
               class="nav-link text-white rounded py-2">
                Rekap Poin
            </a>

            <?php if ($role == 'admin' || $isWaliKelas) { ?>

                <a href="walikelas/data_laporan.php"
                   class="nav-link text-white rounded py-2">
                    Laporan
                </a>

            <?php } ?>

            <a href="about_me.php"
               class="nav-link text-white rounded py-2">
                About Me
            </a>

            <hr class="border-secondary my-2">

            <a href="logout.php"
               class="nav-link text-danger rounded py-2">
                Logout
            </a>

        </nav>

    </aside>

    <!-- KONTEN -->

    <main class="col-md-9 col-lg-10 p-0">

        <!-- HEADER -->

        <header class="bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">

            <div>

                <h4 class="fw-bold text-dark mb-1">
                    Catatan Pelanggaran
                </h4>

                <small class="text-secondary">
                    Sistem Informasi Pelanggaran Siswa
                </small>

            </div>

            <div class="d-flex align-items-center gap-2">

                <div class="text-end">

                    <div class="fw-bold">
                        <?= htmlspecialchars($nama); ?>
                    </div>

                    <small class="text-secondary">
                        <?= htmlspecialchars(ucfirst($role)); ?>
                    </small>

                </div>

                <div class="bg-primary-subtle text-primary rounded-circle d-flex justify-content-center align-items-center fw-bold flex-shrink-0"
                     style="width:42px;height:42px;">

                    <?= strtoupper(substr($nama, 0, 1)); ?>

                </div>

            </div>

        </header>

        <!-- ISI HALAMAN -->

        <section class="p-3 p-md-4">

            <div class="card border-0 shadow-sm rounded-3">

                <div class="card-header bg-white p-3 p-md-4">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Data Catatan Pelanggaran
                            </h5>

                            <small class="text-secondary">

                                <?php if ($role == 'guru') { ?>

                                    Daftar catatan pelanggaran yang Anda buat.

                                <?php } else { ?>

                                    Daftar seluruh catatan pelanggaran siswa.

                                <?php } ?>

                            </small>

                        </div>

                        <button
                            type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalTambahCatatan">

                            Tambah Catatan

                        </button>

                    </div>

                </div>

                <div class="card-body p-3 p-md-4">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Nama Guru</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (count($dataCatatan) > 0) { ?>

                                    <?php $no = 1; ?>

                                    <?php foreach ($dataCatatan as $catatan) { ?>

                                        <tr>

                                            <td>
                                                <?= $no++; ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($catatan['nama_siswa']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($catatan['nama_kelas']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($catatan['nama_pelanggaran']); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($catatan['nama_guru']); ?>
                                            </td>

                                            <td>
                                                <?= !empty($catatan['tanggal'])
                                                    ? date('d-m-Y', strtotime($catatan['tanggal']))
                                                    : '-'; ?>
                                            </td>

                                            <td>
                                                <?= (int) $catatan['poin']; ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($catatan['tindakan'] ?: '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($catatan['status']); ?>
                                            </td>

                                            <td>

                                                <form method="POST"
                                                      action="data_catatan_pelanggaran.php"
                                                      onsubmit="return confirm('Yakin ingin memperbarui status catatan ini?');">

                                                    <input
                                                        type="hidden"
                                                        name="catatan_id"
                                                        value="<?= (int) $catatan['id']; ?>">

                                                    <select
                                                        name="status_baru"
                                                        class="form-select form-select-sm mb-2"
                                                        required>

                                                        <option
                                                            value="Diproses"
                                                            <?= $catatan['status'] == 'Diproses' ? 'selected' : ''; ?>>
                                                            Diproses
                                                        </option>

                                                        <option
                                                            value="Ditindaklanjuti"
                                                            <?= $catatan['status'] == 'Ditindaklanjuti' ? 'selected' : ''; ?>>
                                                            Ditindaklanjuti
                                                        </option>

                                                        <option value="Selesai">
                                                            Selesai
                                                        </option>

                                                    </select>

                                                    <button
                                                        type="submit"
                                                        name="perbarui_status"
                                                        class="btn btn-success btn-sm w-100">

                                                        Perbarui

                                                    </button>

                                                </form>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                <?php } else { ?>

                                    <tr>

                                        <td colspan="10" class="text-center text-secondary">

                                            Belum ada data catatan pelanggaran.

                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->

            <footer class="text-center text-secondary small mt-4">

                Sistem Informasi Pelanggaran Siswa &copy; 2026

            </footer>

        </section>

    </main>

</div>
</div>

<!-- ====================================== -->
<!-- MODAL TAMBAH CATATAN -->
<!-- ====================================== -->

<div class="modal fade"
     id="modalTambahCatatan"
     tabindex="-1"
     aria-labelledby="judulModalTambah"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title fw-bold" id="judulModalTambah">
                        Tambah Catatan Pelanggaran
                    </h5>

                    <small class="text-secondary">
                        Silakan isi informasi pelanggaran siswa.
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            <div class="modal-body p-3 p-md-4">

                <form method="POST"
                      action="data_catatan_pelanggaran.php"
                      id="formTambahCatatan">

                    <!-- NAMA SISWA -->

                    <div class="mb-3">

                        <label for="tambah_siswa" class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <select
                            name="siswa_id"
                            id="tambah_siswa"
                            class="form-select"
                            required>

                            <option value=""></option>

                            <?php foreach ($dataSiswa as $siswa) { ?>

                                <option
                                    value="<?= (int) $siswa['siswa_id']; ?>"
                                    data-kelas="<?= htmlspecialchars($siswa['nama_kelas'], ENT_QUOTES, 'UTF-8'); ?>">

                                    <?= htmlspecialchars($siswa['nama_siswa']); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- KELAS -->

                    <div class="mb-3">

                        <label for="tambah_kelas" class="form-label fw-semibold">
                            Kelas
                        </label>

                        <input
                            type="text"
                            id="tambah_kelas"
                            class="form-control"
                            placeholder="Kelas otomatis terisi"
                            readonly>

                    </div>

                    <!-- NAMA PELANGGARAN -->

                    <div class="mb-3">

                        <label for="tambah_pelanggaran" class="form-label fw-semibold">
                            Nama Pelanggaran
                        </label>

                        <select
                            name="pelanggaran_id"
                            id="tambah_pelanggaran"
                            class="form-select"
                            required>

                            <option value="">
                                Pilih Nama Pelanggaran
                            </option>

                            <?php foreach ($dataPelanggaran as $pelanggaran) { ?>

                                <option
                                    value="<?= (int) $pelanggaran['id']; ?>"
                                    data-poin="<?= (int) $pelanggaran['poin']; ?>">

                                    <?= htmlspecialchars($pelanggaran['nama']); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- NAMA GURU -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Nama Guru Pencatat
                        </label>

                        <?php if ($role == 'guru') { ?>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($namaGuruLogin); ?>"
                                readonly>

                        <?php } else { ?>

                            <select
                                name="guru_id"
                                id="tambah_guru"
                                class="form-select"
                                required>

                                <option value=""></option>

                                <?php foreach ($dataGuru as $guru) { ?>

                                    <option value="<?= (int) $guru['id']; ?>">

                                        <?= htmlspecialchars($guru['nama']); ?>

                                    </option>

                                <?php } ?>

                            </select>

                        <?php } ?>

                    </div>

                    <!-- TANGGAL -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            id="tanggal_otomatis"
                            class="form-control"
                            required>

                    </div>

                    <!-- POIN -->

                    <div class="mb-3">

                        <label for="tambah_poin" class="form-label fw-semibold">
                            Poin
                        </label>

                        <input
                            type="number"
                            id="tambah_poin"
                            class="form-control"
                            min="0"
                            value="0"
                            readonly>

                    </div>

                    <!-- KETERANGAN -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan keterangan jika diperlukan"></textarea>

                    </div>

                    <!-- TINDAKAN -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Tindakan
                        </label>

                        <select name="tindakan" class="form-select">

                            <option value="">
                                Pilih Tindakan
                            </option>

                            <?php foreach ($daftarTindakan as $tindakan) { ?>

                                <option value="<?= htmlspecialchars($tindakan); ?>">

                                    <?= htmlspecialchars($tindakan); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- STATUS -->

                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                            required>

                            <option value="">
                                Pilih Status
                            </option>

                            <option value="Diproses">
                                Diproses
                            </option>

                            <option value="Ditindaklanjuti">
                                Ditindaklanjuti
                            </option>

                            <option value="Selesai">
                                Selesai
                            </option>

                        </select>

                    </div>

                    <!-- TOMBOL -->

                    <div class="d-flex justify-content-end gap-2">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button
                            type="reset"
                            class="btn btn-outline-secondary">

                            Reset

                        </button>

                        <button
                            type="submit"
                            name="simpan_tambah"
                            class="btn btn-primary">

                            Simpan Catatan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<!-- ====================================== -->
<!-- TOAST NOTIFIKASI -->
<!-- ====================================== -->

<?php if (isset($_SESSION['pesan_hapus'])) {

    $jenis = $_SESSION['jenis_pesan'] ?? 'success';

    $jenis = in_array($jenis, [
        'success',
        'danger',
        'warning',
        'info',
        'primary',
        'secondary',
        'dark'
    ]) ? $jenis : 'success';

?>

<div class="toast-container position-fixed top-0 end-0 p-3"
     style="z-index:9999;">

    <div
        id="notifikasiToast"
        class="toast align-items-center text-bg-<?= $jenis; ?> border-0"
        role="alert"
        aria-live="assertive"
        aria-atomic="true">

        <div class="d-flex">

            <div class="toast-body">

                <?= htmlspecialchars($_SESSION['pesan_hapus']); ?>

            </div>

            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"
                aria-label="Close">
            </button>

        </div>

    </div>

</div>

<?php

unset($_SESSION['pesan_hapus'], $_SESSION['jenis_pesan']);

}
?>

<!-- ====================================== -->
<!-- JAVASCRIPT -->
<!-- ====================================== -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// PENCARIAN NAMA SISWA

$('#tambah_siswa').select2({
    theme: 'bootstrap-5',
    placeholder: 'Cari nama siswa...',
    allowClear: true,
    width: '100%',
    dropdownParent: $('#modalTambahCatatan')
});

// PENCARIAN NAMA GURU KHUSUS ADMIN

<?php if ($role == 'admin') { ?>

$('#tambah_guru').select2({
    theme: 'bootstrap-5',
    placeholder: 'Cari nama guru...',
    allowClear: true,
    width: '100%',
    dropdownParent: $('#modalTambahCatatan')
});

<?php } ?>

// KELAS OTOMATIS SETELAH MEMILIH SISWA

$('#tambah_siswa').on('change', function() {

    const kelas = $(this).find(':selected').attr('data-kelas') || '';

    $('#tambah_kelas').val(kelas);

});

// POIN OTOMATIS

document.getElementById('tambah_pelanggaran').addEventListener('change', function() {

    const pilihan = this.options[this.selectedIndex];

    document.getElementById('tambah_poin').value =
        pilihan.getAttribute('data-poin') || 0;

});

// RESET FORM

document.getElementById('formTambahCatatan').addEventListener('reset', function() {

    setTimeout(function() {

        $('#tambah_siswa').val('').trigger('change');

        <?php if ($role == 'admin') { ?>

        $('#tambah_guru').val('').trigger('change');

        <?php } ?>

        document.getElementById('tambah_kelas').value = '';

        document.getElementById('tambah_pelanggaran').value = '';

        document.getElementById('tambah_poin').value = 0;

        isiTanggalHariIni();

    }, 0);

});

// NOTIFIKASI TOAST

document.addEventListener('DOMContentLoaded', function() {

    const toastElement = document.getElementById('notifikasiToast');

    if (toastElement) {

        new bootstrap.Toast(toastElement, {
            delay: 3000
        }).show();

    }

});

// TANGGAL OTOMATIS

function isiTanggalHariIni() {

    const tanggal = new Date();

    const tahun = tanggal.getFullYear();

    const bulan = String(tanggal.getMonth() + 1).padStart(2, '0');

    const hari = String(tanggal.getDate()).padStart(2, '0');

    document.getElementById('tanggal_otomatis').value =
        `${tahun}-${bulan}-${hari}`;

}

isiTanggalHariIni();

</script>

</body>
</html>
