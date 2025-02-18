<?php
session_start();
require "includes/database_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get user details
$sql_1 = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_1);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_1 = $stmt->get_result();

if (!$result_1 || $result_1->num_rows == 0) {
    die("Something went wrong! " . mysqli_error($conn));
}
$user = $result_1->fetch_assoc();

// Get interested properties
$sql_2 = "SELECT p.* FROM interested_users_properties iup 
          INNER JOIN properties p ON iup.property_id = p.id
          WHERE iup.user_id = ?";
$stmt = $conn->prepare($sql_2);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_2 = $stmt->get_result();
$interested_properties = $result_2->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <link href="css/dashboard.css" rel="stylesheet" />
</head>

<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="my-profile page-container">
        <h1>My Profile</h1>
        <div class="row">
            <div class="col-md-3 profile-img-container">
                <i class="fas fa-user profile-img"></i>
            </div>
            <div class="col-md-9">
                <div class="row no-gutters justify-content-between align-items-end">
                    <div class="profile">
                        <div class="name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="email"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="phone"><?= htmlspecialchars($user['phone']) ?></div>
                        <div class="college"><?= htmlspecialchars($user['college_name']) ?></div>
                    </div>
                    <div class="edit">
                        <div class="edit-profile">Edit Profile</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($interested_properties)) { ?>
        <div class="my-interested-properties">
            <div class="page-container">
                <h1>My Interested Properties</h1>
                <?php foreach ($interested_properties as $property) { 
                    $property_images = glob("img/properties/" . $property['id'] . "/*"); ?>
                    <div class="property-card property-id-<?= $property['id'] ?> row">
                        <div class="image-container col-md-4">
                            <img src="<?= htmlspecialchars($property_images[0]) ?>" />
                        </div>
                        <div class="content-container col-md-8">
                            <div class="row no-gutters justify-content-between">
                                <?php 
                                $total_rating = ($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3;
                                $total_rating = round($total_rating, 1);
                                ?>
                                <div class="star-container" title="<?= $total_rating ?>">
                                    <?php for ($i = 0; $i < 5; $i++) {
                                        if ($total_rating >= $i + 0.8) {
                                            echo '<i class="fas fa-star"></i>';
                                        } elseif ($total_rating >= $i + 0.3) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    } ?>
                                </div>
                                <div class="interested-container">
                                    <i class="is-interested-image fas fa-heart" property_id="<?= $property['id'] ?>"></i>
                                </div>
                            </div>
                            <div class="detail-container">
                                <div class="property-name"><?= htmlspecialchars($property['name']) ?></div>
                                <div class="property-address"><?= htmlspecialchars($property['address']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php include "includes/footer.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".interested-container i").click(function () {
                let heartIcon = $(this);
                let propertyId = heartIcon.attr("property_id");

                $.ajax({
                    url: "update_interest.php",
                    type: "POST",
                    data: { property_id: propertyId },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "error") {
                            alert(response.message);
                            if (response.message === "User not logged in") {
                                window.location.href = "login.php";
                            }
                        } else if (response.status === "added") {
                            heartIcon.removeClass("far fa-heart").addClass("fas fa-heart");
                        } else if (response.status === "removed") {
                            heartIcon.removeClass("fas fa-heart").addClass("far fa-heart");
                            $(".property-id-" + propertyId).remove();
                        }
                    },
                    error: function (xhr) {
                        console.log("AJAX Error:", xhr.responseText);
                        alert("Something went wrong! Check the console.");
                    }
                });
            });
        });
    </script>
</body>

</html>
