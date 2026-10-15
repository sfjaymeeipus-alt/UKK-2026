<?php
include "middleware/auth.php";
include "config/koneksi.php";

// Hanya admin yang boleh menghapus
if ($_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses.");
}

// Pastikan permintaan menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_kelas.php?pesan=metode_salah");
    exit;
}

// Pastikan ID dikirim
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header("Location: data_kelas.php?pesan=id_tidak_valid");
    exit;
}

$id = (int) $_POST['id'];

if ($id <= 0) {
    header("Location: data_kelas.php?pesan=id_tidak_valid");
    exit;
}

// Periksa keberadaan kelas
$cek = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);

$hasil = mysqli_stmt_get_result($cek);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($cek);

if (!$data) {
    header("Location: data_kelas.php?pesan=data_tidak_ditemukan");
    exit;
}

// Proses hapus kelas
$hapus = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($hapus, "i", $id);

if (mysqli_stmt_execute($hapus)) {

    $jumlah = mysqli_stmt_affected_rows($hapus);
    mysqli_stmt_close($hapus);

    if ($jumlah > 0) {
        header("Location: data_kelas.php?pesan=hapus_berhasil");
        exit;
    }

    header("Location: data_kelas.php?pesan=hapus_gagal");
    exit;

} else {

    $error = mysqli_stmt_error($hapus);
    mysqli_stmt_close($hapus);

    // Error 1451 biasanya berarti data masih direferensikan tabel lain
    if (mysqli_errno($koneksi) == 1451) {
        header("Location: data_kelas.php?pesan=kelas_digunakan");
        exit;
    }

    exit("Gagal menghapus data kelas: " . htmlspecialchars($error));
}
?>