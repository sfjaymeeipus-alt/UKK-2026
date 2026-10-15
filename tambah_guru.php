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
    $status_aktif = (int) $_POST['status_aktif'];

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

            // Buat password awal secara acak
            $password_awal = bin2hex(random_bytes(6));
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
                    throw new Exception(mysqli_stmt_error($buat_user));
                }

                // Ambil ID akun yang baru dibuat
                $user_id = mysqli_insert_id($koneksi);

                // Simpan data guru dengan user_id yang benar
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
                    throw new Exception(mysqli_stmt_error($buat_guru));
                }

                // Simpan kedua data jika semuanya berhasil
                mysqli_commit($koneksi);

                // Tampilkan password awal sekali kepada admin
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
        <aside class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <p class="text-white">
                Halo, <?= htmlspecialchars($nama); ?>
            </p>

            <ul class="nav nav-pills flex-column gap-2">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link active">
                        Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">
                        Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link text-white">
                        Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link text-white">
                        Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">
                        Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link text-white">
                        Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>

                <hr class="text-secondary">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        Logout
                    </a>
                </li>

            </ul>

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

                    <form method="POST" action="">

                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>

                            <input
                                type="text"
                                name="nip"
                                id="nip"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Guru</label>

                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                required>
                        </div>

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
