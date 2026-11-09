<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

/* =====================================
   PROSES TAMBAH DATA PENEMPATAN SISWA
===================================== */

if (isset($_POST['simpan_tambah'])) {

    $siswa_id = (int) $_POST['siswa_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = !empty($_POST['tanggal_selesai'])
        ? $_POST['tanggal_selesai']
        : null;
    $status_aktif = (int) $_POST['status_aktif'];

    if (
        $siswa_id <= 0 ||
        $kelas_id <= 0 ||
        $tahun_ajaran_id <= 0 ||
        empty($tanggal_mulai)
    ) {
        $_SESSION['pesan_hapus'] = "Data penempatan siswa gagal ditambahkan. Periksa kembali isian.";
        $_SESSION['jenis_pesan'] = "danger";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_kelas_siswa
                (siswa_id, kelas_id, tahun_ajaran_id, tanggal_mulai, tanggal_selesai, status_aktif)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "iiissi",
                $siswa_id,
                $kelas_id,
                $tahun_ajaran_id,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan_hapus'] = "Data penempatan siswa berhasil ditambahkan.";
                $_SESSION['jenis_pesan'] = "success";
            } else {
                $_SESSION['pesan_hapus'] = "Data penempatan siswa gagal ditambahkan. " . mysqli_stmt_error($stmt);
                $_SESSION['jenis_pesan'] = "danger";
            }

            mysqli_stmt_close($stmt);

        } else {
            $_SESSION['pesan_hapus'] = "Query gagal diproses: " . mysqli_error($koneksi);
            $_SESSION['jenis_pesan'] = "danger";
        }
    }

    header("Location: data_penempatan_siswa.php");
    exit;
}

/* =====================================
   PROSES UPDATE DATA PENEMPATAN SISWA
===================================== */

if (isset($_POST['simpan_edit'])) {

    $id = (int) $_POST['id'];
    $siswa_id = (int) $_POST['siswa_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = !empty($_POST['tanggal_selesai'])
        ? $_POST['tanggal_selesai']
        : null;
    $status_aktif = (int) $_POST['status_aktif'];

    if (
        $id <= 0 ||
        $siswa_id <= 0 ||
        $kelas_id <= 0 ||
        $tahun_ajaran_id <= 0 ||
        empty($tanggal_mulai)
    ) {
        $_SESSION['pesan_hapus'] = "Data penempatan siswa gagal diperbarui. Periksa kembali isian.";
        $_SESSION['jenis_pesan'] = "danger";
    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_kelas_siswa
             SET siswa_id = ?,
                 kelas_id = ?,
                 tahun_ajaran_id = ?,
                 tanggal_mulai = ?,
                 tanggal_selesai = ?,
                 status_aktif = ?
             WHERE id = ?"
        );

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "iiissii",
                $siswa_id,
                $kelas_id,
                $tahun_ajaran_id,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan_hapus'] = "Data penempatan siswa berhasil diperbarui.";
                $_SESSION['jenis_pesan'] = "success";
            } else {
                $_SESSION['pesan_hapus'] = "Data penempatan siswa gagal diperbarui. " . mysqli_stmt_error($stmt);
                $_SESSION['jenis_pesan'] = "danger";
            }

            mysqli_stmt_close($stmt);

        } else {
            $_SESSION['pesan_hapus'] = "Query gagal diproses: " . mysqli_error($koneksi);
            $_SESSION['jenis_pesan'] = "danger";
        }
    }

    header("Location: data_penempatan_siswa.php");
    exit;
}

/* =====================================
   NOTIFIKASI
===================================== */

$pesan_notifikasi = $_SESSION['pesan_hapus']
    ?? $_SESSION['pesan_notifikasi']
    ?? '';

$jenis_notifikasi = $_SESSION['jenis_pesan']
    ?? $_SESSION['jenis_notifikasi']
    ?? 'success';

unset(
    $_SESSION['pesan_hapus'],
    $_SESSION['jenis_pesan'],
    $_SESSION['pesan_notifikasi'],
    $_SESSION['jenis_notifikasi']
);

if (!in_array($jenis_notifikasi, ['success', 'danger', 'warning', 'info'])) {
    $jenis_notifikasi = 'success';
}

/* =====================================
   DATA SISWA UNTUK MODAL
===================================== */

$query_siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC"
);

$query_siswa_tambah = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC"
);

/* =====================================
   DATA KELAS UNTUK MODAL
===================================== */

$query_kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

$query_kelas_tambah = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

/* =====================================
   DATA TAHUN AJARAN UNTUK MODAL
===================================== */

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

$query_tahun_tambah = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

/* =====================================
   DATA PENEMPATAN SISWA
===================================== */

$query = "
    SELECT
        s.id AS siswa_id,
        s.nis,
        s.nama AS nama_siswa,

        ks.id,
        ks.kelas_id,
        ks.tahun_ajaran_id,

        k.nama AS nama_kelas,
        ta.nama AS tahun_ajaran,

        ks.tanggal_mulai,
        ks.tanggal_selesai,
        ks.status_aktif

    FROM t_siswa s

    LEFT JOIN t_kelas_siswa ks
        ON s.id = ks.siswa_id

    LEFT JOIN t_kelas k
        ON ks.kelas_id = k.id

    LEFT JOIN t_tahun_ajaran ta
        ON ks.tahun_ajaran_id = ta.id

    ORDER BY s.id ASC
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

    <title>Data Penempatan Siswa</title>

    <!-- Bootstrap 5.3.3 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">

    <!-- SIDEBAR TETAP SAAT KONTEN DI-SCROLL -->
    <style>

        @media (min-width: 768px) {

            .sidebar-tetap {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                overflow-y: auto;
                z-index: 1030;
                width: 25%;
            }

            .konten-utama {
                margin-left: 25%;
                width: 75%;
            }

        }

        @media (min-width: 992px) {

            .sidebar-tetap {
                width: 16.666667%;
            }

            .konten-utama {
                margin-left: 16.666667%;
                width: 83.333333%;
            }

        }

    </style>

</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3 sidebar-tetap">

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

                <a href="data_siswa.php"
                   class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="data_guru.php"
                   class="nav-link text-white rounded py-2">
                    Data Guru
                </a>

                <a href="data_kelas.php"
                   class="nav-link text-white rounded py-2">
                    Kelas
                </a>

                <a href="data_tahun_ajaran.php"
                   class="nav-link text-white rounded py-2">
                    Tahun Ajaran
                </a>

                <a href="data_penempatan_siswa.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Penempatan Siswa
                </a>

                <a href="data_wali_kelas.php"
                   class="nav-link text-white rounded py-2">
                    Wali Kelas
                </a>

                <a href="data_kategori_pelanggaran.php"
                   class="nav-link text-white rounded py-2">
                    Kategori Pelanggaran
                </a>

                <a href="data_jenis_pelanggaran.php"
                   class="nav-link text-white rounded py-2">
                    Jenis Pelanggaran
                </a>

                <a href="data_laporan.php"
                   class="nav-link text-white rounded py-2">
                    Laporan
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
        <main class="col-md-9 col-lg-10 p-0 konten-utama">

            <!-- HEADER -->
            <header class="bg-white border-bottom p-3 p-md-4
                          d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        Data Penempatan Siswa
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

                        <small class="text-secondary">Admin</small>
                    </div>

                    <div class="bg-primary-subtle text-primary rounded-circle
                                d-flex justify-content-center align-items-center
                                fw-bold flex-shrink-0"
                         style="width: 42px; height: 42px;">
                        <?= strtoupper(substr($nama, 0, 1)); ?>
                    </div>

                </div>

            </header>

            <!-- ISI HALAMAN -->
            <section class="p-3 p-md-4">

                <!-- KARTU TABEL -->
                <div class="card border-0 shadow-sm rounded-3">

                    <!-- HEADER KARTU -->
                    <div class="card-header bg-white p-3 p-md-4
                                d-flex justify-content-between align-items-center
                                flex-wrap gap-3">

                        <div>
                            <h5 class="fw-bold mb-1">
                                Tabel Penempatan Siswa
                            </h5>

                            <small class="text-secondary">
                                Data penempatan yang tersimpan di sistem.
                            </small>
                        </div>

                        <button type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTambahPenempatan">
                            + Tambah Penempatan
                        </button>

                    </div>

                    <!-- TABEL DATA -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelPenempatanSiswa"
                                   class="table table-bordered table-hover
                                          align-middle w-100">

                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php
                                $no = 1;

                                if (mysqli_num_rows($result) > 0) {

                                    while ($data = mysqli_fetch_assoc($result)) {
                                ?>

                                    <tr>
                                        <td class="text-center">
                                            <?= $no++; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nis'] ?? ''); ?>
                                        </td>

                                        <td class="fw-semibold">
                                            <?= htmlspecialchars($data['nama_siswa'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?php
                                            if (!empty($data['nama_kelas'])) {
                                                echo htmlspecialchars($data['nama_kelas']);
                                            } else {
                                                echo '<span class="badge bg-secondary">Belum Ditempatkan</span>';
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            if (!empty($data['tahun_ajaran'])) {
                                                echo htmlspecialchars($data['tahun_ajaran']);
                                            } else {
                                                echo '<span class="badge bg-secondary">Belum Ditempatkan</span>';
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            if (!empty($data['tanggal_mulai'])) {
                                                echo htmlspecialchars($data['tanggal_mulai']);
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            if (!empty($data['tanggal_selesai'])) {
                                                echo htmlspecialchars($data['tanggal_selesai']);
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </td>

                                        <td class="text-center">
                                            <?php

                                            if (empty($data['id'])) {

                                                echo '<span class="badge bg-secondary">
                                                        Belum Ditempatkan
                                                      </span>';

                                            } elseif ($data['status_aktif'] == 1) {

                                                echo '<span class="badge bg-success">
                                                        Aktif
                                                      </span>';

                                            } else {

                                                echo '<span class="badge bg-danger">
                                                        Tidak Aktif
                                                      </span>';

                                            }

                                            ?>
                                        </td>

                                        <td class="text-center">

                                            <?php if (!empty($data['id'])) { ?>

                                                <div class="d-flex justify-content-center gap-1">

                                                    <button
                                                        type="button"
                                                        class="btn btn-warning btn-sm tombol-edit"
                                                        data-id="<?= (int) $data['id']; ?>"
                                                        data-siswa="<?= (int) $data['siswa_id']; ?>"
                                                        data-kelas="<?= (int) $data['kelas_id']; ?>"
                                                        data-tahun="<?= (int) $data['tahun_ajaran_id']; ?>"
                                                        data-mulai="<?= htmlspecialchars($data['tanggal_mulai'], ENT_QUOTES); ?>"
                                                        data-selesai="<?= htmlspecialchars($data['tanggal_selesai'] ?? '', ENT_QUOTES); ?>"
                                                        data-status="<?= (int) $data['status_aktif']; ?>">
                                                        Edit
                                                    </button>

                                                    <a
                                                        href="hapus_penempatan_siswa.php?id=<?= urlencode($data['id']); ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Yakin ingin menghapus data penempatan siswa ini?');">
                                                        Hapus
                                                    </a>

                                                </div>

                                            <?php } else { ?>

                                                <button
                                                    type="button"
                                                    class="btn btn-primary btn-sm tombol-tempatkan"
                                                    data-siswa="<?= (int) $data['siswa_id']; ?>">
                                                    Tempatkan
                                                </button>

                                            <?php } ?>

                                        </td>
                                    </tr>

                                <?php
                                    }

                                }
                                ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- FOOTER KARTU -->
                    <div class="card-footer bg-white p-3
                                d-flex justify-content-start align-items-center">

                        <a href="dashboard.php"
                           class="btn btn-outline-secondary btn-sm">
                            Kembali ke Dashboard
                        </a>

                    </div>

                </div>

                <!-- FOOTER HALAMAN -->
                <footer class="text-center text-secondary small mt-4">
                    Sistem Informasi Pelanggaran Siswa &copy; 2026
                </footer>

            </section>

        </main>

    </div>
</div>


<!-- MODAL TAMBAH PENEMPATAN SISWA -->
<div class="modal fade" id="modalTambahPenempatan" tabindex="-1"
     aria-labelledby="judulModalTambah" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <form method="POST" action="">

                <div class="modal-header">

                    <h5 class="modal-title fw-bold" id="judulModalTambah">
                        Tambah Penempatan Siswa
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <select name="siswa_id"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Siswa
                            </option>

                            <?php
                            while ($siswa = mysqli_fetch_assoc($query_siswa_tambah)) {
                            ?>

                                <option value="<?= (int) $siswa['id']; ?>">
                                    <?= htmlspecialchars($siswa['nis'] . ' - ' . $siswa['nama']); ?>
                                </option>

                            <?php
                            }
                            ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Kelas
                        </label>

                        <select name="kelas_id"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Kelas
                            </option>

                            <?php
                            while ($kelas = mysqli_fetch_assoc($query_kelas_tambah)) {
                            ?>

                                <option value="<?= (int) $kelas['id']; ?>">
                                    <?= htmlspecialchars($kelas['nama']); ?>
                                </option>

                            <?php
                            }
                            ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Tahun Ajaran
                        </label>

                        <select name="tahun_ajaran_id"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Tahun Ajaran
                            </option>

                            <?php
                            while ($tahun = mysqli_fetch_assoc($query_tahun_tambah)) {
                            ?>

                                <option value="<?= (int) $tahun['id']; ?>">
                                    <?= htmlspecialchars($tahun['nama']); ?>
                                </option>

                            <?php
                            }
                            ?>

                        </select>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Tanggal Selesai
                            </label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control">

                        </div>

                    </div>

                    <div class="mb-2">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status_aktif"
                                class="form-select"
                                required>

                            <option value="1">
                                Aktif
                            </option>

                            <option value="0">
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            name="simpan_tambah"
                            class="btn btn-primary">
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


<!-- MODAL EDIT PENEMPATAN SISWA -->
<div class="modal fade" id="modalEditPenempatan" tabindex="-1"
     aria-labelledby="judulModalEdit" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <form method="POST" action="">

                <div class="modal-header">

                    <h5 class="modal-title fw-bold" id="judulModalEdit">
                        Edit Penempatan Siswa
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>

                <div class="modal-body">

                    <input type="hidden"
                           name="id"
                           id="edit_id">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <select name="siswa_id"
                                id="edit_siswa"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Siswa
                            </option>

                            <?php
                            while ($siswa = mysqli_fetch_assoc($query_siswa)) {
                            ?>

                                <option value="<?= (int) $siswa['id']; ?>">
                                    <?= htmlspecialchars($siswa['nis'] . ' - ' . $siswa['nama']); ?>
                                </option>

                            <?php
                            }
                            ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Kelas
                        </label>

                        <select name="kelas_id"
                                id="edit_kelas"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Kelas
                            </option>

                            <?php
                            while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                            ?>

                                <option value="<?= (int) $kelas['id']; ?>">
                                    <?= htmlspecialchars($kelas['nama']); ?>
                                </option>

                            <?php
                            }
                            ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Tahun Ajaran
                        </label>

                        <select name="tahun_ajaran_id"
                                id="edit_tahun"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Tahun Ajaran
                            </option>

                            <?php
                            while ($tahun = mysqli_fetch_assoc($query_tahun)) {
                            ?>

                                <option value="<?= (int) $tahun['id']; ?>">
                                    <?= htmlspecialchars($tahun['nama']); ?>
                                </option>

                            <?php
                            }
                            ?>

                        </select>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   id="edit_mulai"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label fw-semibold">
                                Tanggal Selesai
                            </label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   id="edit_selesai"
                                   class="form-control">

                        </div>

                    </div>

                    <div class="mb-2">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status_aktif"
                                id="edit_status"
                                class="form-select"
                                required>

                            <option value="1">
                                Aktif
                            </option>

                            <option value="0">
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            name="simpan_edit"
                            class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


<!-- NOTIFIKASI MELAYANG KANAN ATAS -->
<?php if (!empty($pesan_notifikasi)) { ?>

<div class="toast-container position-fixed top-0 end-0 p-3"
     style="z-index: 9999;">

    <div id="notifikasiToast"
         class="toast align-items-center text-bg-<?= htmlspecialchars($jenis_notifikasi); ?> border-0"
         role="alert"
         aria-live="assertive"
         aria-atomic="true">

        <div class="d-flex">

            <div class="toast-body">
                <?= htmlspecialchars($pesan_notifikasi); ?>
            </div>

            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close">
            </button>

        </div>

    </div>

</div>

<?php } ?>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>


<script>
$(document).ready(function () {

    // DATATABLES
    $('#tabelPenempatanSiswa').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,

        columnDefs: [
            {
                targets: 8,
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
            zeroRecords: "Data penempatan siswa tidak ditemukan",
            emptyTable: "Belum ada data penempatan siswa",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });


    // MODAL EDIT MELAYANG
    $(document).on('click', '.tombol-edit', function () {

        $('#edit_id').val($(this).data('id'));
        $('#edit_siswa').val($(this).data('siswa'));
        $('#edit_kelas').val($(this).data('kelas'));
        $('#edit_tahun').val($(this).data('tahun'));
        $('#edit_mulai').val($(this).data('mulai'));
        $('#edit_selesai').val($(this).data('selesai'));
        $('#edit_status').val($(this).data('status'));

        var modal = new bootstrap.Modal(
            document.getElementById('modalEditPenempatan')
        );

        modal.show();

    });


    // TOMBOL TEMPATKAN
    $(document).on('click', '.tombol-tempatkan', function () {

        var siswaId = $(this).attr('data-siswa');

        $('#modalTambahPenempatan select[name="siswa_id"]')
            .val(siswaId);

        var modal = new bootstrap.Modal(
            document.getElementById('modalTambahPenempatan')
        );

        modal.show();

    });


    // NOTIFIKASI OTOMATIS HILANG SETELAH 3 DETIK
    var toastElement = document.getElementById('notifikasiToast');

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