<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];
$pesan = "";

if (isset($_POST['simpan'])) {

    $nip = trim($_POST['nip']);
    $nama_guru = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password_awal = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $status_aktif = (int) $_POST['status_aktif'];

    // Validasi password
    if ($password_awal !== $konfirmasi_password) {

        $pesan = "Konfirmasi password tidak cocok.";

    } elseif (strlen($password_awal) < 6) {

        $pesan = "Password minimal 6 karakter.";

    } else {

        // Periksa apakah email sudah digunakan
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_users WHERE email = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $email);
        mysqli_stmt_execute($cek);
        $hasil_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil_cek) > 0) {

            $pesan = "Email sudah digunakan oleh akun lain.";

        } else {

            // Periksa apakah NIP sudah digunakan
            $cek_nip = mysqli_prepare(
                $koneksi,
                "SELECT id FROM t_guru WHERE nip = ?"
            );

            mysqli_stmt_bind_param($cek_nip, "s", $nip);
            mysqli_stmt_execute($cek_nip);
            $hasil_nip = mysqli_stmt_get_result($cek_nip);

            if (mysqli_num_rows($hasil_nip) > 0) {

                $pesan = "NIP sudah terdaftar.";

            } else {

                // Enkripsi password
                $password_hash = password_hash(
                    $password_awal,
                    PASSWORD_DEFAULT
                );

                // Mulai transaksi database
                mysqli_begin_transaction($koneksi);

                try {

                    // Buat akun pengguna
                    $role = "guru";

                    $buat_user = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO t_users (name, email, password, role)
                         VALUES (?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $buat_user,
                        "ssss",
                        $nama_guru,
                        $email,
                        $password_hash,
                        $role
                    );

                    if (!mysqli_stmt_execute($buat_user)) {
                        throw new Exception(
                            mysqli_stmt_error($buat_user)
                        );
                    }

                    // Ambil ID akun pengguna
                    $user_id = mysqli_insert_id($koneksi);

                    // Simpan data guru
                    $buat_guru = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO t_guru
                         (nip, nama, email, status_aktif, user_id)
                         VALUES (?, ?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $buat_guru,
                        "sssii",
                        $nip,
                        $nama_guru,
                        $email,
                        $status_aktif,
                        $user_id
                    );

                    if (!mysqli_stmt_execute($buat_guru)) {
                        throw new Exception(
                            mysqli_stmt_error($buat_guru)
                        );
                    }

                    // Simpan transaksi
                    mysqli_commit($koneksi);

                    // Simpan informasi akun baru untuk admin
                    $_SESSION['password_guru_baru'] = [
                        'nama' => $nama_guru,
                        'email' => $email,
                        'password' => $password_awal
                    ];

                    header("Location: data_guru.php?berhasil=tambah");
                    exit;

                } catch (Throwable $e) {

                    mysqli_rollback($koneksi);

                    $pesan = "Gagal menambahkan guru: " . $e->getMessage();
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Guru</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
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

        <!-- KONTEN TAMBAH GURU -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h2 class="mb-3">Tambah Data Guru</h2>

                    <p class="text-muted">
                        Silakan isi formulir untuk menambahkan data guru.
                    </p>

                    <?php if ($pesan != "") { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>

                    <?php } ?>

                    <form method="POST" action="" autocomplete="off">

                        <!-- NIP -->
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>

                            <input
                                type="text"
                                name="nip"
                                id="nip"
                                class="form-control"
                                autocomplete="off"
                                value="<?= htmlspecialchars($_POST['nip'] ?? '') ?>"
                                required>
                        </div>

                        <!-- NAMA GURU -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Guru</label>

                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                autocomplete="off"
                                value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                                required>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                autocomplete="off"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                required>
                        </div>

                        <!-- PASSWORD -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                minlength="6"
                                autocomplete="new-password"
                                required>

                            <div class="form-text">
                                Password minimal 6 karakter.
                            </div>
                        </div>

                        <!-- KONFIRMASI PASSWORD -->
                        <div class="mb-3">
                            <label for="konfirmasi_password" class="form-label">
                                Konfirmasi Password
                            </label>

                            <input
                                type="password"
                                name="konfirmasi_password"
                                id="konfirmasi_password"
                                class="form-control"
                                minlength="6"
                                autocomplete="new-password"
                                required>
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

                        <!-- TOMBOL -->
                        <button
                            type="submit"
                            name="simpan"
                            class="btn btn-primary">
                            Simpan
                        </button>

                        <a href="data_guru.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>

            </div>

        </main>

    </div>
</div>

</body>
</html>