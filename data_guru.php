<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

// PROSES EDIT DATA GURU
if (isset($_POST['simpan_edit'])) {

    $id = (int) $_POST['id'];
    $nip = trim($_POST['nip']);
    $nama_guru = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $status_aktif = (int) $_POST['status_aktif'];

    // Cek email yang digunakan guru lain
    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_guru WHERE email = ? AND id != ?"
    );

    mysqli_stmt_bind_param($cek, "si", $email, $id);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {

        $_SESSION['pesan_hapus'] = "Email sudah digunakan oleh guru lain.";
        $_SESSION['jenis_pesan'] = "danger";

    } else {

        $update = mysqli_prepare(
            $koneksi,
            "UPDATE t_guru
             SET nip = ?, nama = ?, email = ?, status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "sssii",
            $nip,
            $nama_guru,
            $email,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($update)) {

            $_SESSION['pesan_hapus'] = "Data guru berhasil diperbarui.";
            $_SESSION['jenis_pesan'] = "success";

        } else {

            $_SESSION['pesan_hapus'] = "Gagal mengubah data guru.";
            $_SESSION['jenis_pesan'] = "danger";

        }

        mysqli_stmt_close($update);
    }

    mysqli_stmt_close($cek);

    header("Location: data_guru.php");
    exit;
}

// MENGAMBIL DATA GURU
$query = mysqli_query($koneksi, "SELECT * FROM t_guru ORDER BY id ASC");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Guru - Sistem Informasi Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

                <a href="dashboard.php" class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <a href="data_siswa.php" class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="data_guru.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Data Guru
                </a>

                <a href="data_kelas.php" class="nav-link text-white rounded py-2">
                    Kelas
                </a>

                <a href="data_tahun_ajaran.php" class="nav-link text-white rounded py-2">
                    Tahun Ajaran
                </a>

                <a href="data_penempatan_siswa.php" class="nav-link text-white rounded py-2">
                    Penempatan Siswa
                </a>

                <a href="data_wali_kelas.php" class="nav-link text-white rounded py-2">
                    Wali Kelas
                </a>

                <a href="data_kategori_pelanggaran.php" class="nav-link text-white rounded py-2">
                    Kategori Pelanggaran
                </a>

                <a href="data_jenis_pelanggaran.php" class="nav-link text-white rounded py-2">
                    Jenis Pelanggaran
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
            <header class="bg-white border-bottom p-3 p-md-4
                           d-flex justify-content-between align-items-center">

                <div>

                    <h4 class="fw-bold text-dark mb-1">
                        Data Guru
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

                <!-- KARTU DATA GURU -->
                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4
                                d-flex justify-content-between align-items-center
                                flex-wrap gap-3">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Daftar Guru
                            </h5>

                            <small class="text-secondary">
                                Informasi identitas dan status guru.
                            </small>

                        </div>

                        <!-- TOMBOL TAMBAH GURU -->
                        <button type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTambahGuru">

                            + Tambah Guru

                        </button>

                    </div>

                    <!-- TABEL GURU -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelGuru"
                                   class="table table-bordered table-hover
                                          align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>No</th>
                                        <th>NIP</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>

                                </thead>

                                <tbody>

                                <?php

                                $no = 1;

                                if ($query && mysqli_num_rows($query) > 0) {

                                    while ($data = mysqli_fetch_assoc($query)) {

                                ?>

                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td>
                                            <?= htmlspecialchars($data['nip'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['email'] ?? ''); ?>
                                        </td>

                                        <td>

                                            <?php if (($data['status_aktif'] ?? 0) == 1) { ?>

                                                <span class="badge bg-success">
                                                    Aktif
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge bg-danger">
                                                    Tidak Aktif
                                                </span>

                                            <?php } ?>

                                        </td>

                                        <td class="text-nowrap">

                                            <!-- TOMBOL EDIT MODAL -->
                                            <button type="button"
                                                    class="btn btn-warning btn-sm tombolEdit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditGuru"

                                                    data-id="<?= htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8'); ?>"

                                                    data-nip="<?= htmlspecialchars($data['nip'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"

                                                    data-nama="<?= htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"

                                                    data-email="<?= htmlspecialchars($data['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"

                                                    data-status="<?= htmlspecialchars($data['status_aktif'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">

                                                Edit

                                            </button>

                                            <a href="hapus_guru.php?id=<?= urlencode($data['id']); ?>"
                                               class="btn btn-danger btn-sm">

                                                Hapus

                                            </a>

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


<!-- ====================================================== -->
<!-- MODAL TAMBAH GURU -->
<!-- ====================================================== -->

<div class="modal fade"
     id="modalTambahGuru"
     tabindex="-1"
     aria-labelledby="judulModalTambah"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow rounded-3">

            <!-- HEADER MODAL -->
            <div class="modal-header bg-white">

                <div>

                    <h5 class="modal-title fw-bold" id="judulModalTambah">
                        Tambah Data Guru
                    </h5>

                    <small class="text-secondary">
                        Silakan isi informasi guru yang akan ditambahkan.
                    </small>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <!-- FORM TAMBAH -->
            <form method="POST" action="tambah_guru.php">

                <div class="modal-body">

                    <!-- NIP -->
                    <div class="mb-3">

                        <label for="tambah_nip"
                               class="form-label fw-semibold">
                            NIP
                        </label>

                        <input type="text"
                               name="nip"
                               id="tambah_nip"
                               class="form-control"
                               maxlength="18"
                               required>

                    </div>

                    <!-- NAMA -->
                    <div class="mb-3">

                        <label for="tambah_nama"
                               class="form-label fw-semibold">
                            Nama Guru
                        </label>

                        <input type="text"
                               name="nama"
                               id="tambah_nama"
                               class="form-control"
                               required>

                    </div>

                    <!-- EMAIL -->
                    <div class="mb-3">

                        <label for="tambah_email"
                               class="form-label fw-semibold">
                            Email
                        </label>

                        <input type="email"
                               name="email"
                               id="tambah_email"
                               class="form-control"
                               autocomplete="off"
                               required>

                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-3">

                        <label for="tambah_password"
                               class="form-label fw-semibold">
                            Password
                        </label>

                        <input type="password"
                               name="password"
                               id="tambah_password"
                               class="form-control"
                               autocomplete="new-password"
                               required>

                    </div>

                    <!-- KONFIRMASI PASSWORD -->
                    <div class="mb-3">

                        <label for="tambah_konfirmasi_password"
                               class="form-label fw-semibold">
                            Konfirmasi Password
                        </label>

                        <input type="password"
                               name="konfirmasi_password"
                               id="tambah_konfirmasi_password"
                               class="form-control"
                               autocomplete="new-password"
                               required>

                    </div>

                    <!-- STATUS -->
                    <div class="mb-3">

                        <label for="tambah_status"
                               class="form-label fw-semibold">
                            Status Guru
                        </label>

                        <select name="status_aktif"
                                id="tambah_status"
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

                <!-- FOOTER MODAL -->
                <div class="modal-footer">

                    <button type="submit"
                            name="simpan"
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


<!-- ====================================================== -->
<!-- MODAL EDIT GURU -->
<!-- ====================================================== -->

<div class="modal fade"
     id="modalEditGuru"
     tabindex="-1"
     aria-labelledby="judulModalEdit"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow rounded-3">

            <!-- HEADER MODAL -->
            <div class="modal-header bg-white">

                <div>

                    <h5 class="modal-title fw-bold" id="judulModalEdit">
                        Edit Data Guru
                    </h5>

                    <small class="text-secondary">
                        Perbarui informasi guru yang terdaftar.
                    </small>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <!-- FORM EDIT -->
            <form method="POST" action="data_guru.php">

                <div class="modal-body">

                    <input type="hidden"
                           name="id"
                           id="edit_id">

                    <div class="mb-3">

                        <label for="edit_nip" class="form-label fw-semibold">
                            NIP
                        </label>

                        <input type="text"
                               name="nip"
                               id="edit_nip"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_nama" class="form-label fw-semibold">
                            Nama Guru
                        </label>

                        <input type="text"
                               name="nama"
                               id="edit_nama"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_email" class="form-label fw-semibold">
                            Email
                        </label>

                        <input type="email"
                               name="email"
                               id="edit_email"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_status" class="form-label fw-semibold">
                            Status Guru
                        </label>

                        <select name="status_aktif"
                                id="edit_status"
                                class="form-select"
                                required>

                            <option value="1">Aktif</option>

                            <option value="0">Tidak Aktif</option>

                        </select>

                    </div>

                </div>

                <!-- FOOTER MODAL -->
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


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- DataTables Bootstrap 5 JavaScript -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- INISIALISASI DATATABLES -->
<script>

    $(document).ready(function () {

        $('#tabelGuru').DataTable({

            pageLength: 10,

            lengthMenu: [5, 10, 25, 50, 100],

            ordering: true,

            searching: true,

            paging: true,

            info: true,

            columnDefs: [
                {
                    targets: 5,
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

                zeroRecords: "Data guru tidak ditemukan",

                emptyTable: "Belum ada data guru",

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


<!-- MENGISI DATA KE MODAL EDIT -->
<script>

    document.querySelectorAll('.tombolEdit').forEach(function (tombol) {

        tombol.addEventListener('click', function () {

            document.getElementById('edit_id').value =
                this.getAttribute('data-id');

            document.getElementById('edit_nip').value =
                this.getAttribute('data-nip');

            document.getElementById('edit_nama').value =
                this.getAttribute('data-nama');

            document.getElementById('edit_email').value =
                this.getAttribute('data-email');

            document.getElementById('edit_status').value =
                this.getAttribute('data-status');

        });

    });

</script>


<!-- ====================================================== -->
<!-- NOTIFIKASI MELAYANG -->
<!-- ====================================================== -->

<?php if (isset($_SESSION['pesan_hapus'])) { ?>

    <div class="toast-container position-fixed top-0 end-0 p-3"
         style="z-index: 9999;">

        <div id="notifikasiToast"
             class="toast align-items-center text-bg-<?= htmlspecialchars($_SESSION['jenis_pesan'] ?? 'success'); ?> border-0"
             role="alert"
             aria-live="assertive"
             aria-atomic="true">

            <div class="d-flex">

                <div class="toast-body">

                    <?= htmlspecialchars($_SESSION['pesan_hapus']); ?>

                </div>

                <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast">
                </button>

            </div>

        </div>

    </div>

    <?php

    unset($_SESSION['pesan_hapus']);
    unset($_SESSION['jenis_pesan']);

    ?>

<?php } ?>


<!-- MENAMPILKAN TOAST -->
<script>

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