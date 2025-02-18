<?php
session_start();
require "includes/database_connect.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$city_name = $_GET["city"] ?? "";

// Get city details
$sql_1 = "SELECT * FROM cities WHERE name = ?";
$stmt = $conn->prepare($sql_1);
$stmt->bind_param("s", $city_name);
$stmt->execute();
$result_1 = $stmt->get_result();
$city = $result_1->fetch_assoc();

if (!$city) {
    die("Sorry! We do not have any PG listed in this city.");
}
$city_id = $city['id'];

// Get properties in the city
$sql_2 = "SELECT * FROM properties WHERE city_id = ?";
$stmt = $conn->prepare($sql_2);
$stmt->bind_param("i", $city_id);
$stmt->execute();
$result_2 = $stmt->get_result();
$properties = $result_2->fetch_all(MYSQLI_ASSOC);

// Get interested properties for this city
$sql_3 = "SELECT iup.user_id, iup.property_id FROM interested_users_properties iup
          INNER JOIN properties p ON iup.property_id = p.id
          WHERE p.city_id = ?";
$stmt = $conn->prepare($sql_3);
$stmt->bind_param("i", $city_id);
$stmt->execute();
$result_3 = $stmt->get_result();
$interested_users_properties = $result_3->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PGs in <?= htmlspecialchars($city_name) ?> | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <link href="css/property_list.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($city_name); ?></li>
        </ol>
    </nav>

    <div class="page-container">
        <h1>PGs in <?= htmlspecialchars($city_name); ?></h1>

        <?php if (!empty($properties)) { 
            foreach ($properties as $property) {
                $property_images = glob("img/properties/" . $property['id'] . "/*");

                $is_interested = false;
                $interested_users_count = 0;
                foreach ($interested_users_properties as $interested) {
                    if ($interested['property_id'] == $property['id']) {
                        $interested_users_count++;
                        if ($interested['user_id'] == $user_id) {
                            $is_interested = true;
                        }
                    }
                }
        ?>
            <div class="property-card row property-id-<?= $property['id'] ?>">
                <div class="image-container col-md-4">
                    <img src="<?= htmlspecialchars($property_images[0]) ?>" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <div class="star-container" title="<?= $property['rating_clean'] ?>">
                            <?php for ($i = 0; $i < 5; $i++) {
                                echo ($property['rating_clean'] >= $i + 0.8) ? '<i class="fas fa-star"></i>' :
                                     (($property['rating_clean'] >= $i + 0.3) ? '<i class="fas fa-star-half-alt"></i>' :
                                     '<i class="far fa-star"></i>');
                            } ?>
                        </div>
                        <div class="interested-container">
                            <i class="<?= $is_interested ? 'fas' : 'far' ?> fa-heart" property_id="<?= $property['id'] ?>"></i>
                            <div class="interested-text"><?= $interested_users_count ?> interested</div>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?= htmlspecialchars($property['name']) ?></div>
                        <div class="property-address"><?= htmlspecialchars($property['address']) ?></div>
                    </div>
                    <div class="row no-gutters">
                        <div class="rent-container col-6">
                            <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_details.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } 
        } else { ?>
            <div class="no-property-container"><p>No PGs found.</p></div>
        <?php } ?>
    </div>

    <?php include "includes/footer.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $(document).on("click", ".interested-container i", function () {
                let heartIcon = $(this);
                let propertyId = heartIcon.attr("property_id");

                if (!propertyId) {
                    console.log("Property ID missing in HTML!");
                    return;
                }

                $.ajax({
                    url: "update_interest.php",
                    type: "POST",
                    data: { property_id: propertyId },
                    dataType: "json",
                    success: function (response) {
                        console.log("Server Response:", response);

                        if (response.status === "error") {
                            alert(response.message);
                            if (response.message === "User not logged in") {
                                window.location.href = "login.php";
                            }
                        } else if (response.status === "added") {
                            heartIcon.removeClass("far fa-heart").addClass("fas fa-heart");
                            let countElement = heartIcon.siblings(".interested-text");
                            countElement.text((parseInt(countElement.text()) + 1) + " interested");
                        } else if (response.status === "removed") {
                            heartIcon.removeClass("fas fa-heart").addClass("far fa-heart");
                            let countElement = heartIcon.siblings(".interested-text");
                            countElement.text((parseInt(countElement.text()) - 1) + " interested");
                        }
                    },
                    error: function (xhr) {
                        console.log("AJAX Error:", xhr.responseText);
                        alert("Something went wrong! Check console for details.");
                    }
                });
            });
        });
    </script>
</body>
</html>
