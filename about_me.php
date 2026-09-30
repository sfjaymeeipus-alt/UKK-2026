<?php

include "middleware/auth.php";

$role = $_SESSION['role'];
$nama = "Puspita Dewi";

?>

<!DOCTYPE html>
<html>

<head>

    <title>About Me</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body style="background-color: #eef7fa;">

<div class="container py-5">

    <!-- JUDUL -->
    <div class="text-center mb-4">

        <h2 class="fw-bold" style="color: #087fa3;">
            About Me
        </h2>

        <p class="text-muted">
            Informasi pengguna dan project
        </p>

    </div>


    <!-- CARD UTAMA -->
    <div class="card border-0 shadow mx-auto"
         style="max-width: 650px; border-radius: 20px; overflow: hidden;">

        <!-- HEADER BIRU -->
        <div class="text-center text-white p-4"
             style="background-color: #087fa3;">

            <!-- LINGKARAN NAMA -->
            <div class="mx-auto mb-3 d-flex justify-content-center align-items-center"
                 style="
                    width: 90px;
                    height: 90px;
                    background-color: white;
                    color: #087fa3;
                    border-radius: 50%;
                    font-size: 32px;
                    font-weight: bold;
                 ">

                P

            </div>

            <h3 class="fw-bold mb-1">
                Puspita Dewi
            </h3>

            <p class="mb-0">
                <?= ucfirst($role); ?>
            </p>

        </div>


        <!-- ISI CARD -->
        <div class="card-body p-4">

            <h5 class="fw-bold mb-4"
                style="color: #087fa3;">
                Informasi Saya
            </h5>


            <!-- NAMA -->
            <div class="d-flex align-items-center mb-3 p-3 rounded"
                 style="background-color: #f4fafc;">

                <div class="me-3"
                     style="font-size: 25px;">
                    👤
                </div>

                <div>
                    <small class="text-muted">
                        Nama
                    </small>

                    <div class="fw-bold">
                        Puspita Dewi
                    </div>
                </div>

            </div>


            <!-- KELAS -->
            <div class="d-flex align-items-center mb-3 p-3 rounded"
                 style="background-color: #f4fafc;">

                <div class="me-3"
                     style="font-size: 25px;">
                    🎓
                </div>

                <div>
                    <small class="text-muted">
                        Kelas
                    </small>

                    <div class="fw-bold">
                        XII RPL 2
                    </div>
                </div>

            </div>


            <!-- SEKOLAH -->
            <div class="d-flex align-items-center mb-3 p-3 rounded"
                 style="background-color: #f4fafc;">

                <div class="me-3"
                     style="font-size: 25px;">
                    🏫
                </div>

                <div>
                    <small class="text-muted">
                        Sekolah
                    </small>

                    <div class="fw-bold">
                        SMK Muhammadiyah Tasikmalaya
                    </div>
                </div>

            </div>


            <!-- PROJECT -->
            <div class="d-flex align-items-center mb-3 p-3 rounded"
                 style="background-color: #f4fafc;">

                <div class="me-3"
                     style="font-size: 25px;">
                    💻
                </div>

                <div>
                    <small class="text-muted">
                        Project
                    </small>

                    <div class="fw-bold">
                        Sistem Informasi Pelanggaran Siswa
                    </div>
                </div>

            </div>


            <!-- TEKNOLOGI -->
            <div class="d-flex align-items-center mb-4 p-3 rounded"
                 style="background-color: #f4fafc;">

                <div class="me-3"
                     style="font-size: 25px;">
                    ⚙️
                </div>

                <div>
                    <small class="text-muted">
                        Teknologi
                    </small>

                    <div class="fw-bold">
                        PHP Native, MySQL, HTML, CSS, JavaScript
                    </div>
                </div>

            </div>


            <!-- TOMBOL -->
            <div class="text-center">

                <a href="dashboard.php"
                   class="btn text-white px-4"
                   style="background-color: #087fa3;
                          border-radius: 10px;">

                    ← Kembali ke Dashboard

                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>