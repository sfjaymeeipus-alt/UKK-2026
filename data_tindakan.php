
<?php
include "middleware/auth.php";
include "config/koneksi.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if (!in_array($role, ['admin', 'guru', 'wali kelas'])) {
    exit("Anda tidak memiliki akses.");
}

// ======================================
// MENU
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

$menuGuru = [
    ["Dashboard", "dashboard.php"],
    ["Catatan Pelanggaran", "data_catatan_pelanggaran.php"],
    ["Tindakan", "data_tindakan.php"],
    ["Riwayat", "data_riwayat.php"],
    ["Rekap Poin", "data_rekap_poin.php"],
    ["About Me", "about_me.php"]
];

$menuWali = [
    ["Dashboard", "dashboard.php"],
    ["Catatan Pelanggaran", "data_catatan_pelanggaran.php"],
    ["Tindakan", "data_tindakan.php"],
    ["Data Siswa", "walikelas/data_siswa.php"],
    ["Riwayat", "data_riwayat.php"],
    ["Rekap Poin", "data_rekap_poin.php"],
    ["Laporan", "walikelas/data_laporan.php"],
    ["About Me", "about_me.php"]
];

// ======================================
// CEK WALI KELAS
// ======================================

$isWaliKelas = ($role == 'wali kelas');
$guru_id_login = 0;

if ($role == 'guru') {
    $stmtGuru = mysqli_prepare($koneksi, "
        SELECT id
        FROM t_guru
        WHERE user_id = ?
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmtGuru, "i", $user_id);
    mysqli_stmt_execute($stmtGuru);

    $dataGuru = mysqli_stmt_get_result($stmtGuru)->fetch_assoc();
    mysqli_stmt_close($stmtGuru);

    if ($dataGuru) {
        $guru_id_login = (int) $dataGuru['id'];

        $stmtWali = mysqli_prepare($koneksi, "
            SELECT COUNT(*) AS total
            FROM t_wali_kelas wk
            JOIN t_tahun_ajaran ta
                ON ta.id = wk.tahun_ajaran_id
            WHERE wk.guru_id = ?
              AND wk.status_aktif = 1
              AND ta.status_aktif = 1
        ");

        mysqli_stmt_bind_param($stmtWali, "i", $guru_id_login);
        mysqli_stmt_execute($stmtWali);

        $dataWali = mysqli_stmt_get_result($stmtWali)->fetch_assoc();

        $isWaliKelas = (int) ($dataWali['total'] ?? 0) > 0;

        mysqli_stmt_close($stmtWali);
    }
}

// ======================================
// TENTUKAN MENU
// ======================================

if ($role == 'admin') {
    $menu = $menuAdmin;
} elseif ($isWaliKelas || $role == 'wali kelas') {
    $menu = $menuWali;
} else {
    $menu = $menuGuru;
}

$halamanAktif = basename($_SERVER['PHP_SELF']);

// ======================================
// DAFTAR TINDAKAN
// ======================================

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

// ======================================
// NOTIFIKASI
// ======================================

function setPesan($pesan, $jenis = 'success')
{
    $_SESSION['pesan_hapus'] = $pesan;
    $_SESSION['jenis_pesan'] = $jenis;
}

// ======================================
// SIMPAN TINDAKAN
// ======================================

if (isset($_POST['simpan_tindakan'])) {
    try {
        $catatan_id = (int) ($_POST['catatan_id'] ?? 0);
        $tindakan = trim($_POST['tindakan'] ?? '');

        if ($catatan_id <= 0) {
            throw new Exception("Data catatan tidak valid.");
        }

        if (!in_array($tindakan, $daftarTindakan, true)) {
            throw new Exception("Silakan pilih tindakan yang tersedia.");
        }

        if ($role == 'guru') {
            if ($guru_id_login <= 0) {
                throw new Exception("Data guru tidak ditemukan.");
            }

            $stmt = mysqli_prepare($koneksi, "
                UPDATE t_pelanggaran_siswa
                SET tindakan = ?
                WHERE id = ?
                  AND guru_id = ?
                  AND status IN ('Diproses', 'Ditindaklanjuti')
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "sii",
                $tindakan,
                $catatan_id,
                $guru_id_login
            );
        } else {
            $stmt = mysqli_prepare($koneksi, "
                UPDATE t_pelanggaran_siswa
                SET tindakan = ?
                WHERE id = ?
                  AND status IN ('Diproses', 'Ditindaklanjuti')
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $tindakan,
                $catatan_id
            );
        }

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            setPesan("Tindakan siswa berhasil disimpan.");
        } else {
            setPesan(
                "Data tidak ditemukan, sudah selesai, atau tindakan tidak berubah.",
                "warning"
            );
        }

        mysqli_stmt_close($stmt);

    } catch (Throwable $e) {
        setPesan(
            "Gagal menyimpan tindakan: " . $e->getMessage(),
            "danger"
        );
    }

    header("Location: data_tindakan.php");
    exit;
}

// ======================================
// AMBIL DATA TINDAKAN
// ======================================

$sql = "
    SELECT
        ps.id,
        ps.nama_siswa,
        ps.nama_kelas,

        CASE
            WHEN pk.nama IS NOT NULL THEN pk.nama
            WHEN ps.nama_pelanggaran IS NOT NULL
                 AND ps.nama_pelanggaran != '0'
                 AND ps.nama_pelanggaran != ''
                THEN ps.nama_pelanggaran
            WHEN p.nama IS NOT NULL THEN p.nama
            ELSE '-'
        END AS nama_pelanggaran,

        CASE
            WHEN g.nama IS NOT NULL THEN g.nama
            WHEN ps.nama_guru IS NOT NULL
                 AND ps.nama_guru != '0'
                 AND ps.nama_guru != ''
                THEN ps.nama_guru
            ELSE '-'
        END AS nama_guru,

        ps.tanggal,
        ps.keterangan,
        ps.poin,
        ps.tindakan,
        ps.status

    FROM t_pelanggaran_siswa ps

    LEFT JOIN t_pelanggaran p
        ON p.id = ps.pelanggaran_id

    LEFT JOIN t_pelanggaran_kategori pk
        ON pk.id = COALESCE(
            NULLIF(ps.pelanggaran_kategori_id, 0),
            p.pelanggaran_kategori_id
        )

    LEFT JOIN t_guru g
        ON g.id = ps.guru_id

    WHERE ps.status IN ('Diproses', 'Ditindaklanjuti')
";

if ($role == 'guru') {
    $sql .= " AND ps.guru_id = " . $guru_id_login;
}

$sql .= " ORDER BY ps.id DESC";

$query = mysqli_query($koneksi, $sql);
$totalData = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tindakan - Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <aside class="col-md-3 col-lg-2 bg-dark text-white p-3 position-sticky top-0 vh-100 overflow-auto align-self-start">
            <h5 class="fw-bold mb-1">Sistem Informasi</h5>
            <h6 class="fw-bold">Pelanggaran Siswa</h6>

            <p class="text-secondary small mb-4">
                SMK Muhammadiyah Kota Tasikmalaya
            </p>

            <p class="text-secondary small fw-bold mb-2">MENU NAVIGASI</p>

            <nav class="nav flex-column gap-1">
                <?php foreach ($menu as $item) { ?>
                    <a href="<?= htmlspecialchars($item[1]); ?>"
                       class="nav-link rounded py-2 <?= $halamanAktif == basename($item[1]) ? 'active bg-primary text-white' : 'text-white'; ?>">
                        <?= htmlspecialchars($item[0]); ?>
                    </a>
                <?php } ?>

                <hr class="border-secondary my-2">

                <a href="logout.php" class="nav-link text-danger rounded py-2">
                    Logout
                </a>
            </nav>
        </aside>

        <main class="col-md-9 col-lg-10 p-0">

            <header class="bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Data Tindakan Siswa</h4>
                    <small class="text-secondary">Sistem Informasi Pelanggaran Siswa</small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="text-end">
                        <div class="fw-bold"><?= htmlspecialchars($nama); ?></div>
                        <small class="text-secondary">
                            <?= $role == 'admin' ? 'Admin' : ($isWaliKelas || $role == 'wali kelas' ? 'Wali Kelas' : 'Guru'); ?>
                        </small>
                    </div>

                    <div class="bg-primary-subtle text-primary rounded-circle d-flex justify-content-center align-items-center fw-bold flex-shrink-0"
                         style="width:42px;height:42px;">
                        <?= htmlspecialchars(strtoupper(substr($nama, 0, 1))); ?>
                    </div>
                </div>
            </header>

            <section class="p-3 p-md-4">

                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold mb-1">Daftar Tindakan Siswa</h5>
                            <small class="text-secondary">
                                Pilih dan simpan tindakan untuk setiap pelanggaran siswa.
                            </small>
                        </div>

                        <span class="badge bg-primary fs-6">
                            <?= $totalData; ?> Data
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">

                            <table id="tabelTindakan" class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Nama Pelanggaran</th>
                                        <th>Nama Guru</th>
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

                                    while ($data = mysqli_fetch_assoc($query)) {

                                        $namaPelanggaran = trim(
                                            preg_replace(
                                                '/\s+[0-9]+$/',
                                                '',
                                                $data['nama_pelanggaran'] ?? '-'
                                            )
                                        );

                                        $status = $data['status'] ?? '-';

                                        $warnaStatus = 'secondary';

                                        if ($status == 'Diproses') {
                                            $warnaStatus = 'warning text-dark';
                                        } elseif ($status == 'Ditindaklanjuti') {
                                            $warnaStatus = 'primary';
                                        }

                                        $formId = 'formTindakan' . (int) $data['id'];
                                        $tindakanSaatIni = $data['tindakan'] ?? '';
                                    ?>

                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($namaPelanggaran); ?></td>
                                        <td><?= htmlspecialchars($data['nama_guru'] ?? '-'); ?></td>
                                        <td>
                                            <?= !empty($data['tanggal'])
                                                ? date('d-m-Y', strtotime($data['tanggal']))
                                                : '-'; ?>
                                        </td>
                                        <td><?= (int) ($data['poin'] ?? 0); ?></td>
                                        <td><?= htmlspecialchars($data['keterangan'] ?? '-'); ?></td>

                                        <td>
                                            <form method="POST" action="data_tindakan.php" id="<?= $formId; ?>">
                                                <input type="hidden" name="catatan_id" value="<?= (int) $data['id']; ?>">

                                                <select name="tindakan" class="form-select form-select-sm" required>
                                                    <option value="">Pilih Tindakan</option>

                                                    <?php foreach ($daftarTindakan as $item) { ?>
                                                        <option value="<?= htmlspecialchars($item); ?>"
                                                            <?= $tindakanSaatIni == $item ? 'selected' : ''; ?>>
                                                            <?= htmlspecialchars($item); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </form>
                                        </td>

                                        <td>
                                            <span class="badge bg-<?= $warnaStatus; ?>">
                                                <?= htmlspecialchars($status); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <button type="submit"
                                                    name="simpan_tindakan"
                                                    value="1"
                                                    form="<?= $formId; ?>"
                                                    class="btn btn-success btn-sm w-100">
                                                Simpan Tindakan
                                            </button>
                                        </td>
                                    </tr>

                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="card-footer bg-white p-3">
                        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
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

<?php if (isset($_SESSION['pesan_hapus'])) {
    $jenis = $_SESSION['jenis_pesan'] ?? 'success';

    $jenis = in_array($jenis, [
        'success', 'danger', 'warning', 'info',
        'primary', 'secondary', 'dark'
    ]) ? $jenis : 'success';
?>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
    <div id="notifikasiToast"
         class="toast align-items-center text-bg-<?= $jenis; ?> border-0"
         role="alert" aria-live="assertive" aria-atomic="true">

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
unset($_SESSION['pesan_hapus'], $_SESSION['jenis_pesan']);
}
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelTindakan').DataTable({
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
            zeroRecords: "Data tindakan tidak ditemukan",
            emptyTable: "Belum ada data tindakan siswa",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });

    const toastElement = document.getElementById('notifikasiToast');

    if (toastElement) {
        new bootstrap.Toast(toastElement, {
            delay: 3000
        }).show();
    }

});
</script>

</body>
</html>
