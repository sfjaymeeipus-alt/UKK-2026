<?php

include "config/koneksi.php";
include "middleware/auth.php";

// Cek hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Ambil ID kelas
if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || (int) $_GET['id'] < 1) {
    header("Location: data_kelas.php");
    exit;
}

$id = (int) $_GET['id'];

// Hapus data penempatan siswa yang terkait dengan kelas
$hapus_penempatan = mysqli_query(
    $koneksi,
    "DELETE FROM t_kelas_siswa WHERE kelas_id = '$id'"
);

if ($hapus_penempatan) {

    // Hapus data kelas
    $hapus_kelas = mysqli_query(
        $koneksi,
        "DELETE FROM t_kelas WHERE id = '$id'"
    );

    if ($hapus_kelas) {

        if (mysqli_affected_rows($koneksi) > 0) {
            $_SESSION['pesan_sukses'] = "Data kelas berhasil dihapus.";
        } else {
            $_SESSION['pesan_error'] = "Data kelas tidak ditemukan.";
        }

    } else {
        $_SESSION['pesan_error'] =
            "Data kelas gagal dihapus karena masih memiliki relasi lain.";
    }

} else {
    $_SESSION['pesan_error'] = "Data penempatan siswa gagal dihapus.";
}

// Kembali ke halaman data kelas
header("Location: data_kelas.php");
exit;

?>