<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
} ?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid d-flex justify-content-center align-items-center"
         style="height: 100vh;">

         <div style="
            background-color: #1532a8;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;">

            <div class="card border-0 shadow-sm"
                 style="width: 420px; border-radius: 8px;">

                <div class="card-body p-4">

            <h4 class="text-center mb-4">
            Login Sistem Informasi Pelanggaran Siswa</h4>

<form action="proses_login.php" method="POST">

    <label class="form-label">Email</label><br>
    <input type="email" name="email" class="form-control" required>

    <br>
    <label>Password</label><br>
    <input type="password" name="password" class="form-control" required>
    <br>

    <div class="d-grid gap-2">
    <button type="submit" class="btn btn-outline-primary">Submit</button>
    </div>
</form>
</div>
</body>
</html>