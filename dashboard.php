<?php

include "middleware/auth.php";

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>

<body>

<h2>Dashboard Sistem Informasi Pelanggaran Siswa</h2>

<p>Selamat datang, <b><?= $nama; ?></b></p>
<p>Hak Akses: <b><?= $role; ?></b></p>

<hr>

<?php

if ($role == 'admin') {

?>

    <h3>Menu Admin</h3>

    <a href="data_guru.php">Kelola Guru</a><br>
    <a href="data_siswa.php">Kelola Siswa</a><br>
    <a href="data_kelas.php">Kelola Kelas</a><br>
    <a href="tahun_ajaran.php">Kelola Tahun Ajaran</a><br>
    <a href="penempatan_siswa.php">Penempatan Siswa</a><br>
    <a href="data_wali_kelas.php">Kelola Wali Kelas</a><br>
    <a href="kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a><br>
    <a href="jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a><br>

    <h3>Menu Pelanggaran</h3>

    <a href="data_pelanggaran.php">Data Pelanggaran</a><br>
    <a href="tindakan.php">Tindakan</a><br>
    <a href="laporan.php">Laporan</a><br>
    <a href="riwayat.php">Riwayat</a><br>
    <a href="rekap_poin.php">Rekap Poin</a><br>
    <a href="cetak_export.php">Cetak / Export</a><br>

<?php

} else if ($role == 'guru') {

?>

    <h3>Menu Guru</h3>

    <a href="data_pelanggaran.php">Data Pelanggaran</a><br>
    <a href="tindakan.php">Tindakan</a><br>
    <a href="laporan.php">Laporan</a><br>
    <a href="riwayat.php">Riwayat</a><br>
    <a href="rekap_poin.php">Rekap Poin</a><br>
    <a href="cetak_export.php">Cetak / Export</a><br>

<?php

}

?>

<br>

<a href="logout.php">Logout</a>

</body>
</html>