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

    // Menghapus data guru berdasarkan ID
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
        mysqli_stmt_close($query);
        echo "Gagal menghapus data guru.";
        exit;
    }

} else {
    echo "ID guru tidak ditemukan.";
    exit;
}

?>