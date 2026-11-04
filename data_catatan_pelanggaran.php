
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

// PROSES EDIT DATA PELANGGARAN
if (isset($_POST['simpan_edit'])) {

    $id = (int) $_POST['id'];
    $nama_siswa = trim($_POST['nama_siswa']);
    $nama_kelas = trim($_POST['nama_kelas']);
    $nama_pelanggaran = trim($_POST['nama_pelanggaran']);
    $nama_guru = trim($_POST['nama_guru']);
    $tanggal = trim($_POST['tanggal']);
    $keterangan = trim($_POST['keterangan']);
    $poin = (int) $_POST['poin'];
    $tindakan = trim($_POST['tindakan']);
    $status = trim($_POST['status']);

    if (
        $id <= 0 ||
        $nama_siswa == "" ||
        $nama_kelas == "" ||
        $nama_pelanggaran == "" ||
        $nama_guru == "" ||
        $tanggal == ""
    ) {

        $_SESSION['pesan_hapus'] = "Semua data wajib diisi.";
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
    }

    header("Location: data_catatan_pelanggaran.php");
    exit;
}

// MENGAMBIL DATA PELANGGARAN
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
            <header class="bg-white border-bottom p-3 p-md-4
                           d-flex justify-content-between align-items-center">

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

                    <div class="card-header bg-white p-3 p-md-4
                                d-flex justify-content-between align-items-center
                                flex-wrap gap-3">

                        <div>
                            <h5 class="fw-bold mb-1">
                                Daftar Catatan Pelanggaran
                            </h5>

                            <small class="text-secondary">
                                Informasi pelanggaran, poin, dan tindakan siswa.
                            </small>
                        </div>

                        <a href="tambah_pelanggaran_siswa.php"
                           class="btn btn-primary">
                            + Tambah Catatan
                        </a>

                    </div>

                    <!-- TABEL -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelPelanggaran"
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

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($data = mysqli_fetch_assoc($result)) {
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

                                                <!-- TOMBOL EDIT MODAL -->
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


<!-- MODAL EDIT PELANGGARAN -->
<div class="modal fade"
     id="modalEditPelanggaran"
     tabindex="-1"
     aria-labelledby="judulModalEdit"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow rounded-3">

            <!-- HEADER MODAL -->
            <div class="modal-header bg-white">

                <div>
                    <h5 class="modal-title fw-bold" id="judulModalEdit">
                        Edit Catatan Pelanggaran
                    </h5>

                    <small class="text-secondary">
                        Perbarui informasi catatan pelanggaran siswa.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <!-- FORM EDIT -->
            <form method="POST" action="data_catatan_pelanggaran.php">

                <div class="modal-body">

                    <input type="hidden"
                           name="id"
                           id="edit_id">

                    <div class="mb-3">

                        <label for="edit_siswa" class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <input type="text"
                               name="nama_siswa"
                               id="edit_siswa"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_kelas" class="form-label fw-semibold">
                            Kelas
                        </label>

                        <input type="text"
                               name="nama_kelas"
                               id="edit_kelas"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_pelanggaran" class="form-label fw-semibold">
                            Jenis Pelanggaran
                        </label>

                        <input type="text"
                               name="nama_pelanggaran"
                               id="edit_pelanggaran"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_guru" class="form-label fw-semibold">
                            Nama Guru
                        </label>

                        <input type="text"
                               name="nama_guru"
                               id="edit_guru"
                               class="form-control"
                               required>

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

                        <textarea name="tindakan"
                                  id="edit_tindakan"
                                  class="form-control"
                                  rows="3"></textarea>

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

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- INISIALISASI DATATABLES -->
<script>

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

</script>


<!-- MENGISI DATA KE MODAL EDIT -->
<script>

document.querySelectorAll('.tombolEdit').forEach(function (tombol) {

    tombol.addEventListener('click', function () {

        document.getElementById('edit_id').value =
            this.getAttribute('data-id');

        document.getElementById('edit_siswa').value =
            this.getAttribute('data-siswa');

        document.getElementById('edit_kelas').value =
            this.getAttribute('data-kelas');

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
