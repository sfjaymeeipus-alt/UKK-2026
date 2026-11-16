
<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];
$pesan = "";

/* DATA SISWA */
$query_siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC"
);

if (!$query_siswa) {
    die("Query siswa gagal: " . mysqli_error($koneksi));
}

/* DATA KELAS */
$query_kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

if (!$query_kelas) {
    die("Query kelas gagal: " . mysqli_error($koneksi));
}

/* TAHUN AJARAN AKTIF SAJA */
$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

if (!$query_tahun) {
    die("Query tahun ajaran gagal: " . mysqli_error($koneksi));
}

/* SIMPAN PENEMPATAN SISWA */
if (isset($_POST['simpan'])) {

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

        $pesan = "Periksa kembali data yang diisi.";

    } else {

        /* PASTIKAN TAHUN AJARAN MASIH AKTIF */
        $cek_tahun = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_tahun_ajaran
             WHERE id = ? AND status_aktif = 1"
        );

        mysqli_stmt_bind_param(
            $cek_tahun,
            "i",
            $tahun_ajaran_id
        );

        mysqli_stmt_execute($cek_tahun);

        $hasil_tahun = mysqli_stmt_get_result($cek_tahun);

        if (mysqli_num_rows($hasil_tahun) == 0) {

            $pesan = "Tahun ajaran yang dipilih tidak aktif.";

        } else {

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO t_kelas_siswa
                (
                    siswa_id,
                    kelas_id,
                    tahun_ajaran_id,
                    tanggal_mulai,
                    tanggal_selesai,
                    status_aktif
                )
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

                    header("Location: data_penempatan_siswa.php");
                    exit;

                } else {

                    $pesan = "Gagal menyimpan data: " . mysqli_stmt_error($stmt);

                }

                mysqli_stmt_close($stmt);

            } else {

                $pesan = "Query gagal: " . mysqli_error($koneksi);

            }

        }

        mysqli_stmt_close($cek_tahun);

    }

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Penempatan Siswa</title>

    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- SELECT2 -->
    <link
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    <style>

        .select2-container {
            width: 100% !important;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }

    </style>

</head>

<body class="bg-light">

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

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

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h2 class="mb-3">
                        Tambah Penempatan Siswa
                    </h2>

                    <p class="text-muted">
                        Isi formulir berikut untuk menempatkan siswa
                        ke kelas dan tahun ajaran yang sesuai.
                    </p>

                    <?php if ($pesan != "") { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>

                    <?php } ?>

                    <form method="POST" action="">

                        <!-- SISWA -->
                        <div class="mb-3">

                            <label for="siswa_id" class="form-label">
                                Pilih Siswa
                            </label>

                            <select
                                name="siswa_id"
                                id="siswa_id"
                                class="form-select"
                                required>

                                <option value=""></option>

                                <?php while ($siswa = mysqli_fetch_assoc($query_siswa)) { ?>

                                    <option value="<?= (int) $siswa['id']; ?>">
                                        <?= htmlspecialchars($siswa['nis'] . ' - ' . $siswa['nama']); ?>
                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- KELAS -->
                        <div class="mb-3">

                            <label for="kelas_id" class="form-label">
                                Pilih Kelas
                            </label>

                            <select
                                name="kelas_id"
                                id="kelas_id"
                                class="form-select"
                                required>

                                <option value=""></option>

                                <?php while ($kelas = mysqli_fetch_assoc($query_kelas)) { ?>

                                    <option value="<?= (int) $kelas['id']; ?>">
                                        <?= htmlspecialchars($kelas['nama']); ?>
                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- TAHUN AJARAN -->
                        <div class="mb-3">

                            <label for="tahun_ajaran_id" class="form-label">
                                Tahun Ajaran Aktif
                            </label>

                            <select
                                name="tahun_ajaran_id"
                                id="tahun_ajaran_id"
                                class="form-select"
                                required>

                                <option value="">
                                    -- Pilih Tahun Ajaran Aktif --
                                </option>

                                <?php while ($tahun = mysqli_fetch_assoc($query_tahun)) { ?>

                                    <option value="<?= (int) $tahun['id']; ?>">
                                        <?= htmlspecialchars($tahun['nama']); ?>
                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- TANGGAL MULAI -->
                        <div class="mb-3">

                            <label for="tanggal_mulai" class="form-label">
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                id="tanggal_mulai"
                                class="form-control"
                                required>

                        </div>

                        <!-- TANGGAL SELESAI -->
                        <div class="mb-3">

                            <label for="tanggal_selesai" class="form-label">
                                Tanggal Selesai
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                id="tanggal_selesai"
                                class="form-control">

                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">

                            <label for="status_aktif" class="form-label">
                                Status
                            </label>

                            <select
                                name="status_aktif"
                                id="status_aktif"
                                class="form-select"
                                required>

                                <option value="1">Aktif</option>

                                <option value="0">Tidak Aktif</option>

                            </select>

                        </div>

                        <button
                            type="submit"
                            name="simpan"
                            class="btn btn-primary">
                            Simpan
                        </button>

                        <a
                            href="data_penempatan_siswa.php"
                            class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>

            </div>

        </main>

    </div>

</div>

<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

$(document).ready(function () {

    $('#siswa_id').select2({

        theme: 'bootstrap-5',

        width: '100%',

        placeholder: 'Ketik NIS atau nama siswa...',

        allowClear: true,

        minimumResultsForSearch: 0

    });

    $('#kelas_id').select2({

        theme: 'bootstrap-5',

        width: '100%',

        placeholder: 'Ketik nama kelas...',

        allowClear: true,

        minimumResultsForSearch: 0

    });

});

</script>

</body>
</html>
