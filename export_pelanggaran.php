<?php

include "middleware/auth.php";
include "config/koneksi.php";

/* Memeriksa hak akses */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

/* Mengambil ID siswa dari URL */
$siswa_id = isset($_GET['siswa_id']) ? (int) $_GET['siswa_id'] : 0;

if ($siswa_id <= 0) {
    exit("ID siswa tidak valid.");
}

/* Mengambil riwayat pelanggaran siswa */
$query = "
    SELECT
        siswa_id,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        tanggal,
        poin,
        status
    FROM t_pelanggaran_siswa
    WHERE siswa_id = ?
    ORDER BY tanggal DESC, id DESC
";

$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    exit("Query gagal disiapkan.");
}

mysqli_stmt_bind_param($stmt, "i", $siswa_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

/* Menentukan nama file */
$nama_file = "laporan_pelanggaran_siswa_" . $siswa_id . ".csv";

/* Header untuk mengunduh file CSV */
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"" . $nama_file . "\"");
header("Pragma: no-cache");
header("Expires: 0");

/* Membuka output CSV */
$output = fopen("php://output", "w");

/* BOM agar karakter Indonesia terbaca dengan baik di Excel */
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

/* Judul laporan */
fputcsv($output, ["LAPORAN PELANGGARAN SISWA"]);
fputcsv($output, ["Tanggal Export", date("d-m-Y H:i:s")]);
fputcsv($output, []);

/* Header tabel */
fputcsv($output, [
    "No",
    "Nama Siswa",
    "Kelas",
    "Pelanggaran",
    "Tanggal",
    "Poin",
    "Status"
]);

$no = 1;
$total_poin = 0;
$nama_siswa = "";
$nama_kelas = "";

/* Mengisi data laporan */
while ($data = mysqli_fetch_assoc($result)) {

    $nama_siswa = $data['nama_siswa'];
    $nama_kelas = $data['nama_kelas'];
    $poin = (int) $data['poin'];

    $total_poin += $poin;

    fputcsv($output, [
        $no++,
        $data['nama_siswa'],
        $data['nama_kelas'],
        $data['nama_pelanggaran'],
        $data['tanggal'],
        $poin,
        $data['status']
    ]);
}

/* Menambahkan total poin */
fputcsv($output, []);
fputcsv($output, [
    "",
    "Nama Siswa",
    $nama_siswa,
    "",
    "",
    "",
    ""
]);

fputcsv($output, [
    "",
    "Kelas",
    $nama_kelas,
    "",
    "",
    "",
    ""
]);

fputcsv($output, [
    "",
    "",
    "",
    "TOTAL POIN",
    "",
    $total_poin,
    ""
]);

/* Menutup proses */
fclose($output);
mysqli_stmt_close($stmt);
exit;

?>