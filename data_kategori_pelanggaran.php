
<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

// PROSES TAMBAH KATEGORI
if (isset($_POST['simpan_tambah'])) {

    $nama_kategori = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $status_aktif = (int) $_POST['status_aktif'];

    if ($nama_kategori == "") {

        $_SESSION['pesan_hapus'] = "Nama kategori wajib diisi.";
        $_SESSION['jenis_pesan'] = "danger";

    } else {

        $insert = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_pelanggaran_kategori
            (nama, deskripsi, status_aktif)
            VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $insert,
            "ssi",
            $nama_kategori,
            $deskripsi,
            $status_aktif
        );

        if (mysqli_stmt_execute($insert)) {

            $_SESSION['pesan_hapus'] = "Data kategori pelanggaran berhasil ditambahkan.";
            $_SESSION['jenis_pesan'] = "success";

        } else {

            $_SESSION['pesan_hapus'] = "Gagal menambahkan data kategori pelanggaran.";
            $_SESSION['jenis_pesan'] = "danger";

        }

        mysqli_stmt_close($insert);
    }

    header("Location: data_kategori_pelanggaran.php");
    exit;
}


// PROSES EDIT KATEGORI PELANGGARAN
if (isset($_POST['simpan_edit'])) {

    $id = (int) $_POST['id'];
    $nama_kategori = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $status_aktif = (int) $_POST['status_aktif'];

    if ($id <= 0 || $nama_kategori == "") {

        $_SESSION['pesan_hapus'] = "Nama kategori wajib diisi.";
        $_SESSION['jenis_pesan'] = "danger";

    } else {

        $update = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran_kategori
             SET nama = ?, deskripsi = ?, status_aktif = ?, updated_at = NOW()
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "ssii",
            $nama_kategori,
            $deskripsi,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($update)) {

            $_SESSION['pesan_hapus'] = "Data kategori pelanggaran berhasil diperbarui.";
            $_SESSION['jenis_pesan'] = "success";

        } else {

            $_SESSION['pesan_hapus'] = "Gagal mengubah data kategori pelanggaran.";
            $_SESSION['jenis_pesan'] = "danger";

        }

        mysqli_stmt_close($update);
    }

    header("Location: data_kategori_pelanggaran.php");
    exit;
}


// MENGAMBIL DATA KATEGORI
$query = "
    SELECT
        id,
        nama,
        deskripsi,
        status_aktif,
        created_at,
        updated_at
    FROM t_pelanggaran_kategori
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

    <title>Data Kategori Pelanggaran</title>

    <!-- Bootstrap 5.3.3 -->
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
        <aside class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3"
               style="position: sticky; top: 0; height: 100vh; overflow-y: auto; align-self: flex-start;">

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
                   class="nav-link text-white rounded py-2">
                    Penempatan Siswa
                </a>

                <a href="data_wali_kelas.php"
                   class="nav-link text-white rounded py-2">
                    Wali Kelas
                </a>

                <a href="data_kategori_pelanggaran.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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
        <main class="col-md-9 col-lg-10 p-0">

            <!-- HEADER -->
            <header class="bg-white border-bottom p-3 p-md-4
                          d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        Data Kategori Pelanggaran
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
                                Tabel Kategori Pelanggaran
                            </h5>

                            <small class="text-secondary">
                                Data kategori yang tersimpan di sistem.
                            </small>
                        </div>

                        <!-- TOMBOL TAMBAH MODAL -->
                        <button type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTambahKategori">

                            + Tambah Kategori

                        </button>

                    </div>

                    <!-- TABEL DATA -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelKategoriPelanggaran"
                                   class="table table-bordered table-hover
                                          align-middle w-100">

                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
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

                                        <td class="fw-semibold">
                                            <?= htmlspecialchars($data['nama'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['deskripsi'] ?? ''); ?>
                                        </td>

                                        <td class="text-center">

                                            <?php
                                            if ($data['status_aktif'] == 1) {
                                                echo '<span class="badge bg-success">Aktif</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">Tidak Aktif</span>';
                                            }
                                            ?>

                                        </td>

                                        <td class="text-center">

                                            <div class="d-flex justify-content-center gap-1">

                                                <!-- TOMBOL EDIT MODAL -->
                                                <button
                                                    type="button"
                                                    class="btn btn-warning btn-sm tombolEdit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditKategori"

                                                    data-id="<?= htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8'); ?>"

                                                    data-nama="<?= htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"

                                                    data-deskripsi="<?= htmlspecialchars($data['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"

                                                    data-status="<?= htmlspecialchars($data['status_aktif'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">

                                                    Edit

                                                </button>

                                                <a
                                                    href="hapus_kategori_pelanggaran.php?id=<?= urlencode($data['id']); ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus kategori ini?');">

                                                    Hapus

                                                </a>

                                            </div>

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


<!-- ===================================================== -->
<!-- MODAL TAMBAH KATEGORI PELANGGARAN -->
<!-- ===================================================== -->

<div class="modal fade"
     id="modalTambahKategori"
     tabindex="-1"
     aria-labelledby="judulModalTambah"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow rounded-3">

            <div class="modal-header bg-white">

                <div>

                    <h5 class="modal-title fw-bold" id="judulModalTambah">
                        Tambah Kategori Pelanggaran
                    </h5>

                    <small class="text-secondary">
                        Silakan isi informasi kategori pelanggaran.
                    </small>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <form method="POST" action="data_kategori_pelanggaran.php">

                <div class="modal-body">

                    <div class="mb-3">

                        <label for="tambah_nama" class="form-label fw-semibold">
                            Nama Kategori
                        </label>

                        <input type="text"
                               name="nama"
                               id="tambah_nama"
                               class="form-control"
                               placeholder="Masukkan nama kategori"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_deskripsi" class="form-label fw-semibold">
                            Deskripsi
                        </label>

                        <textarea
                            name="deskripsi"
                            id="tambah_deskripsi"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan deskripsi kategori"></textarea>

                    </div>

                    <div class="mb-3">

                        <label for="tambah_status" class="form-label fw-semibold">
                            Status Kategori
                        </label>

                        <select name="status_aktif"
                                id="tambah_status"
                                class="form-select"
                                required>

                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>

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


<!-- ===================================================== -->
<!-- MODAL EDIT KATEGORI PELANGGARAN -->
<!-- ===================================================== -->

<div class="modal fade"
     id="modalEditKategori"
     tabindex="-1"
     aria-labelledby="judulModalEdit"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow rounded-3">

            <!-- HEADER MODAL -->
            <div class="modal-header bg-white">

                <div>
                    <h5 class="modal-title fw-bold" id="judulModalEdit">
                        Edit Kategori Pelanggaran
                    </h5>

                    <small class="text-secondary">
                        Perbarui informasi kategori pelanggaran.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <!-- FORM EDIT -->
            <form method="POST" action="data_kategori_pelanggaran.php">

                <div class="modal-body">

                    <input type="hidden"
                           name="id"
                           id="edit_id">

                    <div class="mb-3">

                        <label for="edit_nama" class="form-label fw-semibold">
                            Nama Kategori
                        </label>

                        <input type="text"
                               name="nama"
                               id="edit_nama"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label for="edit_deskripsi" class="form-label fw-semibold">
                            Deskripsi
                        </label>

                        <textarea
                            name="deskripsi"
                            id="edit_deskripsi"
                            class="form-control"
                            rows="4"></textarea>

                    </div>

                    <div class="mb-3">

                        <label for="edit_status" class="form-label fw-semibold">
                            Status Kategori
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

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- INISIALISASI DATATABLES -->
<script>

    $(document).ready(function () {

        $('#tabelKategoriPelanggaran').DataTable({

            pageLength: 10,

            lengthMenu: [5, 10, 25, 50, 100],

            ordering: true,

            searching: true,

            paging: true,

            info: true,

            columnDefs: [
                {
                    targets: 4,
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

                zeroRecords: "Data kategori pelanggaran tidak ditemukan",

                emptyTable: "Belum ada data kategori pelanggaran",

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

            document.getElementById('edit_nama').value =
                this.getAttribute('data-nama');

            document.getElementById('edit_deskripsi').value =
                this.getAttribute('data-deskripsi');

            document.getElementById('edit_status').value =
                this.getAttribute('data-status');

        });

    });

</script>


<!-- ===================================================== -->
<!-- NOTIFIKASI MELAYANG -->
<!-- ===================================================== -->

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
