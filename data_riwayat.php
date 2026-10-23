<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (
    !isset($_SESSION['role']) ||
    !in_array($_SESSION['role'], ['admin', 'guru'])
) {
    echo "Anda tidak memiliki akses";
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'] ?? 'Pengguna';

$query = "
    SELECT
        id,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        nama_guru,
        tanggal,
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

    <title>Riwayat - Sistem Informasi Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
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

                <a href="data_pelanggaran_siswa.php"
                   class="nav-link text-white rounded py-2">
                    Catatan Pelanggaran
                </a>

                <a href="data_tindakan.php"
                   class="nav-link text-white rounded py-2">
                    Tindakan
                </a>

                <a href="data_riwayat.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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
                            <?= htmlspecialchars($nama); ?>
                        </div>

                        <small class="text-secondary">
                            <?= htmlspecialchars(ucfirst($role)); ?>
                        </small>
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
                                                <span class="badge bg-<?= $warnaStatus; ?>">
                                                    <?= htmlspecialchars($status); ?>
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