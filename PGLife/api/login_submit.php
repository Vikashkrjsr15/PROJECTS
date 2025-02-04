<?php
session_start();
require("../includes/database_connect.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$email = $_POST['email'];
$password = $_POST['password'];
$email = mysqli_real_escape_string($conn, $email);

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    $_SESSION['error_message'] = "Something went wrong!";
    header("location: ../index.php?error=true");
    exit;
}

$row_count = mysqli_num_rows($result);
if ($row_count == 0) {
    $_SESSION['error_message'] = "Login failed! Invalid email or password.";
    header("location: ../index.php?error=true");
    exit;
}

$row = mysqli_fetch_assoc($result);
if (!password_verify($password, $row['password'])) {
    $_SESSION['error_message'] = "Login failed! Invalid email or password.";
    header("location: ../index.php?error=true");
    exit;
}
$_SESSION['user_id'] = $row['id'];
$_SESSION['full_name'] = $row['full_name'];
$_SESSION['email'] = $row['email'];

header("location: ../index.php");
exit;
?>
