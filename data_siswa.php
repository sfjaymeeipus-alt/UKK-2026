
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

// PROSES EDIT DATA SISWA
if (isset($_POST['simpan_edit'])) {

    $id = (int) $_POST['id'];
    $nis = trim($_POST['nis']);
    $nisn = trim($_POST['nisn']);
    $nama_siswa = trim($_POST['nama']);
    $jenis_kelamin = trim($_POST['jenis_kelamin']);
    $tanggal_lahir = trim($_POST['tanggal_lahir']);
    $alamat = trim($_POST['alamat']);
    $status_aktif = (int) $_POST['status_aktif'];

    if (
        $id <= 0 || $nis == "" || $nisn == "" ||
        $nama_siswa == "" || $jenis_kelamin == "" ||
        $tanggal_lahir == "" || $alamat == ""
    ) {

        $_SESSION['pesan_hapus'] = "Semua data siswa wajib diisi.";
        $_SESSION['jenis_pesan'] = "danger";

    } else {

        // CEK NIS YANG SAMA
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_siswa WHERE nis = ? AND id != ?"
        );

        mysqli_stmt_bind_param($cek, "si", $nis, $id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $_SESSION['pesan_hapus'] = "NIS sudah digunakan.";
            $_SESSION['jenis_pesan'] = "danger";

        } else {

            // CEK NISN YANG SAMA
            $cek_nisn = mysqli_prepare(
                $koneksi,
                "SELECT id FROM t_siswa WHERE nisn = ? AND id != ?"
            );

            mysqli_stmt_bind_param($cek_nisn, "si", $nisn, $id);
            mysqli_stmt_execute($cek_nisn);
            mysqli_stmt_store_result($cek_nisn);

            if (mysqli_stmt_num_rows($cek_nisn) > 0) {

                $_SESSION['pesan_hapus'] = "NISN sudah digunakan.";
                $_SESSION['jenis_pesan'] = "danger";

            } else {

                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE t_siswa
                     SET nis = ?, nisn = ?, nama = ?,
                         jenis_kelamin = ?, tanggal_lahir = ?,
                         alamat = ?, status_aktif = ?
                     WHERE id = ?"
                );

                mysqli_stmt_bind_param(
                    $update,
                    "ssssssii",
                    $nis,
                    $nisn,
                    $nama_siswa,
                    $jenis_kelamin,
                    $tanggal_lahir,
                    $alamat,
                    $status_aktif,
                    $id
                );

                if (mysqli_stmt_execute($update)) {

                    $_SESSION['pesan_hapus'] = "Data siswa berhasil diperbarui.";
                    $_SESSION['jenis_pesan'] = "success";

                } else {

                    $_SESSION['pesan_hapus'] = "Gagal mengubah data siswa.";
                    $_SESSION['jenis_pesan'] = "danger";

                }

                mysqli_stmt_close($update);
            }

            mysqli_stmt_close($cek_nisn);
        }

        mysqli_stmt_close($cek);
    }

    header("Location: data_siswa.php");
    exit;
}

// MENGAMBIL DATA SISWA
$query = mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Siswa - Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
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

                <a href="dashboard.php" class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <a href="data_siswa.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="data_guru.php" class="nav-link text-white rounded py-2">
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
                    <h4 class="fw-bold text-dark mb-1">Data Siswa</h4>
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

                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4
                                d-flex justify-content-between align-items-center
                                flex-wrap gap-3">

                        <div>
                            <h5 class="fw-bold mb-1">Daftar Siswa</h5>
                            <small class="text-secondary">
                                Informasi identitas, alamat, dan status siswa.
                            </small>
                        </div>

                        <a href="tambah_siswa.php" class="btn btn-primary">
                            + Tambah Siswa
                        </a>

                    </div>

                    <!-- TABEL SISWA -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelSiswa"
                                   class="table table-bordered table-hover
                                          align-middle mb-0">

                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Alamat</th>
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

                                        <td><?= htmlspecialchars($data['nis'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['nisn'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['nama'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['jenis_kelamin'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['tanggal_lahir'] ?? ''); ?></td>

                                        <td><?= htmlspecialchars($data['alamat'] ?? ''); ?></td>

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
                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm tombolEdit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditSiswa"

                                                data-id="<?= htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                data-nis="<?= htmlspecialchars($data['nis'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-nisn="<?= htmlspecialchars($data['nisn'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-nama="<?= htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-jk="<?= htmlspecialchars($data['jenis_kelamin'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-tanggal="<?= htmlspecialchars($data['tanggal_lahir'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-alamat="<?= htmlspecialchars($data['alamat'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                data-status="<?= htmlspecialchars($data['status_aktif'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">

                                                Edit

                                            </button>

                                            <a href="hapus_siswa.php?id=<?= urlencode($data['id']); ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus data siswa ini?')">
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


<!-- MODAL EDIT SISWA -->
<div class="modal fade"
     id="modalEditSiswa"
     tabindex="-1"
     aria-labelledby="judulModalEdit"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow rounded-3">

            <div class="modal-header bg-white">

                <div>
                    <h5 class="modal-title fw-bold" id="judulModalEdit">
                        Edit Data Siswa
                    </h5>

                    <small class="text-secondary">
                        Perbarui informasi siswa yang terdaftar.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <!-- FORM EDIT -->
            <form method="POST" action="data_siswa.php">

                <div class="modal-body">

                    <input type="hidden" name="id" id="edit_id">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label for="edit_nis" class="form-label fw-semibold">
                                NIS
                            </label>

                            <input type="text"
                                   name="nis"
                                   id="edit_nis"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="edit_nisn" class="form-label fw-semibold">
                                NISN
                            </label>

                            <input type="text"
                                   name="nisn"
                                   id="edit_nisn"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="edit_nama" class="form-label fw-semibold">
                                Nama Siswa
                            </label>

                            <input type="text"
                                   name="nama"
                                   id="edit_nama"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="edit_jk" class="form-label fw-semibold">
                                Jenis Kelamin
                            </label>

                            <select name="jenis_kelamin"
                                    id="edit_jk"
                                    class="form-select"
                                    required>

                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="edit_tanggal" class="form-label fw-semibold">
                                Tanggal Lahir
                            </label>

                            <input type="date"
                                   name="tanggal_lahir"
                                   id="edit_tanggal"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="edit_status" class="form-label fw-semibold">
                                Status Siswa
                            </label>

                            <select name="status_aktif"
                                    id="edit_status"
                                    class="form-select"
                                    required>

                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>

                            </select>

                        </div>

                        <div class="col-12 mb-3">

                            <label for="edit_alamat" class="form-label fw-semibold">
                                Alamat
                            </label>

                            <textarea name="alamat"
                                      id="edit_alamat"
                                      class="form-control"
                                      rows="3"
                                      required></textarea>

                        </div>

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

        $('#tabelSiswa').DataTable({

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

                zeroRecords: "Data siswa tidak ditemukan",

                emptyTable: "Belum ada data siswa",

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

            document.getElementById('edit_nis').value =
                this.getAttribute('data-nis');

            document.getElementById('edit_nisn').value =
                this.getAttribute('data-nisn');

            document.getElementById('edit_nama').value =
                this.getAttribute('data-nama');

            document.getElementById('edit_jk').value =
                this.getAttribute('data-jk');

            document.getElementById('edit_tanggal').value =
                this.getAttribute('data-tanggal');

            document.getElementById('edit_alamat').value =
                this.getAttribute('data-alamat');

            document.getElementById('edit_status').value =
                this.getAttribute('data-status');

        });

    });

</script>


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
