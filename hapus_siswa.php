<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$id = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "DELETE FROM t_siswa WHERE id = '$id'"
);

if ($query) {

    header("Location: data_siswa.php");
    exit;

} else {

    echo "Gagal menghapus data: " . mysqli_error($koneksi);

}

?>