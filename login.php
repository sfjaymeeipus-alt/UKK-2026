<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid min-vh-100 d-flex
            justify-content-center align-items-center p-3"
     style="background: linear-gradient(135deg, #a3b1c6, #3b82f6, #93c5fd);">

    <div class="card border-0 shadow-lg rounded-4"
         style="width: 100%; max-width: 430px;">

        <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">

                <div class="bg-primary text-white rounded-circle
                            d-inline-flex justify-content-center
                            align-items-center mb-3"
                     style="width: 75px; height: 75px;">

                    <span class="fw-bold fs-2">S</span>

                </div>

                <h4 class="fw-bold text-dark mb-2">
                    Selamat Datang
                </h4>

                <h6 class="text-primary fw-semibold">
                    Sistem Informasi Pelanggaran Siswa
                </h6>

                <p class="text-secondary small mt-2 mb-0">
                    Silakan masuk menggunakan akun Anda
                </p>

            </div>

            <hr class="text-secondary mb-4">

            <form action="proses_login.php" method="POST">

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           class="form-control form-control-lg rounded-3"
                           placeholder="Masukkan email Anda"
                           autocomplete="off"
                           required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Password
                    </label>

                    <input type="password"
                           name="password"
                           class="form-control form-control-lg rounded-3"
                           placeholder="Masukkan password Anda"
                           autocomplete="current-password"
                           required>
                </div>

                <div class="d-grid">
                    <button type="submit"
                            class="btn btn-primary btn-lg rounded-3 fw-semibold">
                        Masuk ke Sistem
                    </button>
                </div>

            </form>

            <div class="text-center mt-4">
                <p class="text-secondary small mb-0">
                    Sistem Informasi Pelanggaran Siswa
                </p>
            </div>

        </div>

    </div>

</div>

</body>
</html>