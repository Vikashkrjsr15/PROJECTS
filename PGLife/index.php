<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome | PG Life</title>

    <?php include "includes/head_links.php"; ?>
    <link href="css/home.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include "includes/header.php"; ?>

    <div class="banner-container">
        <h2 class="white pb-3">Happiness per Square Foot</h2>
        <form id="search-form" action="property_list.php" method="GET">
            <div class="input-group city-search">
                <input type="text" class="form-control input-city" id="city" name="city" placeholder="Enter your city to search for PGs" />
                <div class="input-group-append">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="page-container">
        <h1 class="city-heading">Major Cities</h1>
        <div class="row">
            <div class="city-card-container col-md">
                <a href="property_list.php?city=Delhi">
                    <div class="city-card rounded-circle">
                        <img src="img/delhi.png" class="city-img" />
                    </div>
                </a>
            </div>
            <div class="city-card-container col-md">
                <a href="property_list.php?city=Mumbai">
                    <div class="city-card rounded-circle">
                        <img src="img/mumbai.png" class="city-img" />
                    </div>
                </a>
            </div>
            <div class="city-card-container col-md">
                <a href="property_list.php?city=Bengaluru">
                    <div class="city-card rounded-circle">
                        <img src="img/bangalore.png" class="city-img" />
                    </div>
                </a>
            </div>
            <div class="city-card-container col-md">
                <a href="property_list.php?city=Hyderabad">
                    <div class="city-card rounded-circle">
                        <img src="img/hyderabad.png" class="city-img" />
                    </div>
                </a>
            </div>
        </div>
    </div>

    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>

     <!-- Registration Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-headers">
                <h5 class="modal-titles" id="messageModalLabel"></h5>
            </div>
            <div class="modal-body text-center">
                <p id="messageText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModalBtn">Close</button>
            </div>
        </div>
    </div>
</div>


    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content ">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="errorModalLabel"><i class="fas fa-exclamation-circle"></i> Login Failed</h5>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"><?php echo isset($_SESSION['error_message']) ? $_SESSION['error_message'] : ''; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="redirectHome()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
    $("#signup-form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "api/signup_submit.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                $("#messageModal").modal("show");
                $("#messageText").text(response.message);

                if (response.status === "error") {
                    // $(".modal-headers").css("background-color", "#dc3545");
                    $(".modal-titles").html('<i class="fas fa-exclamation-circle"></i> Registration Failed');
                } else if (response.status === "success") {
                    // $(".modal-headers").css("background-color", "#28a745");
                    $(".modal-titles").html('<i class="fas fa-check-circle"></i> Registration Successful');
                }
            }
        });
    });

    $("#closeModalBtn").click(function () {
        $("#messageModal").modal("hide");
        if ($("#messageModal .modal-titles").text().includes("Registration Successful")) {
            window.location.href = "index.php";
        }
    });
});


        function redirectHome() {
            window.location.href = "index.php";
        }
        $(document).ready(function() {
            let errorMessage = "<?php echo isset($_SESSION['error_message']) ? $_SESSION['error_message'] : ''; ?>";
            if (errorMessage.trim() !== "") {
                $('#errorModal').modal('show');
            }
        });
        <?php unset($_SESSION['error_message']); ?>
    </script>

</body>
</html>
