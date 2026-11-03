<?php

include "config/koneksi.php";
include "middleware/auth.php";

// Cek hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Ambil ID guru
if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || (int) $_GET['id'] < 1) {
    header("Location: data_guru.php");
    exit;
}

$id = (int) $_GET['id'];

// Hapus data pelanggaran yang terkait dengan guru
$hapus_pelanggaran = mysqli_query(
    $koneksi,
    "DELETE FROM t_pelanggaran_siswa WHERE guru_id = '$id'"
);

if ($hapus_pelanggaran) {

    // Hapus data wali kelas yang terkait dengan guru
    $hapus_wali = mysqli_query(
        $koneksi,
        "DELETE FROM t_wali_kelas WHERE guru_id = '$id'"
    );

    if ($hapus_wali) {

        // Hapus data guru
        $hapus_guru = mysqli_query(
            $koneksi,
            "DELETE FROM t_guru WHERE id = '$id'"
        );

        if ($hapus_guru) {
            $_SESSION['pesan_sukses'] = "Data guru berhasil dihapus.";
        } else {
            $_SESSION['pesan_error'] =
                "Data guru gagal dihapus karena masih memiliki relasi lain.";
        }

    } else {
        $_SESSION['pesan_error'] = "Data wali kelas gagal dihapus.";
    }

} else {
    $_SESSION['pesan_error'] = "Data pelanggaran gagal dihapus.";
}

// Kembali ke halaman data guru
header("Location: data_guru.php");
exit;

?>