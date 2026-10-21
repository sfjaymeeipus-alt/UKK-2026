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

<div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center p-3"
     style="background: linear-gradient(135deg, #a3b1c6, #3b82f6, #93c5fd);">

    <div class="card border-0 shadow-lg rounded-4"
         style="width: 100%; max-width: 400px;">

        <div class="card-body p-4">

            <!-- LOGO DAN IDENTITAS SEKOLAH -->
            <div class="text-center mb-3">

                <img src="logo-smkmtsm.png"
                     alt="Logo SMK Muhammadiyah Tasikmalaya"
                     class="rounded-circle shadow mb-3 border border-3 border-white"
                     width="100"
                     height="100"
                     style="object-fit: contain; background-color: white; padding: 5px;">

                <h5 class="fw-bold text-dark mb-1">
                    Selamat Datang
                </h5>

                <h6 class="text-primary fw-semibold mb-1">
                    Sistem Informasi Pelanggaran Siswa
                </h6>

                <p class="text-dark fw-semibold small mb-1">
                    SMK Muhammadiyah Tasikmalaya
                </p>

                <p class="text-secondary small mb-0">
                    Silakan masuk menggunakan akun Anda
                </p>

            </div>

            <hr class="text-secondary mb-3">

            <!-- FORM LOGIN -->
            <form action="proses_login.php"
                  method="POST"
                  autocomplete="off">

                <div class="mb-3">

                    <label for="email" class="form-label fw-semibold small">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control rounded-3"
                           placeholder="Masukkan email Anda"
                           autocomplete="off"
                           autocapitalize="none"
                           spellcheck="false"
                           required>

                </div>

                <div class="mb-3">

                    <label for="password" class="form-label fw-semibold small">
                        Password
                    </label>

                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control rounded-3"
                           placeholder="Masukkan password Anda"
                           autocomplete="new-password"
                           required>

                </div>

                <div class="d-grid">

                    <button type="submit"
                            class="btn btn-primary rounded-3 fw-semibold">
                        Masuk ke Sistem
                    </button>

                </div>

            </form>

            <!-- FOOTER -->
            <div class="text-center mt-3">

                <p class="text-secondary small mb-0">
                    &copy; 2026 SMK Muhammadiyah Tasikmalaya
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>