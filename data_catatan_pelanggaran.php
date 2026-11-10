<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (
    !isset($_SESSION['role']) ||
    !in_array($_SESSION['role'], ['admin', 'guru'])
) {
    exit("Anda tidak memiliki akses");
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'] ?? 'Pengguna';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* =====================================
   FUNGSI NOTIFIKASI
===================================== */

function setPesan($pesan, $jenis = 'success')
{
    $_SESSION['pesan_hapus'] = $pesan;
    $_SESSION['jenis_pesan'] = $jenis;
}

/* =====================================
   MEMBERSIHKAN NAMA PELANGGARAN
===================================== */

function bersihkanNamaPelanggaran($nama)
{
    return trim(preg_replace('/\s+[0-9]+$/', '', $nama));
}

/* =====================================
   MENGAMBIL DATA PENEMPATAN SISWA AKTIF
===================================== */

$dataSiswa = [];

$querySiswa = mysqli_query($koneksi, "
    SELECT
        s.id AS siswa_id,
        s.nama AS nama_siswa,
        ks.kelas_id,
        k.nama AS nama_kelas,
        ks.tahun_ajaran_id
    FROM t_siswa s
    JOIN (
        SELECT ks1.*
        FROM t_kelas_siswa ks1
        JOIN (
            SELECT siswa_id, MAX(id) AS id_terbaru
            FROM t_kelas_siswa
            WHERE status_aktif = 1
            GROUP BY siswa_id
        ) terbaru ON ks1.id = terbaru.id_terbaru
    ) ks ON ks.siswa_id = s.id
    JOIN t_kelas k ON k.id = ks.kelas_id
    WHERE ks.status_aktif = 1
    ORDER BY s.nama ASC
");

while ($row = mysqli_fetch_assoc($querySiswa)) {
    $dataSiswa[] = $row;
}

/* =====================================
   MENGAMBIL DATA PELANGGARAN
   NAMA DARI KATEGORI
   POIN DARI JENIS PELANGGARAN
===================================== */

$dataPelanggaran = [];

$queryPelanggaran = mysqli_query($koneksi, "
    SELECT
        p.id,
        p.pelanggaran_kategori_id,
        p.poin,
        pk.nama AS nama_kategori
    FROM t_pelanggaran p
    INNER JOIN t_pelanggaran_kategori pk
        ON pk.id = p.pelanggaran_kategori_id
    WHERE p.status_aktif = 1
      AND pk.status_aktif = 1
    ORDER BY pk.id ASC, p.id ASC
");

while ($row = mysqli_fetch_assoc($queryPelanggaran)) {
    $row['nama'] = bersihkanNamaPelanggaran($row['nama_kategori']);
    $dataPelanggaran[] = $row;
}

/* =====================================
   MENGAMBIL DATA GURU
===================================== */

$dataGuru = [];

$queryGuru = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

while ($row = mysqli_fetch_assoc($queryGuru)) {
    $dataGuru[] = $row;
}

/* =====================================
   DAFTAR TINDAKAN
===================================== */

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

/* =====================================
   FUNGSI MENGAMBIL DATA SISWA
===================================== */

function ambilDataSiswa($koneksi, $siswa_id)
{
    $stmt = mysqli_prepare($koneksi, "
        SELECT
            s.id AS siswa_id,
            s.nama AS nama_siswa,
            ks.kelas_id,
            k.nama AS nama_kelas,
            ks.tahun_ajaran_id
        FROM t_siswa s
        JOIN t_kelas_siswa ks
            ON ks.siswa_id = s.id
        JOIN t_kelas k
            ON k.id = ks.kelas_id
        WHERE s.id = ?
          AND ks.status_aktif = 1
        ORDER BY ks.id DESC
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, "i", $siswa_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $data ?: false;
}

/* =====================================
   FUNGSI MENGAMBIL DATA PELANGGARAN
===================================== */

function ambilDataPelanggaran($koneksi, $pelanggaran_id)
{
    $stmt = mysqli_prepare($koneksi, "
        SELECT
            p.id,
            p.pelanggaran_kategori_id,
            p.poin,
            pk.nama AS nama_kategori
        FROM t_pelanggaran p
        INNER JOIN t_pelanggaran_kategori pk
            ON pk.id = p.pelanggaran_kategori_id
        WHERE p.id = ?
          AND p.status_aktif = 1
          AND pk.status_aktif = 1
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, "i", $pelanggaran_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if ($data) {
        $data['nama'] = bersihkanNamaPelanggaran(
            $data['nama_kategori']
        );
    }

    return $data ?: false;
}

/* =====================================
   FUNGSI MENGAMBIL DATA GURU
===================================== */

function ambilDataGuru($koneksi, $guru_id)
{
    $stmt = mysqli_prepare($koneksi, "
        SELECT id, nama
        FROM t_guru
        WHERE id = ?
          AND status_aktif = 1
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, "i", $guru_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $data ?: false;
}

/* =====================================
   PROSES TAMBAH CATATAN PELANGGARAN
===================================== */

if (isset($_POST['simpan_tambah'])) {

    try {

        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
        $guru_id = (int) ($_POST['guru_id'] ?? 0);

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

        if (!$siswa) {
            throw new Exception("Data siswa atau kelas aktif tidak ditemukan.");
        }

        $pelanggaran = ambilDataPelanggaran(
            $koneksi,
            $pelanggaran_id
        );

        if (!$pelanggaran) {
            throw new Exception("Data pelanggaran tidak ditemukan.");
        }

        $guru = ambilDataGuru($koneksi, $guru_id);

        if (!$guru) {
            throw new Exception("Data guru tidak ditemukan.");
        }

        $tahun_ajaran_id = (int) $siswa['tahun_ajaran_id'];
        $nama_siswa = $siswa['nama_siswa'];

        $kelas_id = (int) $siswa['kelas_id'];
        $nama_kelas = $siswa['nama_kelas'];

        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) $pelanggaran['pelanggaran_kategori_id'];
        $poin = (int) $pelanggaran['poin'];

        $nama_guru = $guru['nama'];

        $simpan = mysqli_prepare($koneksi, "
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
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $simpan,
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

        mysqli_stmt_execute($simpan);
        mysqli_stmt_close($simpan);

        setPesan(
            "Catatan pelanggaran berhasil ditambahkan.",
            "success"
        );

    } catch (Throwable $e) {

        setPesan(
            "Gagal menambahkan catatan: " . $e->getMessage(),
            "danger"
        );
    }

    header("Location: data_catatan_pelanggaran.php");
    exit;
}

/* =====================================
   PROSES EDIT CATATAN PELANGGARAN
===================================== */

if (isset($_POST['simpan_edit'])) {

    try {

        $id = (int) ($_POST['id'] ?? 0);
        $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
        $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
        $guru_id = (int) ($_POST['guru_id'] ?? 0);

        $tanggal = trim($_POST['tanggal'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $tindakan = trim($_POST['tindakan'] ?? '');
        $status = trim($_POST['status'] ?? '');

        if (
            $id <= 0 ||
            $siswa_id <= 0 ||
            $pelanggaran_id <= 0 ||
            $guru_id <= 0 ||
            $tanggal == '' ||
            $status == ''
        ) {
            throw new Exception("Semua data wajib diisi.");
        }

        $siswa = ambilDataSiswa($koneksi, $siswa_id);

        if (!$siswa) {
            throw new Exception("Data siswa atau kelas aktif tidak ditemukan.");
        }

        $pelanggaran = ambilDataPelanggaran(
            $koneksi,
            $pelanggaran_id
        );

        if (!$pelanggaran) {
            throw new Exception("Data pelanggaran tidak ditemukan.");
        }

        $guru = ambilDataGuru($koneksi, $guru_id);

        if (!$guru) {
            throw new Exception("Data guru tidak ditemukan.");
        }

        $tahun_ajaran_id = (int) $siswa['tahun_ajaran_id'];
        $nama_siswa = $siswa['nama_siswa'];

        $kelas_id = (int) $siswa['kelas_id'];
        $nama_kelas = $siswa['nama_kelas'];

        $nama_pelanggaran = $pelanggaran['nama'];
        $kategori_id = (int) $pelanggaran['pelanggaran_kategori_id'];
        $poin = (int) $pelanggaran['poin'];

        $nama_guru = $guru['nama'];

        $update = mysqli_prepare($koneksi, "
            UPDATE t_pelanggaran_siswa SET
                tahun_ajaran_id = ?,
                siswa_id = ?,
                nama_siswa = ?,
                kelas_id = ?,
                nama_kelas = ?,
                pelanggaran_id = ?,
                nama_pelanggaran = ?,
                pelanggaran_kategori_id = ?,
                guru_id = ?,
                nama_guru = ?,
                tanggal = ?,
                keterangan = ?,
                poin = ?,
                tindakan = ?,
                status = ?
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $update,
            "iisisiisiississi",
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
            $status,
            $id
        );

        mysqli_stmt_execute($update);
        mysqli_stmt_close($update);

        setPesan(
            "Catatan pelanggaran berhasil diperbarui.",
            "success"
        );

    } catch (Throwable $e) {

        setPesan(
            "Gagal mengubah catatan: " . $e->getMessage(),
            "danger"
        );
    }

    header("Location: data_catatan_pelanggaran.php");
    exit;
}

/* =====================================
   MENGAMBIL DATA CATATAN PELANGGARAN
   NAMA DIAMBIL LANGSUNG DARI TABEL SUMBER
===================================== */

$query = mysqli_query($koneksi, "
    SELECT
        ps.id,
        ps.tahun_ajaran_id,
        ps.siswa_id,
        COALESCE(s.nama, ps.nama_siswa) AS nama_siswa,
        ps.kelas_id,
        COALESCE(k.nama, ps.nama_kelas) AS nama_kelas,
        ps.pelanggaran_id,
        CASE
            WHEN pk.nama IS NOT NULL THEN
                TRIM(REGEXP_REPLACE(pk.nama, '[[:space:]]+[0-9]+$', ''))
            ELSE ps.nama_pelanggaran
        END AS nama_pelanggaran,
        ps.pelanggaran_kategori_id,
        ps.guru_id,
        COALESCE(g.nama, ps.nama_guru) AS nama_guru,
        ps.tanggal,
        ps.keterangan,
        COALESCE(p.poin, ps.poin) AS poin,
        ps.tindakan,
        ps.status
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_siswa s
        ON s.id = ps.siswa_id
    LEFT JOIN t_kelas k
        ON k.id = ps.kelas_id
    LEFT JOIN t_pelanggaran p
        ON p.id = ps.pelanggaran_id
    LEFT JOIN t_pelanggaran_kategori pk
        ON pk.id = p.pelanggaran_kategori_id
    LEFT JOIN t_guru g
        ON g.id = ps.guru_id
    ORDER BY ps.id DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catatan Pelanggaran - Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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

                <a href="dashboard.php" class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <a href="data_catatan_pelanggaran.php" class="nav-link active bg-primary text-white rounded py-2">
                    Catatan Pelanggaran
                </a>

                <a href="data_tindakan.php" class="nav-link text-white rounded py-2">
                    Tindakan
                </a>

                <a href="data_riwayat.php" class="nav-link text-white rounded py-2">
                    Riwayat
                </a>

                <a href="data_rekap_poin.php" class="nav-link text-white rounded py-2">
                    Rekap Poin
                </a>

                <a href="data_laporan.php" class="nav-link text-white rounded py-2">
                    Laporan
                </a>
               

                <a href="about_me.php" class="nav-link text-white rounded py-2">
                    About Me
                </a>

                <hr class="border-secondary my-2">

                <a href="logout.php" class="nav-link text-danger rounded py-2">
                    Logout
                </a>

            </nav>
        </aside>

        <!-- KONTEN UTAMA -->
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
                         style="width: 42px; height: 42px;">
                        <?= strtoupper(substr($nama, 0, 1)); ?>
                    </div>

                </div>

            </header>

            <!-- ISI HALAMAN -->
            <section class="p-3 p-md-4">

                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

                        <div>
                            <h5 class="fw-bold mb-1">
                                Daftar Catatan Pelanggaran
                            </h5>

                            <small class="text-secondary">
                                Informasi pelanggaran, poin, dan tindakan siswa.
                            </small>
                        </div>

                        <button type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTambahPelanggaran">
                            + Tambah Catatan
                        </button>

                    </div>

                    <!-- TABEL -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelPelanggaran"
                                   class="table table-bordered table-hover align-middle mb-0">

                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Nama Pelanggaran</th>
                                        <th>Guru</th>
                                        <th>Tanggal</th>
                                        <th>Poin</th>
                                        <th>Keterangan</th>
                                        <th>Tindakan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $no = 1;

                                    while ($data = mysqli_fetch_assoc($query)) {
                                    ?>

                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_siswa'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_kelas'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_pelanggaran'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_guru'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['tanggal'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['poin'] ?? '0'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['keterangan'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['tindakan'] ?? ''); ?>
                                        </td>

                                        <td>

                                            <?php
                                            $statusData = $data['status'] ?? '';
                                            $warnaStatus = 'secondary';

                                            if ($statusData == 'Selesai') {
                                                $warnaStatus = 'success';
                                            } elseif ($statusData == 'Diproses') {
                                                $warnaStatus = 'warning';
                                            } elseif ($statusData == 'Ditindaklanjuti') {
                                                $warnaStatus = 'primary';
                                            }
                                            ?>

                                            <span class="badge bg-<?= $warnaStatus; ?>">
                                                <?= htmlspecialchars($statusData); ?>
                                            </span>

                                        </td>

                                        <td class="text-nowrap">

                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm tombolEdit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditPelanggaran"

                                                data-id="<?= (int) $data['id']; ?>"
                                                data-siswa="<?= (int) $data['siswa_id']; ?>"
                                                data-pelanggaran="<?= (int) $data['pelanggaran_id']; ?>"
                                                data-guru="<?= (int) $data['guru_id']; ?>"
                                                data-tanggal="<?= htmlspecialchars($data['tanggal'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-keterangan="<?= htmlspecialchars($data['keterangan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-tindakan="<?= htmlspecialchars($data['tindakan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-status="<?= htmlspecialchars($data['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                                                Edit

                                            </button>

                                            <a href="hapus_pelanggaran_siswa.php?id=<?= (int) $data['id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus catatan pelanggaran ini?')">
                                                Hapus
                                            </a>

                                        </td>

                                    </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <div class="card-footer bg-white p-3">

                        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                            Kembali ke Dashboard
                        </a>

                    </div>

                </div>

                <footer class="text-center text-secondary small mt-4">
                    Sistem Informasi Pelanggaran Siswa &copy; 2026
                </footer>

            </section>

        </main>

    </div>
</div>

<!-- =====================================
     MODAL TAMBAH
===================================== -->

<div class="modal fade" id="modalTambahPelanggaran" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow rounded-3">

            <div class="modal-header bg-white">

                <div>
                    <h5 class="modal-title fw-bold">
                        Tambah Catatan Pelanggaran
                    </h5>

                    <small class="text-secondary">
                        Pilih informasi catatan pelanggaran siswa.
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <form method="POST" action="data_catatan_pelanggaran.php">

                <div class="modal-body">

                    <div class="mb-3">

                        <label for="tambah_siswa" class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <select name="siswa_id" id="tambah_siswa" class="form-select" required>

                            <option value="">Pilih Nama Siswa</option>

                            <?php foreach ($dataSiswa as $siswa) { ?>

                                <option
                                    value="<?= (int) $siswa['siswa_id']; ?>"
                                    data-kelas="<?= htmlspecialchars($siswa['nama_kelas'], ENT_QUOTES, 'UTF-8'); ?>">

                                    <?= htmlspecialchars($siswa['nama_siswa']); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_kelas" class="form-label fw-semibold">
                            Kelas
                        </label>

                        <input type="text"
                               id="tambah_kelas"
                               class="form-control"
                               placeholder="Kelas otomatis terisi"
                               readonly>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_pelanggaran" class="form-label fw-semibold">
                            Nama Pelanggaran
                        </label>

                        <select name="pelanggaran_id"
                                id="tambah_pelanggaran"
                                class="form-select"
                                required>

                            <option value="">Pilih Nama Pelanggaran</option>

                            <?php foreach ($dataPelanggaran as $pelanggaran) { ?>

                                <option
                                    value="<?= (int) $pelanggaran['id']; ?>"
                                    data-poin="<?= (int) $pelanggaran['poin']; ?>">

                                    <?= htmlspecialchars($pelanggaran['nama']); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_guru" class="form-label fw-semibold">
                            Nama Guru
                        </label>

                        <select name="guru_id" id="tambah_guru" class="form-select" required>

                            <option value="">Pilih Nama Guru</option>

                            <?php foreach ($dataGuru as $guru) { ?>

                                <option value="<?= (int) $guru['id']; ?>">
                                    <?= htmlspecialchars($guru['nama']); ?>
                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_tanggal" class="form-label fw-semibold">
                            Tanggal
                        </label>

                        <input type="date"
                               name="tanggal"
                               id="tambah_tanggal"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_poin" class="form-label fw-semibold">
                            Poin
                        </label>

                        <input type="number"
                               id="tambah_poin"
                               class="form-control"
                               min="0"
                               value="0"
                               readonly>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_keterangan" class="form-label fw-semibold">
                            Keterangan
                        </label>

                        <textarea name="keterangan"
                                  id="tambah_keterangan"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Masukkan keterangan jika diperlukan"></textarea>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_tindakan" class="form-label fw-semibold">
                            Tindakan
                        </label>

                        <select name="tindakan" id="tambah_tindakan" class="form-select">

                            <option value="">Pilih Tindakan</option>

                            <?php foreach ($daftarTindakan as $tindakan) { ?>

                                <option value="<?= htmlspecialchars($tindakan); ?>">
                                    <?= htmlspecialchars($tindakan); ?>
                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_status" class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status" id="tambah_status" class="form-select" required>

                            <option value="">Pilih Status</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                            <option value="Selesai">Selesai</option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit" name="simpan_tambah" class="btn btn-primary">
                        Simpan
                    </button>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- =====================================
     MODAL EDIT
===================================== -->

<div class="modal fade" id="modalEditPelanggaran" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow rounded-3">

            <div class="modal-header bg-white">

                <div>
                    <h5 class="modal-title fw-bold">
                        Edit Catatan Pelanggaran
                    </h5>

                    <small class="text-secondary">
                        Perbarui informasi catatan pelanggaran siswa.
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <form method="POST" action="data_catatan_pelanggaran.php">

                <div class="modal-body">

                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">

                        <label for="edit_siswa" class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <select name="siswa_id" id="edit_siswa" class="form-select" required>

                            <option value="">Pilih Nama Siswa</option>

                            <?php foreach ($dataSiswa as $siswa) { ?>

                                <option
                                    value="<?= (int) $siswa['siswa_id']; ?>"
                                    data-kelas="<?= htmlspecialchars($siswa['nama_kelas'], ENT_QUOTES, 'UTF-8'); ?>">

                                    <?= htmlspecialchars($siswa['nama_siswa']); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="edit_kelas" class="form-label fw-semibold">
                            Kelas
                        </label>

                        <input type="text"
                               id="edit_kelas"
                               class="form-control"
                               readonly>

                    </div>

                    <div class="mb-3">

                        <label for="edit_pelanggaran" class="form-label fw-semibold">
                            Nama Pelanggaran
                        </label>

                        <select name="pelanggaran_id"
                                id="edit_pelanggaran"
                                class="form-select"
                                required>

                            <option value="">Pilih Nama Pelanggaran</option>

                            <?php foreach ($dataPelanggaran as $pelanggaran) { ?>

                                <option
                                    value="<?= (int) $pelanggaran['id']; ?>"
                                    data-poin="<?= (int) $pelanggaran['poin']; ?>">

                                    <?= htmlspecialchars($pelanggaran['nama']); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="edit_guru" class="form-label fw-semibold">
                            Nama Guru
                        </label>

                        <select name="guru_id" id="edit_guru" class="form-select" required>

                            <option value="">Pilih Nama Guru</option>

                            <?php foreach ($dataGuru as $guru) { ?>

                                <option value="<?= (int) $guru['id']; ?>">
                                    <?= htmlspecialchars($guru['nama']); ?>
                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="edit_tanggal" class="form-label fw-semibold">
                            Tanggal
                        </label>

                        <input type="date"
                               name="tanggal"
                               id="edit_tanggal"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_poin" class="form-label fw-semibold">
                            Poin
                        </label>

                        <input type="number"
                               id="edit_poin"
                               class="form-control"
                               min="0"
                               readonly>

                    </div>

                    <div class="mb-3">

                        <label for="edit_keterangan" class="form-label fw-semibold">
                            Keterangan
                        </label>

                        <textarea name="keterangan"
                                  id="edit_keterangan"
                                  class="form-control"
                                  rows="3"></textarea>

                    </div>

                    <div class="mb-3">

                        <label for="edit_tindakan" class="form-label fw-semibold">
                            Tindakan
                        </label>

                        <select name="tindakan" id="edit_tindakan" class="form-select">

                            <option value="">Pilih Tindakan</option>

                            <?php foreach ($daftarTindakan as $tindakan) { ?>

                                <option value="<?= htmlspecialchars($tindakan); ?>">
                                    <?= htmlspecialchars($tindakan); ?>
                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label for="edit_status" class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status" id="edit_status" class="form-select" required>

                            <option value="">Pilih Status</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                            <option value="Selesai">Selesai</option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit" name="simpan_edit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- =====================================
     NOTIFIKASI TOAST
===================================== -->

<?php if (isset($_SESSION['pesan_hapus'])) { ?>

    <?php
    $jenis_pesan = $_SESSION['jenis_pesan'] ?? 'success';

    $jenis_pesan = in_array(
        $jenis_pesan,
        ['success', 'danger', 'warning', 'info', 'primary', 'secondary', 'dark']
    ) ? $jenis_pesan : 'success';
    ?>

    <div class="toast-container position-fixed top-0 end-0 p-3"
         style="z-index: 9999;">

        <div id="notifikasiToast"
             class="toast align-items-center text-bg-<?= htmlspecialchars($jenis_pesan); ?> border-0"
             role="alert"
             aria-live="assertive"
             aria-atomic="true">

            <div class="d-flex">

                <div class="toast-body">
                    <?= htmlspecialchars($_SESSION['pesan_hapus']); ?>
                </div>

                <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"
                        aria-label="Close">
                </button>

            </div>

        </div>

    </div>

    <?php
    unset($_SESSION['pesan_hapus']);
    unset($_SESSION['jenis_pesan']);
    ?>

<?php } ?>

<!-- =====================================
     JAVASCRIPT
===================================== -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* =====================================
   KELAS OTOMATIS
===================================== */

function isiKelas(idSiswa, idKelas) {

    const siswa = document.getElementById(idSiswa);
    const pilihan = siswa.options[siswa.selectedIndex];

    document.getElementById(idKelas).value =
        pilihan ? (pilihan.getAttribute('data-kelas') || '') : '';
}

document.getElementById('tambah_siswa').addEventListener('change', function () {
    isiKelas('tambah_siswa', 'tambah_kelas');
});

document.getElementById('edit_siswa').addEventListener('change', function () {
    isiKelas('edit_siswa', 'edit_kelas');
});

/* =====================================
   POIN OTOMATIS
===================================== */

function isiPoin(idPelanggaran, idPoin) {

    const pelanggaran = document.getElementById(idPelanggaran);
    const pilihan = pelanggaran.options[pelanggaran.selectedIndex];

    document.getElementById(idPoin).value =
        pilihan ? (pilihan.getAttribute('data-poin') || 0) : 0;
}

document.getElementById('tambah_pelanggaran').addEventListener('change', function () {
    isiPoin('tambah_pelanggaran', 'tambah_poin');
});

document.getElementById('edit_pelanggaran').addEventListener('change', function () {
    isiPoin('edit_pelanggaran', 'edit_poin');
});

/* =====================================
   DATATABLES
===================================== */

$(document).ready(function () {

    $('#tabelPelanggaran').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        columnDefs: [
            {
                targets: 10,
                orderable: false,
                searchable: false
            }
        ],

        language: {

            search: "Cari data:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data pelanggaran tidak ditemukan",

            emptyTable: "Belum ada catatan pelanggaran",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }

        }

    });

});

/* =====================================
   MENGISI MODAL EDIT
===================================== */

document.querySelectorAll('.tombolEdit').forEach(function (tombol) {

    tombol.addEventListener('click', function () {

        document.getElementById('edit_id').value =
            this.getAttribute('data-id');

        document.getElementById('edit_siswa').value =
            this.getAttribute('data-siswa');

        isiKelas('edit_siswa', 'edit_kelas');

        document.getElementById('edit_pelanggaran').value =
            this.getAttribute('data-pelanggaran');

        isiPoin('edit_pelanggaran', 'edit_poin');

        document.getElementById('edit_guru').value =
            this.getAttribute('data-guru');

        document.getElementById('edit_tanggal').value =
            this.getAttribute('data-tanggal');

        document.getElementById('edit_keterangan').value =
            this.getAttribute('data-keterangan');

        document.getElementById('edit_tindakan').value =
            this.getAttribute('data-tindakan');

        document.getElementById('edit_status').value =
            this.getAttribute('data-status');

    });

});

/* =====================================
   RESET MODAL TAMBAH
===================================== */

document.getElementById('modalTambahPelanggaran')
    .addEventListener('hidden.bs.modal', function () {

        this.querySelector('form').reset();

        document.getElementById('tambah_kelas').value = '';
        document.getElementById('tambah_poin').value = 0;

    });

/* =====================================
   NOTIFIKASI TOAST
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    var toastElement = document.getElementById("notifikasiToast");

    if (toastElement) {

        var toast = new bootstrap.Toast(toastElement, {
            delay: 3000
        });

        toast.show();

    }

});

</script>

</body>
</html>