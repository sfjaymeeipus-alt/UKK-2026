
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

// Validasi ID
if (
    !isset($_GET['id']) ||
    !ctype_digit($_GET['id']) ||
    (int) $_GET['id'] <= 0
) {
    $_SESSION['notifikasi_hapus'] = [
        'tipe' => 'danger',
        'pesan' => 'ID jenis pelanggaran tidak valid!'
    ];

    header("Location: data_jenis_pelanggaran.php");
    exit;
}

$id = (int) $_GET['id'];

// Atur error mysqli
mysqli_report(MYSQLI_REPORT_OFF);

// Proses hapus data
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran WHERE id = ?"
);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['notifikasi_hapus'] = [
                'tipe' => 'success',
                'pesan' => 'Data jenis pelanggaran berhasil dihapus!'
            ];
        } else {
            $_SESSION['notifikasi_hapus'] = [
                'tipe' => 'danger',
                'pesan' => 'Data jenis pelanggaran tidak ditemukan!'
            ];
        }

    } else {

        $_SESSION['notifikasi_hapus'] = [
            'tipe' => 'danger',
            'pesan' => 'Data tidak bisa dihapus karena masih digunakan dalam riwayat pelanggaran siswa!'
        ];
    }

    mysqli_stmt_close($stmt);

} else {

    $_SESSION['notifikasi_hapus'] = [
        'tipe' => 'danger',
        'pesan' => 'Data jenis pelanggaran gagal dihapus!'
    ];
}

header("Location: data_jenis_pelanggaran.php");
exit;
