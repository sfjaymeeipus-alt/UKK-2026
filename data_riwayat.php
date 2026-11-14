
<?php

include "middleware/auth.php";
include "config/koneksi.php";

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if (
    $role != 'admin' &&
    $role != 'guru' &&
    $role != 'wali kelas'
) {
    die("Anda tidak memiliki akses.");
}

// ======================================
// MENU ADMIN
// ======================================

$menuAdmin = [
    ["Dashboard", "dashboard.php"],
    ["Data Siswa", "data_siswa.php"],
    ["Data Guru", "data_guru.php"],
    ["Kelas", "data_kelas.php"],
    ["Tahun Ajaran", "data_tahun_ajaran.php"],
    ["Penempatan Siswa", "data_penempatan_siswa.php"],
    ["Wali Kelas", "data_wali_kelas.php"],
    ["Kategori Pelanggaran", "data_kategori_pelanggaran.php"],
    ["Jenis Pelanggaran", "data_jenis_pelanggaran.php"],
    ["Laporan", "data_laporan.php"],
    ["About Me", "about_me.php"]
];

// ======================================
// MENU GURU BIASA
// ======================================

$menuGuru = [
    ["Dashboard", "dashboard.php"],
    ["Catatan Pelanggaran", "data_catatan_pelanggaran.php"],
   
    ["Riwayat", "data_riwayat.php"],
    ["Rekap Poin", "data_rekap_poin.php"],
    ["About Me", "about_me.php"]
];

// ======================================
// MENU WALI KELAS
// ======================================

$menuWali = [
    ["Dashboard", "dashboard.php"],
    ["Catatan Pelanggaran", "data_catatan_pelanggaran.php"],
    ["Data Siswa", "walikelas/data_siswa.php"],
    ["Riwayat", "data_riwayat.php"],
    ["Rekap Poin", "data_rekap_poin.php"],
    ["Laporan", "walikelas/data_laporan.php"],
    ["About Me", "about_me.php"]
];

// ======================================
// CEK STATUS WALI KELAS
// ======================================

$isWaliKelas = ($role == 'wali kelas');

if ($role == 'guru') {

    $stmtGuru = mysqli_prepare($koneksi, "
        SELECT id
        FROM t_guru
        WHERE user_id = ?
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmtGuru, "i", $user_id);
    mysqli_stmt_execute($stmtGuru);

    $hasilGuru = mysqli_stmt_get_result($stmtGuru);
    $dataGuru = mysqli_fetch_assoc($hasilGuru);

    if ($dataGuru) {

        $guru_id = (int) $dataGuru['id'];

        $stmtWali = mysqli_prepare($koneksi, "
            SELECT COUNT(*) AS total
            FROM t_wali_kelas wk
            JOIN t_tahun_ajaran ta
                ON wk.tahun_ajaran_id = ta.id
            WHERE wk.guru_id = ?
                AND wk.status_aktif = 1
                AND ta.status_aktif = 1
        ");

        mysqli_stmt_bind_param($stmtWali, "i", $guru_id);
        mysqli_stmt_execute($stmtWali);

        $hasilWali = mysqli_stmt_get_result($stmtWali);
        $dataWali = mysqli_fetch_assoc($hasilWali);

        if ((int) ($dataWali['total'] ?? 0) > 0) {
            $isWaliKelas = true;
        }
    }
}

// ======================================
// TENTUKAN MENU
// ======================================

if ($role == 'admin') {
    $menu = $menuAdmin;
} elseif ($isWaliKelas) {
    $menu = $menuWali;
} else {
    $menu = $menuGuru;
}

$halamanAktif = basename($_SERVER['PHP_SELF']);

// ======================================
// QUERY RIWAYAT PELANGGARAN
// HANYA STATUS SELESAI
// ======================================

$query = "

    SELECT
        ps.id,
        ps.nama_siswa,
        ps.nama_kelas,

        COALESCE(
            NULLIF(NULLIF(TRIM(ps.nama_pelanggaran), ''), '0'),
            p.nama,
            '-'
        ) AS nama_pelanggaran,

        COALESCE(
            NULLIF(NULLIF(TRIM(ps.nama_guru), ''), '0'),
            g.nama,
            '-'
        ) AS nama_guru,

        ps.tanggal,
        ps.status

    FROM t_pelanggaran_siswa ps

    LEFT JOIN t_pelanggaran p
        ON ps.pelanggaran_id = p.id

    LEFT JOIN t_guru g
        ON ps.guru_id = g.id

    WHERE ps.status = 'Selesai'
";

if ($isWaliKelas) {
    $query .= " AND ps.guru_id = (
        SELECT id
        FROM t_guru
        WHERE user_id = $user_id
        LIMIT 1
    )";
}

$query .= " ORDER BY ps.id ASC";

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

    <title>Riwayat - Sistem Informasi Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3 position-sticky top-0 vh-100 overflow-auto">

            <h5 class="fw-bold mb-1">Sistem Informasi</h5>
            <h6 class="fw-bold">Pelanggaran Siswa</h6>

            <p class="text-secondary small mb-4">
                SMK Muhammadiyah Kota Tasikmalaya
            </p>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <?php foreach ($menu as $item) {

                    $link = $item[1];
                    $namaMenu = $item[0];

                    $halamanLink = basename($link);
                    $aktif = ($halamanAktif == $halamanLink);

                ?>

                    <a href="<?= htmlspecialchars($link); ?>"
                       class="nav-link <?= $aktif ? 'active bg-primary text-white' : 'text-white'; ?> rounded py-2">

                        <?= htmlspecialchars($namaMenu); ?>

                    </a>

                <?php } ?>

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
            <header class="bg-white border-bottom p-3 p-md-4
                           d-flex justify-content-between align-items-center">

                <div>

                    <h4 class="fw-bold text-dark mb-1">
                        Riwayat Pelanggaran
                    </h4>

                    <small class="text-secondary">
                        Sistem Informasi Pelanggaran Siswa
                    </small>

                </div>

                <div class="d-flex align-items-center gap-2">

                    <div class="text-end">

                        <div class="fw-bold">
                            <?= htmlspecialchars($nama) ?>
                        </div>

                        <small class="text-secondary">

                            <?php
                            if ($role == 'admin') {
                                echo 'Admin';
                            } elseif ($isWaliKelas) {
                                echo 'Wali Kelas';
                            } else {
                                echo 'Guru';
                            }
                            ?>

                        </small>

                    </div>

                    <div class="bg-primary-subtle text-primary rounded-circle
                                d-flex justify-content-center align-items-center
                                fw-bold flex-shrink-0"
                         style="width: 42px; height: 42px;">

                        <?= htmlspecialchars(strtoupper(substr($nama, 0, 1))) ?>

                    </div>

                </div>

            </header>

            <!-- ISI HALAMAN -->
            <section class="p-3 p-md-4">

                <!-- KARTU TABEL -->
                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Daftar Riwayat Pelanggaran
                            </h5>

                            <small class="text-secondary">
                                Informasi riwayat pelanggaran siswa.
                            </small>

                        </div>

                    </div>

                    <!-- TABEL RIWAYAT -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelRiwayat"
                                   class="table table-bordered table-hover
                                          align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Guru</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php
                                    $no = 1;

                                    if (mysqli_num_rows($result) > 0) {

                                        while ($data = mysqli_fetch_assoc($result)) {

                                            $status = $data['status'] ?? '';
                                            $warnaStatus = 'secondary';

                                            if ($status == 'Selesai') {
                                                $warnaStatus = 'success';
                                            } elseif ($status == 'Diproses') {
                                                $warnaStatus = 'warning text-dark';
                                            } elseif ($status == 'Ditindaklanjuti') {
                                                $warnaStatus = 'primary';
                                            }
                                    ?>

                                        <tr>

                                            <td>
                                                <?= $no++ ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_siswa'] ?? '') ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_kelas'] ?? '') ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-') ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_guru'] ?? '-') ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['tanggal'] ?? '') ?>
                                            </td>

                                            <td>

                                                <span class="badge bg-<?= $warnaStatus ?>">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>

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

                    <!-- BAGIAN BAWAH -->
                    <div class="card-footer bg-white p-3">

                        <a href="dashboard.php"
                           class="btn btn-outline-secondary btn-sm">

                            Kembali ke Dashboard

                        </a>

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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

$(document).ready(function () {

    $('#tabelRiwayat').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        language: {

            search: "Cari data:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data riwayat tidak ditemukan",

            emptyTable: "Belum ada riwayat pelanggaran",

            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Berikutnya",

                previous: "Sebelumnya"

            }

        }

    });

});

</script>

</body>
</html>
