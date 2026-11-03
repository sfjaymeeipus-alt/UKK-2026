<?php
include 'middleware/auth.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: data_tahun_ajaran.php");
    exit;
}

$id = (int) $_GET['id'];

mysqli_begin_transaction($koneksi);

// 1. Hapus data pelanggaran siswa
$hapus_pelanggaran = mysqli_query($koneksi, "
    DELETE FROM t_pelanggaran_siswa
    WHERE tahun_ajaran_id = $id
");

if ($hapus_pelanggaran) {

    // 2. Hapus penempatan kelas siswa
    $hapus_kelas_siswa = mysqli_query($koneksi, "
        DELETE FROM t_kelas_siswa
        WHERE tahun_ajaran_id = $id
    ");

    if ($hapus_kelas_siswa) {

        // 3. Hapus data wali kelas
        $hapus_wali = mysqli_query($koneksi, "
            DELETE FROM t_wali_kelas
            WHERE tahun_ajaran_id = $id
        ");

        if ($hapus_wali) {

            // 4. Hapus tahun ajaran
            $hapus_tahun = mysqli_query($koneksi, "
                DELETE FROM t_tahun_ajaran
                WHERE id = $id
            ");

            if ($hapus_tahun) {
                mysqli_commit($koneksi);
                header("Location: data_tahun_ajaran.php");
                exit;
            }
        }
    }
}

// Batalkan jika masih ada kendala
mysqli_rollback($koneksi);

echo "Tahun ajaran gagal dihapus karena masih terhubung dengan data lain.";
echo "<br><a href='data_tahun_ajaran.php'>Kembali</a>";
?>