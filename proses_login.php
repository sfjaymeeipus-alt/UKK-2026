<?php

session_start();

include "config/koneksi.php";

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($koneksi,
    "SELECT * FROM t_users
     WHERE email='$email'
     AND password='$password'"
);

$data = mysqli_fetch_assoc($query);

if ($data) {

    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['nama'] = $data['name'];
    $_SESSION['role'] = $data['role'];

    header("Location: dashboard.php");
    exit;

} else {

    echo "Email atau password salah.";
    echo "<br>";
    echo "<a href='login.php'>Kembali ke login</a>";

}

?>