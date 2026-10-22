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

                    <div class="input-group">

                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control rounded-start-3"
                               placeholder="Masukkan password Anda"
                               autocomplete="new-password"
                               required>

                        <button class="btn btn-outline-secondary rounded-end-3"
                                type="button"
                                id="togglePassword"
                                aria-label="Lihat password"
                                title="Lihat password">

                            <!-- Ikon mata -->
                            <svg id="eyeIcon"
                                 xmlns="http://www.w3.org/2000/svg"
                                 width="20"
                                 height="20"
                                 fill="currentColor"
                                 viewBox="0 0 16 16">

                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>

                                <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>

                            </svg>

                        </button>

                    </div>

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

<!-- JAVASCRIPT UNTUK LIHAT PASSWORD -->
<script>
    const password = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");
    const eyeIcon = document.getElementById("eyeIcon");

    togglePassword.addEventListener("click", function () {

        if (password.type === "password") {

            password.type = "text";

            eyeIcon.innerHTML = `
                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.3 7.3 0 0 0-2.79.55l1.23 1.23A4 4 0 0 1 12 8c0 .68-.17 1.32-.47 1.88l1.83 1.36z"/>
                <path d="M11.297 13.176 9.96 11.84A7.7 7.7 0 0 1 8 12.5C3 12.5 0 8 0 8s1.3-2.38 3.53-3.93L1.39 2.53l.71-.71 12 12-.71.71-2.1-1.35z"/>
                <path d="M5.354 6.06A3 3 0 0 0 9.94 10.646L5.354 6.06z"/>
            `;

            togglePassword.setAttribute("aria-label", "Sembunyikan password");
            togglePassword.setAttribute("title", "Sembunyikan password");

        } else {

            password.type = "password";

            eyeIcon.innerHTML = `
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
            `;

            togglePassword.setAttribute("aria-label", "Lihat password");
            togglePassword.setAttribute("title", "Lihat password");

        }

    });
</script>

</body>
</html>