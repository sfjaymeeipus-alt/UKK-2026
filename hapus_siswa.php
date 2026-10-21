<?php

include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: data_siswa.php");
    exit;
}

$id = (int) $_GET['id'];

mysqli_begin_transaction($koneksi);

try {

    // Periksa data siswa
    $cek = mysqli_query(
        $koneksi,
        "SELECT id FROM t_siswa WHERE id = $id"
    );

    if (!$cek) {
        throw new Exception(mysqli_error($koneksi));
    }

    if (mysqli_num_rows($cek) == 0) {
        throw new Exception("Data siswa tidak ditemukan.");
    }

    // 1. Hapus riwayat pelanggaran siswa
    $hapus_pelanggaran = mysqli_query(
        $koneksi,
        "DELETE FROM t_pelanggaran_siswa
         WHERE siswa_id = $id"
    );

    if (!$hapus_pelanggaran) {
        throw new Exception(mysqli_error($koneksi));
    }

    // 2. Hapus penempatan kelas siswa
    $hapus_kelas = mysqli_query(
        $koneksi,
        "DELETE FROM t_kelas_siswa
         WHERE siswa_id = $id"
    );

    if (!$hapus_kelas) {
        throw new Exception(mysqli_error($koneksi));
    }

    // 3. Hapus data siswa
    $hapus_siswa = mysqli_query(
        $koneksi,
        "DELETE FROM t_siswa
         WHERE id = $id"
    );

    if (!$hapus_siswa) {
        throw new Exception(mysqli_error($koneksi));
    }

    // Simpan jika semua berhasil
    mysqli_commit($koneksi);

    header("Location: data_siswa.php?pesan=hapus_berhasil");
    exit;

} catch (Exception $e) {

    mysqli_rollback($koneksi);

    echo "Data siswa gagal dihapus.";
    echo "<br>Detail error: "
        . htmlspecialchars($e->getMessage());
    echo '<br><a href="data_siswa.php">Kembali ke Data Siswa</a>';

}

?>