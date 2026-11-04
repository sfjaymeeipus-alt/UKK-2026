
<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

// NOTIFIKASI
$notifikasi_hapus = $_SESSION['notifikasi_hapus'] ?? null;
unset($_SESSION['notifikasi_hapus']);

$notifikasi_edit = $_SESSION['notifikasi_edit'] ?? null;
unset($_SESSION['notifikasi_edit']);

$error = "";
$editData = null;

// PROSES EDIT DATA
if (isset($_POST['simpan_edit'])) {

    $id = (int) ($_POST['id'] ?? 0);
    $kode = trim($_POST['kode'] ?? '');
    $nama_pelanggaran = trim($_POST['nama'] ?? '');
    $kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $poin = filter_var($_POST['poin'] ?? null, FILTER_VALIDATE_INT);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = (int) ($_POST['status_aktif'] ?? -1);

    $editData = [
        'id' => $id,
        'kode' => $kode,
        'nama' => $nama_pelanggaran,
        'pelanggaran_kategori_id' => $kategori_id,
        'poin' => $poin === false ? 0 : $poin,
        'deskripsi' => $deskripsi,
        'status_aktif' => $status_aktif
    ];

    if (
        $id <= 0 ||
        $kode == '' ||
        $nama_pelanggaran == '' ||
        $kategori_id <= 0 ||
        $poin === false ||
        $poin === null ||
        $poin < 0 ||
        !in_array($status_aktif, [0, 1], true)
    ) {

        $error = "Periksa kembali data yang kamu masukkan.";

    } else {

        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_pelanggaran_kategori WHERE id = ?"
        );

        mysqli_stmt_bind_param($cek, "i", $kategori_id);
        mysqli_stmt_execute($cek);

        $hasil_cek = mysqli_stmt_get_result($cek);
        $kategori_ada = mysqli_fetch_assoc($hasil_cek);

        mysqli_stmt_close($cek);

        if (!$kategori_ada) {

            $error = "Kategori pelanggaran tidak ditemukan.";

        } else {

            $stmt = mysqli_prepare($koneksi, "
                UPDATE t_pelanggaran
                SET kode = ?,
                    nama = ?,
                    pelanggaran_kategori_id = ?,
                    poin = ?,
                    deskripsi = ?,
                    status_aktif = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "ssiisii",
                $kode,
                $nama_pelanggaran,
                $kategori_id,
                $poin,
                $deskripsi,
                $status_aktif,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                $_SESSION['notifikasi_edit'] = [
                    'tipe' => 'success',
                    'pesan' => 'Data jenis pelanggaran berhasil diperbarui.'
                ];

                header("Location: data_jenis_pelanggaran.php");
                exit;

            } else {

                if (mysqli_errno($koneksi) == 1062) {
                    $error = "Kode pelanggaran sudah digunakan oleh data lain.";
                } else {
                    $error = "Gagal memperbarui data jenis pelanggaran.";
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
}

// AMBIL DATA KATEGORI
$kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_pelanggaran_kategori ORDER BY nama ASC"
);

if (!$kategori) {
    die("Gagal mengambil kategori: " . mysqli_error($koneksi));
}

$daftar_kategori = [];

while ($kat = mysqli_fetch_assoc($kategori)) {
    $daftar_kategori[] = $kat;
}

// AMBIL DATA JENIS PELANGGARAN
$query = "
    SELECT
        p.id,
        p.kode,
        p.nama,
        p.pelanggaran_kategori_id,
        p.poin,
        p.deskripsi,
        p.status_aktif,
        pk.nama AS nama_kategori
    FROM t_pelanggaran p
    INNER JOIN t_pelanggaran_kategori pk
        ON p.pelanggaran_kategori_id = pk.id
    ORDER BY p.id DESC
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

    <title>Data Jenis Pelanggaran - Sistem Informasi Pelanggaran Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white p-3 position-sticky top-0 vh-100 overflow-y-auto">

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
                   class="nav-link text-white rounded py-2">
                    Kategori Pelanggaran
                </a>

                <a href="data_jenis_pelanggaran.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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
                        Data Jenis Pelanggaran
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

                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4
                                d-flex justify-content-between align-items-center
                                flex-wrap gap-3">

                        <div>
                            <h5 class="fw-bold mb-1">
                                Daftar Jenis Pelanggaran
                            </h5>

                            <small class="text-secondary">
                                Informasi kode, nama pelanggaran, kategori, poin, dan status.
                            </small>
                        </div>

                        <a href="tambah_jenis_pelanggaran.php"
                           class="btn btn-primary">
                            + Tambah Jenis Pelanggaran
                        </a>

                    </div>

                    <!-- TABEL DATA -->
                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelJenisPelanggaran"
                                   class="table table-bordered table-hover
                                          align-middle w-100">

                                <thead class="table-dark">

                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Kode</th>
                                        <th>Nama Pelanggaran</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Poin</th>
                                        <th>Deskripsi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>

                                </thead>

                                <tbody>

                                <?php
                                $no = 1;

                                while ($data = mysqli_fetch_assoc($result)) {
                                ?>

                                    <tr>

                                        <td class="text-center">
                                            <?= $no++; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['kode'] ?? ''); ?>
                                        </td>

                                        <td class="fw-semibold">
                                            <?= htmlspecialchars($data['nama'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_kategori'] ?? ''); ?>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-info text-dark">
                                                <?= htmlspecialchars((string) $data['poin']); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['deskripsi'] ?? ''); ?>
                                        </td>

                                        <td class="text-center">

                                            <?php if ($data['status_aktif'] == 1) { ?>

                                                <span class="badge bg-success">
                                                    Aktif
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge bg-danger">
                                                    Tidak Aktif
                                                </span>

                                            <?php } ?>

                                        </td>

                                        <td class="text-center text-nowrap">

                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm tombolEdit"

                                                data-id="<?= (int) $data['id']; ?>"
                                                data-kode="<?= htmlspecialchars($data['kode'], ENT_QUOTES); ?>"
                                                data-nama="<?= htmlspecialchars($data['nama'], ENT_QUOTES); ?>"
                                                data-kategori="<?= (int) $data['pelanggaran_kategori_id']; ?>"
                                                data-poin="<?= (int) $data['poin']; ?>"
                                                data-deskripsi="<?= htmlspecialchars($data['deskripsi'] ?? '', ENT_QUOTES); ?>"
                                                data-status="<?= (int) $data['status_aktif']; ?>">

                                                Edit
                                            </button>

                                            <a
                                                href="hapus_jenis_pelanggaran.php?id=<?= urlencode($data['id']); ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?');">

                                                Hapus

                                            </a>

                                        </td>

                                    </tr>

                                <?php } ?>

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


<!-- POP-UP EDIT JENIS PELANGGARAN -->
<div class="modal fade" id="modalEdit" tabindex="-1"
     aria-labelledby="judulModalEdit" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">

        <div class="modal-content border-0 shadow rounded-3">

            <div class="modal-header bg-white">

                <div>
                    <h5 class="modal-title fw-bold" id="judulModalEdit">
                        Edit Jenis Pelanggaran
                    </h5>

                    <small class="text-secondary">
                        Perbarui informasi jenis pelanggaran siswa.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <form method="POST" action="data_jenis_pelanggaran.php">

                <div class="modal-body p-4">

                    <?php if ($error != '') { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>

                    <?php } ?>

                    <input type="hidden"
                           name="id"
                           id="edit_id"
                           value="<?= (int) ($editData['id'] ?? 0); ?>">

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Kode Pelanggaran
                        </label>

                        <input type="text"
                               name="kode"
                               id="edit_kode"
                               class="form-control"
                               maxlength="50"
                               value="<?= htmlspecialchars($editData['kode'] ?? ''); ?>"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Nama Pelanggaran
                        </label>

                        <input type="text"
                               name="nama"
                               id="edit_nama"
                               class="form-control"
                               maxlength="150"
                               value="<?= htmlspecialchars($editData['nama'] ?? ''); ?>"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Kategori Pelanggaran
                        </label>

                        <select name="pelanggaran_kategori_id"
                                id="edit_kategori"
                                class="form-select"
                                required>

                            <option value="">
                                -- Pilih Kategori --
                            </option>

                            <?php foreach ($daftar_kategori as $kat) { ?>

                                <option
                                    value="<?= (int) $kat['id']; ?>"
                                    <?= isset($editData['pelanggaran_kategori_id']) &&
                                        (string) $editData['pelanggaran_kategori_id'] === (string) $kat['id']
                                        ? 'selected' : ''; ?>>

                                    <?= htmlspecialchars($kat['nama']); ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Poin Pelanggaran
                        </label>

                        <input type="number"
                               name="poin"
                               id="edit_poin"
                               class="form-control"
                               min="0"
                               step="1"
                               value="<?= htmlspecialchars((string) ($editData['poin'] ?? 0)); ?>"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Deskripsi
                        </label>

                        <textarea name="deskripsi"
                                  id="edit_deskripsi"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Masukkan deskripsi pelanggaran"><?= htmlspecialchars($editData['deskripsi'] ?? ''); ?></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status_aktif"
                                id="edit_status"
                                class="form-select"
                                required>

                            <option value="1"
                                <?= isset($editData['status_aktif']) && $editData['status_aktif'] === 1 ? 'selected' : ''; ?>>
                                Aktif
                            </option>

                            <option value="0"
                                <?= isset($editData['status_aktif']) && $editData['status_aktif'] === 0 ? 'selected' : ''; ?>>
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer bg-white">

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
<div class="toast-container position-fixed top-0 end-0 p-3"
     style="z-index: 1100;">

    <?php if ($notifikasi_edit) { ?>

        <div id="toastNotifikasi"
             class="toast align-items-center text-bg-success border-0"
             role="alert"
             aria-live="assertive"
             aria-atomic="true">

            <div class="d-flex">

                <div class="toast-body">
                    <?= htmlspecialchars($notifikasi_edit['pesan']); ?>
                </div>

                <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast">
                </button>

            </div>

        </div>

    <?php } ?>

    <?php if ($notifikasi_hapus) { ?>

        <div id="toastHapus"
             class="toast align-items-center text-bg-<?= htmlspecialchars($notifikasi_hapus['tipe']); ?> border-0"
             role="alert"
             aria-live="assertive"
             aria-atomic="true">

            <div class="d-flex">

                <div class="toast-body">
                    <?= htmlspecialchars($notifikasi_hapus['pesan']); ?>
                </div>

                <button type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast">
                </button>

            </div>

        </div>

    <?php } ?>

</div>


<!-- JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// DATATABLES
$(document).ready(function () {

    $('#tabelJenisPelanggaran').DataTable({

        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,

        columnDefs: [
            {
                targets: 7,
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
            zeroRecords: "Data jenis pelanggaran tidak ditemukan",
            emptyTable: "Belum ada data jenis pelanggaran",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }

        }

    });

});


// MODAL EDIT
const modalEditElement = document.getElementById('modalEdit');

const modalEdit = new bootstrap.Modal(modalEditElement);

document.querySelectorAll('.tombolEdit').forEach(function (tombol) {

    tombol.addEventListener('click', function () {

        document.getElementById('edit_id').value =
            this.dataset.id;

        document.getElementById('edit_kode').value =
            this.dataset.kode;

        document.getElementById('edit_nama').value =
            this.dataset.nama;

        document.getElementById('edit_kategori').value =
            this.dataset.kategori;

        document.getElementById('edit_poin').value =
            this.dataset.poin;

        document.getElementById('edit_deskripsi').value =
            this.dataset.deskripsi;

        document.getElementById('edit_status').value =
            this.dataset.status;

        modalEdit.show();

    });

});


// BUKA KEMBALI MODAL JIKA VALIDASI GAGAL
<?php if ($error != '') { ?>

    modalEdit.show();

<?php } ?>


// NOTIFIKASI MELAYANG
document.addEventListener('DOMContentLoaded', function () {

    const toastEdit = document.getElementById('toastNotifikasi');

    if (toastEdit) {
        new bootstrap.Toast(toastEdit, {
            delay: 3500
        }).show();
    }

    const toastHapus = document.getElementById('toastHapus');

    if (toastHapus) {
        new bootstrap.Toast(toastHapus, {
            delay: 3500
        }).show();
    }

});

</script>

</body>
</html>
