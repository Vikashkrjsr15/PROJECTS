<?php
session_start();
require "includes/database_connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

if (!isset($_POST["property_id"])) {
    echo json_encode(["status" => "error", "message" => "Property ID missing"]);
    exit();
}

$user_id = (int) $_SESSION["user_id"];
$property_id = (int) $_POST["property_id"];

// Check if already interested
$sql_check = "SELECT * FROM interested_users_properties WHERE user_id = ? AND property_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $user_id, $property_id);
$stmt->execute();
$result_check = $stmt->get_result();

if ($result_check->num_rows > 0) {
    // Remove interest
    $sql_delete = "DELETE FROM interested_users_properties WHERE user_id = ? AND property_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("ii", $user_id, $property_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "removed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
} else {
    // Add interest
    $sql_insert = "INSERT INTO interested_users_properties (user_id, property_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ii", $user_id, $property_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "added"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
}
?>
