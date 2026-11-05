
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

/* =====================================
   MENGAMBIL DATA SISWA DAN KELAS
===================================== */

$dataSiswa = [];

$querySiswa = mysqli_query($koneksi, "
    SELECT DISTINCT
        s.nama AS nama_siswa,
        k.nama AS nama_kelas
    FROM t_kelas_siswa ks
    JOIN t_siswa s ON ks.siswa_id = s.id
    JOIN t_kelas k ON ks.kelas_id = k.id
    WHERE ks.status_aktif = 1
    ORDER BY s.nama ASC
");

if ($querySiswa) {
    while ($row = mysqli_fetch_assoc($querySiswa)) {
        $dataSiswa[] = $row;
    }
}

/* =====================================
   MENGAMBIL DATA JENIS PELANGGARAN
===================================== */

$dataPelanggaran = [];

$queryPelanggaran = mysqli_query($koneksi, "
    SELECT nama, poin
    FROM t_pelanggaran
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

if ($queryPelanggaran) {
    while ($row = mysqli_fetch_assoc($queryPelanggaran)) {
        $dataPelanggaran[] = $row;
    }
}

/* =====================================
   MENGAMBIL DATA GURU
===================================== */

$dataGuru = [];

$queryGuru = mysqli_query($koneksi, "
    SELECT nama
    FROM t_guru
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

if ($queryGuru) {
    while ($row = mysqli_fetch_assoc($queryGuru)) {
        $dataGuru[] = $row;
    }
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
   FUNGSI MENGAMBIL KELAS SISWA
===================================== */

function ambilKelasSiswa($koneksi, $namaSiswa)
{
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT k.nama AS nama_kelas
         FROM t_kelas_siswa ks
         JOIN t_siswa s ON ks.siswa_id = s.id
         JOIN t_kelas k ON ks.kelas_id = k.id
         WHERE s.nama = ?
         AND ks.status_aktif = 1
         LIMIT 1"
    );

    if (!$stmt) {
        return '';
    }

    mysqli_stmt_bind_param($stmt, "s", $namaSiswa);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $data['nama_kelas'] ?? '';
}

/* =====================================
   FUNGSI MENGAMBIL POIN PELANGGARAN
===================================== */

function ambilPoinPelanggaran($koneksi, $namaPelanggaran)
{
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT poin
         FROM t_pelanggaran
         WHERE nama = ?
         AND status_aktif = 1
         LIMIT 1"
    );

    if (!$stmt) {
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "s", $namaPelanggaran);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return isset($data['poin']) ? (int) $data['poin'] : 0;
}

/* =====================================
   VALIDASI NAMA GURU
===================================== */

function guruTerdaftar($koneksi, $namaGuru)
{
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT nama
         FROM t_guru
         WHERE nama = ?
         AND status_aktif = 1
         LIMIT 1"
    );

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $namaGuru);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $valid = mysqli_num_rows($result) > 0;

    mysqli_stmt_close($stmt);

    return $valid;
}

/* =====================================
   PROSES TAMBAH DATA PELANGGARAN
===================================== */

if (isset($_POST['simpan_tambah'])) {

    $nama_siswa = trim($_POST['nama_siswa'] ?? '');
    $nama_pelanggaran = trim($_POST['nama_pelanggaran'] ?? '');
    $nama_guru = trim($_POST['nama_guru'] ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $nama_kelas = ambilKelasSiswa($koneksi, $nama_siswa);
    $poin = ambilPoinPelanggaran($koneksi, $nama_pelanggaran);

    if (
        $nama_siswa == "" ||
        $nama_kelas == "" ||
        $nama_pelanggaran == "" ||
        $nama_guru == "" ||
        $tanggal == "" ||
        $status == ""
    ) {

        $_SESSION['pesan_hapus'] = "Semua data wajib diisi.";
        $_SESSION['jenis_pesan'] = "danger";

    } elseif (!guruTerdaftar($koneksi, $nama_guru)) {

        $_SESSION['pesan_hapus'] = "Nama guru tidak ditemukan.";
        $_SESSION['jenis_pesan'] = "danger";

    } else {

        $insert = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_pelanggaran_siswa
            (
                nama_siswa,
                nama_kelas,
                nama_pelanggaran,
                nama_guru,
                tanggal,
                keterangan,
                poin,
                tindakan,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if ($insert) {

            mysqli_stmt_bind_param(
                $insert,
                "ssssssiss",
                $nama_siswa,
                $nama_kelas,
                $nama_pelanggaran,
                $nama_guru,
                $tanggal,
                $keterangan,
                $poin,
                $tindakan,
                $status
            );

            if (mysqli_stmt_execute($insert)) {
                $_SESSION['pesan_hapus'] = "Catatan pelanggaran berhasil ditambahkan.";
                $_SESSION['jenis_pesan'] = "success";
            } else {
                $_SESSION['pesan_hapus'] = "Gagal menambahkan catatan pelanggaran.";
                $_SESSION['jenis_pesan'] = "danger";
            }

            mysqli_stmt_close($insert);

        } else {

            $_SESSION['pesan_hapus'] = "Query gagal diproses: " . mysqli_error($koneksi);
            $_SESSION['jenis_pesan'] = "danger";
        }
    }

    header("Location: data_catatan_pelanggaran.php");
    exit;
}

/* =====================================
   PROSES EDIT DATA PELANGGARAN
===================================== */

if (isset($_POST['simpan_edit'])) {

    $id = (int) ($_POST['id'] ?? 0);
    $nama_siswa = trim($_POST['nama_siswa'] ?? '');
    $nama_pelanggaran = trim($_POST['nama_pelanggaran'] ?? '');
    $nama_guru = trim($_POST['nama_guru'] ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $nama_kelas = ambilKelasSiswa($koneksi, $nama_siswa);
    $poin = ambilPoinPelanggaran($koneksi, $nama_pelanggaran);

    if ($nama_kelas == '') {
        $nama_kelas = trim($_POST['nama_kelas'] ?? '');
    }

    if (
        $id <= 0 ||
        $nama_siswa == "" ||
        $nama_kelas == "" ||
        $nama_pelanggaran == "" ||
        $nama_guru == "" ||
        $tanggal == "" ||
        $status == ""
    ) {

        $_SESSION['pesan_hapus'] = "Semua data wajib diisi.";
        $_SESSION['jenis_pesan'] = "danger";

    } elseif (!guruTerdaftar($koneksi, $nama_guru)) {

        $_SESSION['pesan_hapus'] = "Nama guru tidak ditemukan.";
        $_SESSION['jenis_pesan'] = "danger";

    } else {

        $update = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran_siswa SET
                nama_siswa = ?,
                nama_kelas = ?,
                nama_pelanggaran = ?,
                nama_guru = ?,
                tanggal = ?,
                keterangan = ?,
                poin = ?,
                tindakan = ?,
                status = ?
            WHERE id = ?"
        );

        if ($update) {

            mysqli_stmt_bind_param(
                $update,
                "ssssssissi",
                $nama_siswa,
                $nama_kelas,
                $nama_pelanggaran,
                $nama_guru,
                $tanggal,
                $keterangan,
                $poin,
                $tindakan,
                $status,
                $id
            );

            if (mysqli_stmt_execute($update)) {
                $_SESSION['pesan_hapus'] = "Catatan pelanggaran berhasil diperbarui.";
                $_SESSION['jenis_pesan'] = "success";
            } else {
                $_SESSION['pesan_hapus'] = "Gagal mengubah catatan pelanggaran.";
                $_SESSION['jenis_pesan'] = "danger";
            }

            mysqli_stmt_close($update);

        } else {

            $_SESSION['pesan_hapus'] = "Query gagal diproses: " . mysqli_error($koneksi);
            $_SESSION['jenis_pesan'] = "danger";
        }
    }

    header("Location: data_catatan_pelanggaran.php");
    exit;
}

/* =====================================
   MENGAMBIL DATA PELANGGARAN
===================================== */

$query = "
    SELECT
        id,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        nama_guru,
        tanggal,
        keterangan,
        poin,
        tindakan,
        status
    FROM t_pelanggaran_siswa
    ORDER BY id DESC
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catatan Pelanggaran - Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
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

                <a href="data_pelanggaran_siswa.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Catatan Pelanggaran
                </a>

                <a href="data_tindakan.php"
                   class="nav-link text-white rounded py-2">
                    Tindakan
                </a>

                <a href="data_riwayat.php"
                   class="nav-link text-white rounded py-2">
                    Riwayat
                </a>

                <a href="data_rekap_poin.php"
                   class="nav-link text-white rounded py-2">
                    Rekap Poin
                </a>

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
                                        <th>Jenis Pelanggaran</th>
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

                                    while ($data = mysqli_fetch_assoc($result)) {
                                    ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td><?= htmlspecialchars($data['nama_siswa'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['nama_kelas'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['nama_pelanggaran'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['nama_guru'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['tanggal'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['poin'] ?? '0'); ?></td>

                                        <td><?= htmlspecialchars($data['keterangan'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['tindakan'] ?? ''); ?></td>

                                        <td>
                                            <?php
                                            $status = $data['status'] ?? '';
                                            $warnaStatus = 'secondary';

                                            if ($status == 'Selesai') {
                                                $warnaStatus = 'success';
                                            } elseif ($status == 'Diproses') {
                                                $warnaStatus = 'warning';
                                            } elseif ($status == 'Ditindaklanjuti') {
                                                $warnaStatus = 'primary';
                                            }
                                            ?>

                                            <span class="badge bg-<?= $warnaStatus; ?>">
                                                <?= htmlspecialchars($status); ?>
                                            </span>
                                        </td>

                                        <td class="text-nowrap">

                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm tombolEdit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditPelanggaran"
                                                data-id="<?= htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-siswa="<?= htmlspecialchars($data['nama_siswa'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-kelas="<?= htmlspecialchars($data['nama_kelas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-pelanggaran="<?= htmlspecialchars($data['nama_pelanggaran'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-guru="<?= htmlspecialchars($data['nama_guru'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-tanggal="<?= htmlspecialchars($data['tanggal'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-poin="<?= htmlspecialchars($data['poin'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-keterangan="<?= htmlspecialchars($data['keterangan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-tindakan="<?= htmlspecialchars($data['tindakan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-status="<?= htmlspecialchars($data['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                Edit
                                            </button>

                                            <a href="hapus_pelanggaran_siswa.php?id=<?= urlencode($data['id']); ?>"
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

                        <a href="dashboard.php"
                           class="btn btn-outline-secondary btn-sm">
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


<!-- MODAL TAMBAH -->
<div class="modal fade"
     id="modalTambahPelanggaran"
     tabindex="-1"
     aria-hidden="true">

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

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <form method="POST" action="data_catatan_pelanggaran.php">

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="tambah_siswa" class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <select name="nama_siswa"
                                id="tambah_siswa"
                                class="form-select"
                                required>

                            <option value="">Pilih Nama Siswa</option>

                            <?php foreach ($dataSiswa as $siswa) { ?>
                                <option value="<?= htmlspecialchars($siswa['nama_siswa'], ENT_QUOTES, 'UTF-8'); ?>">
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
                               name="nama_kelas"
                               id="tambah_kelas"
                               class="form-control"
                               placeholder="Kelas otomatis terisi"
                               readonly
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="tambah_pelanggaran" class="form-label fw-semibold">
                            Jenis Pelanggaran
                        </label>

                        <select name="nama_pelanggaran"
                                id="tambah_pelanggaran"
                                class="form-select"
                                required>

                            <option value="">Pilih Jenis Pelanggaran</option>

                            <?php foreach ($dataPelanggaran as $pelanggaran) { ?>
                                <option
                                    value="<?= htmlspecialchars($pelanggaran['nama'], ENT_QUOTES, 'UTF-8'); ?>"
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

                        <select name="nama_guru"
                                id="tambah_guru"
                                class="form-select"
                                required>

                            <option value="">Pilih Nama Guru</option>

                            <?php foreach ($dataGuru as $guru) { ?>
                                <option value="<?= htmlspecialchars($guru['nama'], ENT_QUOTES, 'UTF-8'); ?>">
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
                               name="poin"
                               id="tambah_poin"
                               class="form-control"
                               min="0"
                               value="0"
                               readonly
                               required>
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

                        <select name="tindakan"
                                id="tambah_tindakan"
                                class="form-select">

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

                        <select name="status"
                                id="tambah_status"
                                class="form-select"
                                required>

                            <option value="">Pilih Status</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                            <option value="Selesai">Selesai</option>

                        </select>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit"
                            name="simpan_tambah"
                            class="btn btn-primary">
                        Simpan
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- MODAL EDIT -->
<div class="modal fade"
     id="modalEditPelanggaran"
     tabindex="-1"
     aria-hidden="true">

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

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <form method="POST" action="data_catatan_pelanggaran.php">

                <div class="modal-body">

                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label for="edit_siswa" class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <select name="nama_siswa"
                                id="edit_siswa"
                                class="form-select"
                                required>

                            <option value="">Pilih Nama Siswa</option>

                            <?php foreach ($dataSiswa as $siswa) { ?>
                                <option value="<?= htmlspecialchars($siswa['nama_siswa'], ENT_QUOTES, 'UTF-8'); ?>">
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
                               name="nama_kelas"
                               id="edit_kelas"
                               class="form-control"
                               readonly
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_pelanggaran" class="form-label fw-semibold">
                            Jenis Pelanggaran
                        </label>

                        <select name="nama_pelanggaran"
                                id="edit_pelanggaran"
                                class="form-select"
                                required>

                            <option value="">Pilih Jenis Pelanggaran</option>

                            <?php foreach ($dataPelanggaran as $pelanggaran) { ?>
                                <option
                                    value="<?= htmlspecialchars($pelanggaran['nama'], ENT_QUOTES, 'UTF-8'); ?>"
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

                        <select name="nama_guru"
                                id="edit_guru"
                                class="form-select"
                                required>

                            <option value="">Pilih Nama Guru</option>

                            <?php foreach ($dataGuru as $guru) { ?>
                                <option value="<?= htmlspecialchars($guru['nama'], ENT_QUOTES, 'UTF-8'); ?>">
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
                               name="poin"
                               id="edit_poin"
                               class="form-control"
                               min="0"
                               readonly
                               required>
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

                        <select name="tindakan"
                                id="edit_tindakan"
                                class="form-select">

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

                        <select name="status"
                                id="edit_status"
                                class="form-select"
                                required>

                            <option value="">Pilih Status</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                            <option value="Selesai">Selesai</option>

                        </select>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit"
                            name="simpan_edit"
                            class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- NOTIFIKASI MELAYANG -->
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


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>

/* DATA SISWA DAN KELAS */

const dataKelasSiswa = <?= json_encode(
    $dataSiswa,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
); ?>;


/* KELAS OTOMATIS */

function isiKelas(namaSiswa, idKelas) {

    const siswa = dataKelasSiswa.find(function (item) {
        return item.nama_siswa === namaSiswa;
    });

    document.getElementById(idKelas).value =
        siswa ? siswa.nama_kelas : '';
}

document.getElementById('tambah_siswa').addEventListener('change', function () {
    isiKelas(this.value, 'tambah_kelas');
});

document.getElementById('edit_siswa').addEventListener('change', function () {
    isiKelas(this.value, 'edit_kelas');
});


/* POIN OTOMATIS */

function isiPoin(idPelanggaran, idPoin) {

    const pilihan = document.getElementById(idPelanggaran);
    const opsi = pilihan.options[pilihan.selectedIndex];

    document.getElementById(idPoin).value =
        opsi ? (opsi.getAttribute('data-poin') || 0) : 0;
}

document.getElementById('tambah_pelanggaran').addEventListener('change', function () {
    isiPoin('tambah_pelanggaran', 'tambah_poin');
});

document.getElementById('edit_pelanggaran').addEventListener('change', function () {
    isiPoin('edit_pelanggaran', 'edit_poin');
});


/* DATATABLES */

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


/* MENGISI DATA MODAL EDIT */

document.querySelectorAll('.tombolEdit').forEach(function (tombol) {

    tombol.addEventListener('click', function () {

        const siswa = this.getAttribute('data-siswa');
        const kelas = this.getAttribute('data-kelas');

        document.getElementById('edit_id').value =
            this.getAttribute('data-id');

        document.getElementById('edit_siswa').value = siswa;

        isiKelas(siswa, 'edit_kelas');

        if (document.getElementById('edit_kelas').value === '') {
            document.getElementById('edit_kelas').value = kelas;
        }

        document.getElementById('edit_pelanggaran').value =
            this.getAttribute('data-pelanggaran');

        document.getElementById('edit_guru').value =
            this.getAttribute('data-guru');

        document.getElementById('edit_tanggal').value =
            this.getAttribute('data-tanggal');

        document.getElementById('edit_poin').value =
            this.getAttribute('data-poin');

        document.getElementById('edit_keterangan').value =
            this.getAttribute('data-keterangan');

        document.getElementById('edit_tindakan').value =
            this.getAttribute('data-tindakan');

        document.getElementById('edit_status').value =
            this.getAttribute('data-status');

    });

});


/* NOTIFIKASI TOAST */

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
