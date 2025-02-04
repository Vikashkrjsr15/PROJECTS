<?php
header("Content-Type: application/json");
require("../includes/database_connect.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

// Validate required fields
$required_fields = ['full_name', 'phone', 'email', 'password', 'college_name', 'gender'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(["status" => "error", "message" => ucfirst($field) . " is required"]);
        exit;
    }
}

$full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
$email = mysqli_real_escape_string($conn, trim($_POST['email']));
$college_name = mysqli_real_escape_string($conn, trim($_POST['college_name']));
$gender = mysqli_real_escape_string($conn, trim($_POST['gender']));
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

// Check if email already exists using a prepared statement
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(["status" => "error", "message" => "Email is already registered!"]);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_close($stmt); // Close statement

// Insert new user using a prepared statement
$sql = "INSERT INTO users (email, password, full_name, phone, gender, college_name) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssss", $email, $password, $full_name, $phone, $gender, $college_name);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
}

// Close database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
