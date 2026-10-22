<?php

session_start();

include "config/koneksi.php";

// Pastikan data dikirim melalui form login
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: login.php");
    exit;
}

// Ambil email dan password dari form
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input
if ($email == '' || $password == '') {
    echo "Email dan password wajib diisi.";
    echo "<br><a href='login.php'>Kembali ke login</a>";
    exit;
}

// Cari akun berdasarkan email
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, name, email, password, role
     FROM t_users
     WHERE email = ?
     LIMIT 1"
);

if (!$stmt) {
    die("Terjadi kesalahan database: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

// Periksa password
$password_cocok = false;

if ($data) {

    // Password yang disimpan menggunakan password_hash()
    if (password_verify($password, $data['password'])) {
        $password_cocok = true;
    }

    // Mendukung akun lama yang password-nya masih teks biasa
    elseif ($password === $data['password']) {
        $password_cocok = true;
    }
}

// Jika email dan password benar
if ($password_cocok) {

    // Izinkan admin dan guru
    if (
        $data['role'] == 'admin' ||
        $data['role'] == 'guru'
    ) {

        session_regenerate_id(true);

        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['nama'] = $data['name'];
        $_SESSION['role'] = $data['role'];

        header("Location: dashboard.php");
        exit;

    } else {

        echo "Role akun tidak diizinkan.";
        echo "<br><a href='login.php'>Kembali ke login</a>";
        exit;
    }

} else {

    echo "Email atau password salah.";
    echo "<br><a href='login.php'>Kembali ke login</a>";
    exit;
}

mysqli_stmt_close($stmt);

?>