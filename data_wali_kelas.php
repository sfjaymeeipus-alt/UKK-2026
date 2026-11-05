<?php

include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];


/* =====================================
   FUNGSI OTOMATIS MENONAKTIFKAN DATA GANDA
===================================== */

function periksaWaliKelasGanda($koneksi) {

    $jumlahDinonaktifkan = 0;

    $queryCek = mysqli_query($koneksi, "
        SELECT id, guru_id, kelas_id, tahun_ajaran_id
        FROM t_wali_kelas
        WHERE status_aktif = 1
        ORDER BY id ASC
    ");

    if (!$queryCek) {
        return 0;
    }

    $guruTerpakai = [];
    $kelasTerpakai = [];

    while ($data = mysqli_fetch_assoc($queryCek)) {

        $id = (int) $data['id'];
        $guru = (int) $data['guru_id'];
        $kelas = (int) $data['kelas_id'];
        $tahun = (int) $data['tahun_ajaran_id'];

        $kunciGuru = $tahun . "_" . $guru;
        $kunciKelas = $tahun . "_" . $kelas;

        if (
            isset($guruTerpakai[$kunciGuru]) ||
            isset($kelasTerpakai[$kunciKelas])
        ) {

            $update = mysqli_prepare(
                $koneksi,
                "UPDATE t_wali_kelas
                 SET status_aktif = 0
                 WHERE id = ?"
            );

            if ($update) {

                mysqli_stmt_bind_param($update, "i", $id);

                if (mysqli_stmt_execute($update)) {
                    $jumlahDinonaktifkan++;
                }

                mysqli_stmt_close($update);
            }

        } else {

            $guruTerpakai[$kunciGuru] = true;
            $kelasTerpakai[$kunciKelas] = true;

        }
    }

    return $jumlahDinonaktifkan;
}


/* =====================================
   PROSES TAMBAH WALI KELAS
===================================== */

if (isset($_POST['simpan_tambah'])) {

    $guru_id = (int) $_POST['guru_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];

    $tanggal_mulai = $_POST['tanggal_mulai'];

    $tanggal_selesai = !empty($_POST['tanggal_selesai'])
        ? $_POST['tanggal_selesai']
        : null;

    $status_aktif = (int) $_POST['status_aktif'];

    if (
        $guru_id <= 0 ||
        $kelas_id <= 0 ||
        $tahun_ajaran_id <= 0 ||
        empty($tanggal_mulai) ||
        !in_array($status_aktif, [0, 1])
    ) {

        $_SESSION['pesan_hapus'] =
            "Data wali kelas gagal ditambahkan. Periksa kembali isian.";

        $_SESSION['jenis_pesan'] = "danger";

    } elseif (
        $tanggal_selesai !== null &&
        $tanggal_selesai < $tanggal_mulai
    ) {

        $_SESSION['pesan_hapus'] =
            "Tanggal selesai tidak boleh sebelum tanggal mulai.";

        $_SESSION['jenis_pesan'] = "danger";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_wali_kelas
            (
                guru_id,
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
                $guru_id,
                $kelas_id,
                $tahun_ajaran_id,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif
            );

            if (mysqli_stmt_execute($stmt)) {

                $idBaru = mysqli_insert_id($koneksi);

                mysqli_stmt_close($stmt);

                // Periksa dan nonaktifkan data ganda
                periksaWaliKelasGanda($koneksi);

                // Periksa status akhir data baru
                $cekStatus = mysqli_prepare(
                    $koneksi,
                    "SELECT status_aktif
                     FROM t_wali_kelas
                     WHERE id = ?"
                );

                mysqli_stmt_bind_param(
                    $cekStatus,
                    "i",
                    $idBaru
                );

                mysqli_stmt_execute($cekStatus);

                $hasilStatus = mysqli_stmt_get_result($cekStatus);

                $dataStatus = mysqli_fetch_assoc($hasilStatus);

                mysqli_stmt_close($cekStatus);

                if (
                    $status_aktif == 1 &&
                    $dataStatus['status_aktif'] == 0
                ) {

                    $_SESSION['pesan_hapus'] =
                        "Data berhasil ditambahkan, tetapi otomatis menjadi Tidak Aktif karena terdapat wali kelas yang ganda.";

                    $_SESSION['jenis_pesan'] = "warning";

                } else {

                    $_SESSION['pesan_hapus'] =
                        "Data wali kelas berhasil ditambahkan.";

                    $_SESSION['jenis_pesan'] = "success";

                }

            } else {

                $_SESSION['pesan_hapus'] =
                    "Data wali kelas gagal ditambahkan: " . mysqli_stmt_error($stmt);

                $_SESSION['jenis_pesan'] = "danger";

                mysqli_stmt_close($stmt);

            }

        } else {

            $_SESSION['pesan_hapus'] =
                "Query gagal diproses: " . mysqli_error($koneksi);

            $_SESSION['jenis_pesan'] = "danger";

        }
    }

    header("Location: data_wali_kelas.php");
    exit;
}


/* =====================================
   PROSES EDIT WALI KELAS
===================================== */

if (isset($_POST['simpan_edit'])) {

    $id = (int) $_POST['id'];
    $guru_id = (int) $_POST['guru_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];

    $tanggal_mulai = $_POST['tanggal_mulai'];

    $tanggal_selesai = !empty($_POST['tanggal_selesai'])
        ? $_POST['tanggal_selesai']
        : null;

    $status_aktif = (int) $_POST['status_aktif'];

    if (
        $id <= 0 ||
        $guru_id <= 0 ||
        $kelas_id <= 0 ||
        $tahun_ajaran_id <= 0 ||
        empty($tanggal_mulai) ||
        !in_array($status_aktif, [0, 1])
    ) {

        $_SESSION['pesan_hapus'] =
            "Data tidak valid. Periksa kembali isian.";

        $_SESSION['jenis_pesan'] = "danger";

    } elseif (
        $tanggal_selesai !== null &&
        $tanggal_selesai < $tanggal_mulai
    ) {

        $_SESSION['pesan_hapus'] =
            "Tanggal selesai tidak boleh sebelum tanggal mulai.";

        $_SESSION['jenis_pesan'] = "danger";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_wali_kelas
             SET guru_id = ?,
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
                $guru_id,
                $kelas_id,
                $tahun_ajaran_id,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                periksaWaliKelasGanda($koneksi);

                $cekStatus = mysqli_prepare(
                    $koneksi,
                    "SELECT status_aktif
                     FROM t_wali_kelas
                     WHERE id = ?"
                );

                mysqli_stmt_bind_param($cekStatus, "i", $id);

                mysqli_stmt_execute($cekStatus);

                $hasilStatus = mysqli_stmt_get_result($cekStatus);

                $dataStatus = mysqli_fetch_assoc($hasilStatus);

                mysqli_stmt_close($cekStatus);

                if (
                    $status_aktif == 1 &&
                    $dataStatus['status_aktif'] == 0
                ) {

                    $_SESSION['pesan_hapus'] =
                        "Data berhasil disimpan, tetapi otomatis menjadi Tidak Aktif karena terdapat wali kelas yang ganda.";

                    $_SESSION['jenis_pesan'] = "warning";

                } else {

                    $_SESSION['pesan_hapus'] =
                        "Data wali kelas berhasil diperbarui.";

                    $_SESSION['jenis_pesan'] = "success";

                }

            } else {

                $_SESSION['pesan_hapus'] =
                    "Data gagal diperbarui: " . mysqli_stmt_error($stmt);

                $_SESSION['jenis_pesan'] = "danger";

                mysqli_stmt_close($stmt);
            }

        } else {

            $_SESSION['pesan_hapus'] =
                "Query gagal diproses: " . mysqli_error($koneksi);

            $_SESSION['jenis_pesan'] = "danger";
        }
    }

    header("Location: data_wali_kelas.php");
    exit;
}


/* =====================================
   PEMERIKSAAN DATA GANDA OTOMATIS
===================================== */

$jumlahDuplikatDinonaktifkan =
    periksaWaliKelasGanda($koneksi);


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

if (!in_array(
    $jenis_notifikasi,
    ['success', 'danger', 'warning', 'info']
)) {
    $jenis_notifikasi = 'success';
}


/* =====================================
   DATA GURU UNTUK MODAL EDIT
===================================== */

$queryGuru = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_guru ORDER BY nama ASC"
);


/* =====================================
   DATA GURU UNTUK MODAL TAMBAH
===================================== */

$queryGuruTambah = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_guru ORDER BY nama ASC"
);


/* =====================================
   DATA KELAS UNTUK MODAL EDIT
===================================== */

$queryKelas = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);


/* =====================================
   DATA KELAS UNTUK MODAL TAMBAH
===================================== */

$queryKelasTambah = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);


/* =====================================
   DATA TAHUN AJARAN UNTUK MODAL EDIT
===================================== */

$queryTahun = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id ASC"
);


/* =====================================
   DATA TAHUN AJARAN UNTUK MODAL TAMBAH
===================================== */

$queryTahunTambah = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id ASC"
);


/* =====================================
   MENGHITUNG JUMLAH KELAS AKTIF
===================================== */

$queryKelasAktif = mysqli_query($koneksi, "
    SELECT COUNT(DISTINCT kelas_id) AS total
    FROM t_wali_kelas
    WHERE status_aktif = 1
");

$dataKelasAktif = mysqli_fetch_assoc($queryKelasAktif);

$totalKelasAktif = (int) $dataKelasAktif['total'];


/* =====================================
   MENGECEK DATA GANDA
===================================== */

$queryGanda = mysqli_query($koneksi, "

    SELECT guru_id, tahun_ajaran_id
    FROM t_wali_kelas
    WHERE status_aktif = 1
    GROUP BY guru_id, tahun_ajaran_id
    HAVING COUNT(*) > 1

    UNION

    SELECT kelas_id, tahun_ajaran_id
    FROM t_wali_kelas
    WHERE status_aktif = 1
    GROUP BY kelas_id, tahun_ajaran_id
    HAVING COUNT(*) > 1

");

$totalGanda = mysqli_num_rows($queryGanda);


/* =====================================
   MENGAMBIL DATA WALI KELAS
===================================== */

$query = "

    SELECT
        wk.id,
        wk.guru_id,
        wk.kelas_id,
        wk.tahun_ajaran_id,
        g.nama AS nama_guru,
        k.nama AS nama_kelas,
        ta.nama AS tahun_ajaran,
        wk.tanggal_mulai,
        wk.tanggal_selesai,
        wk.status_aktif

    FROM t_wali_kelas wk

    INNER JOIN t_guru g
        ON wk.guru_id = g.id

    INNER JOIN t_kelas k
        ON wk.kelas_id = k.id

    INNER JOIN t_tahun_ajaran ta
        ON wk.tahun_ajaran_id = ta.id

    ORDER BY wk.id ASC

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

    <title>Data Wali Kelas - Sistem Informasi Pelanggaran Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

</head>


<body class="bg-light">


<div class="container-fluid">

    <div class="row">


        <!-- SIDEBAR -->

        <aside class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3"
               style="position: sticky; top: 0; height: 100vh; overflow-y: auto; align-self: flex-start;">

            <h5 class="fw-bold mb-1">
                Sistem Informasi
            </h5>

            <h6 class="fw-bold">
                Pelanggaran Siswa
            </h6>

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

                <a href="data_wali_kelas.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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
                        Data Wali Kelas
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
                            Admin
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


                <!-- INFORMASI WALI KELAS -->

                <?php if ($totalGanda > 0) { ?>

                    <div class="alert alert-danger small">

                        <strong>Perhatian!</strong>

                        Masih ditemukan data wali kelas aktif yang ganda.

                        Silakan periksa kembali data.

                    </div>

                <?php } else { ?>

                    <div class="alert alert-success small">

                        <strong>Informasi:</strong>

                        Pemeriksaan data berhasil.

                        Sebanyak
                        <?= $jumlahDuplikatDinonaktifkan; ?>
                        data ganda otomatis dinonaktifkan.

                        Saat ini terdapat
                        <?= $totalKelasAktif; ?>
                        kelas yang memiliki wali kelas aktif.

                    </div>

                <?php } ?>



                <!-- KARTU TABEL -->

                <div class="card border-0 shadow-sm rounded-3">


                    <div class="card-header bg-white p-3 p-md-4
                                d-flex justify-content-between align-items-center
                                flex-wrap gap-3">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Tabel Data Wali Kelas
                            </h5>

                            <small class="text-secondary">
                                Data guru yang bertugas sebagai wali kelas.
                            </small>

                        </div>


                        <!-- TOMBOL TAMBAH MEMBUKA MODAL -->

                        <button type="button"
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTambahWaliKelas">

                            + Tambah Wali Kelas

                        </button>

                    </div>



                    <!-- TABEL -->

                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelWaliKelas"
                                   class="table table-bordered table-hover
                                          align-middle w-100">


                                <thead class="table-dark">

                                    <tr>

                                        <th class="text-center">No</th>

                                        <th>Nama Guru</th>

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


                                        <td class="fw-semibold">

                                            <?= htmlspecialchars($data['nama_guru'] ?? ''); ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars($data['nama_kelas'] ?? ''); ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars($data['tahun_ajaran'] ?? ''); ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>

                                        </td>


                                        <td>

                                            <?= !empty($data['tanggal_selesai'])
                                                ? htmlspecialchars($data['tanggal_selesai'])
                                                : '-'; ?>

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



                                        <td class="text-center">


                                            <div class="d-flex justify-content-center gap-1">


                                                <!-- TOMBOL EDIT -->

                                                <button
                                                    type="button"
                                                    class="btn btn-warning btn-sm tombol-edit"

                                                    data-id="<?= (int) $data['id']; ?>"

                                                    data-guru="<?= (int) $data['guru_id']; ?>"

                                                    data-kelas="<?= (int) $data['kelas_id']; ?>"

                                                    data-tahun="<?= (int) $data['tahun_ajaran_id']; ?>"

                                                    data-mulai="<?= htmlspecialchars($data['tanggal_mulai'], ENT_QUOTES); ?>"

                                                    data-selesai="<?= htmlspecialchars($data['tanggal_selesai'] ?? '', ENT_QUOTES); ?>"

                                                    data-status="<?= (int) $data['status_aktif']; ?>">

                                                    Edit

                                                </button>



                                                <!-- TOMBOL HAPUS -->

                                                <a href="hapus_wali_kelas.php?id=<?= urlencode($data['id']); ?>"
                                                   class="btn btn-danger btn-sm"

                                                   onclick="return confirm('Yakin ingin menghapus data wali kelas ini?');">

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




<!-- ================================================= -->
<!-- MODAL TAMBAH WALI KELAS -->
<!-- ================================================= -->

<div class="modal fade"
     id="modalTambahWaliKelas"
     tabindex="-1"
     aria-labelledby="judulModalTambah"
     aria-hidden="true">


    <div class="modal-dialog modal-dialog-centered modal-lg">


        <div class="modal-content">


            <form method="POST" action="data_wali_kelas.php">


                <div class="modal-header">

                    <div>

                        <h5 class="modal-title fw-bold"
                            id="judulModalTambah">

                            Tambah Data Wali Kelas

                        </h5>

                        <small class="text-secondary">

                            Silakan isi informasi wali kelas yang akan ditambahkan.

                        </small>

                    </div>


                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>



                <div class="modal-body">


                    <!-- NAMA GURU -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Nama Guru
                        </label>

                        <select name="guru_id"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Guru
                            </option>


                            <?php

                            while ($guru = mysqli_fetch_assoc($queryGuruTambah)) {

                            ?>

                                <option value="<?= (int) $guru['id']; ?>">

                                    <?= htmlspecialchars($guru['nama']); ?>

                                </option>

                            <?php

                            }

                            ?>


                        </select>

                    </div>



                    <!-- KELAS -->

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

                            while ($kelas = mysqli_fetch_assoc($queryKelasTambah)) {

                            ?>

                                <option value="<?= (int) $kelas['id']; ?>">

                                    <?= htmlspecialchars($kelas['nama']); ?>

                                </option>

                            <?php

                            }

                            ?>


                        </select>

                    </div>



                    <!-- TAHUN AJARAN -->

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

                            while ($tahun = mysqli_fetch_assoc($queryTahunTambah)) {

                            ?>

                                <option value="<?= (int) $tahun['id']; ?>">

                                    <?= htmlspecialchars($tahun['nama']); ?>

                                </option>

                            <?php

                            }

                            ?>


                        </select>

                    </div>



                    <!-- TANGGAL -->

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



                    <!-- STATUS -->

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




<!-- ================================================= -->
<!-- MODAL EDIT WALI KELAS -->
<!-- ================================================= -->

<div class="modal fade"
     id="modalEditWaliKelas"
     tabindex="-1"
     aria-labelledby="judulModalEdit"
     aria-hidden="true">


    <div class="modal-dialog modal-dialog-centered modal-lg">


        <div class="modal-content">


            <form method="POST" action="data_wali_kelas.php">


                <div class="modal-header">

                    <h5 class="modal-title fw-bold" id="judulModalEdit">
                        Edit Wali Kelas
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>



                <div class="modal-body">


                    <input type="hidden" name="id" id="edit_id">



                    <!-- NAMA GURU -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Nama Guru
                        </label>

                        <select name="guru_id"
                                id="edit_guru"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Guru
                            </option>


                            <?php

                            while ($guru = mysqli_fetch_assoc($queryGuru)) {

                            ?>

                                <option value="<?= (int) $guru['id']; ?>">

                                    <?= htmlspecialchars($guru['nama']); ?>

                                </option>

                            <?php

                            }

                            ?>


                        </select>

                    </div>



                    <!-- KELAS -->

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

                            while ($kelas = mysqli_fetch_assoc($queryKelas)) {

                            ?>

                                <option value="<?= (int) $kelas['id']; ?>">

                                    <?= htmlspecialchars($kelas['nama']); ?>

                                </option>

                            <?php

                            }

                            ?>


                        </select>

                    </div>



                    <!-- TAHUN AJARAN -->

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

                            while ($tahun = mysqli_fetch_assoc($queryTahun)) {

                            ?>

                                <option value="<?= (int) $tahun['id']; ?>">

                                    <?= htmlspecialchars($tahun['nama']); ?>

                                </option>

                            <?php

                            }

                            ?>


                        </select>

                    </div>



                    <!-- TANGGAL -->

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



                    <!-- STATUS -->

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




<!-- ================================================= -->
<!-- NOTIFIKASI MELAYANG KANAN ATAS -->
<!-- ================================================= -->

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




<!-- JQUERY -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>



<!-- DATATABLES -->

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>



<!-- BOOTSTRAP JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>




<script>

$(document).ready(function () {


    // DATATABLES

    $('#tabelWaliKelas').DataTable({

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

            zeroRecords: "Data wali kelas tidak ditemukan",

            emptyTable: "Belum ada data wali kelas",


            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Berikutnya",

                previous: "Sebelumnya"

            }

        }

    });



    // MODAL EDIT

    $('.tombol-edit').on('click', function () {


        $('#edit_id').val($(this).data('id'));

        $('#edit_guru').val($(this).data('guru'));

        $('#edit_kelas').val($(this).data('kelas'));

        $('#edit_tahun').val($(this).data('tahun'));

        $('#edit_mulai').val($(this).data('mulai'));

        $('#edit_selesai').val($(this).data('selesai'));

        $('#edit_status').val($(this).data('status'));


        var modal = new bootstrap.Modal(

            document.getElementById('modalEditWaliKelas')

        );


        modal.show();


    });



    // NOTIFIKASI HILANG OTOMATIS SETELAH 3 DETIK

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