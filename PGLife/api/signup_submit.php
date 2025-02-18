<?php
header("Content-Type: application/json");
require("../includes/database_connect.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

// Validate required fields
$required_fields = ['full_name', 'email', 'password', 'phone', 'college_name', 'gender'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(["status" => "error", "message" => ucfirst($field) . " is required"]);
        exit;
    }
}

// Get the sanitized input values
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$phone = trim($_POST['phone']);
$college_name = trim($_POST['college_name']);
$gender = trim($_POST['gender']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit;
}

// Validate phone number (10 digits)
if (!preg_match("/^\d{10}$/", $phone)) {
    echo json_encode(["status" => "error", "message" => "Invalid phone number"]);
    exit;
}

// Secure password hashing
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if email already exists
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

mysqli_stmt_close($stmt);

// Insert new user into database
$sql = "INSERT INTO users (email, password, full_name, phone, gender, college_name) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssss", $email, $hashed_password, $full_name, $phone, $gender, $college_name);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
?>
