<?php

include "middleware/auth.php";
include "config/koneksi.php";

// Memastikan hanya admin yang bisa menghapus data
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Memeriksa apakah ID tersedia
if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    // Memastikan ID valid
    if ($id <= 0) {
        echo "ID guru tidak valid";
        exit;
    }

    // Memeriksa apakah guru digunakan dalam data pelanggaran
    $cek = mysqli_prepare(
        $koneksi,
        "SELECT COUNT(*) AS total
         FROM t_pelanggaran_siswa
         WHERE guru_id = ?"
    );

    mysqli_stmt_bind_param($cek, "i", $id);
    mysqli_stmt_execute($cek);

    $hasil = mysqli_stmt_get_result($cek);
    $data = mysqli_fetch_assoc($hasil);

    mysqli_stmt_close($cek);

    // Jika guru masih memiliki riwayat pelanggaran
    if ($data['total'] > 0) {
        echo "Data guru tidak dapat dihapus karena masih digunakan dalam riwayat pelanggaran siswa.";
        echo "<br>Jumlah data terkait: " . (int) $data['total'];
        echo "<br><a href='data_guru.php'>Kembali ke Data Guru</a>";
        exit;
    }

    // Menghapus data guru dari database
    $query = mysqli_prepare(
        $koneksi,
        "DELETE FROM t_guru WHERE id = ?"
    );

    mysqli_stmt_bind_param($query, "i", $id);

    if (mysqli_stmt_execute($query)) {

        if (mysqli_stmt_affected_rows($query) > 0) {
            mysqli_stmt_close($query);

            header("Location: data_guru.php?pesan=hapus_berhasil");
            exit;
        } else {
            mysqli_stmt_close($query);
            echo "Data guru tidak ditemukan.";
            exit;
        }

    } else {
        $error = mysqli_stmt_error($query);
        mysqli_stmt_close($query);

        echo "Gagal menghapus data guru.";
        echo "<br>Detail error: " . htmlspecialchars($error);
        exit;
    }

} else {
    echo "ID guru tidak ditemukan.";
    exit;
}

?>