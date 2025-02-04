$(document).ready(function () {
  $("#signup-form").submit(function (event) {
      event.preventDefault(); // Prevent default form submission

      $.ajax({
          url: "api/signup_submit.php", // Path to PHP file
          type: "POST",
          data: $(this).serialize(),
          dataType: "json",
          success: function (response) {
              if (response.status === "success") {
                  showPopup("success", response.message);
              } else {
                  showPopup("error", response.message);
              }
          },
          error: function () {
              showPopup("error", "Something went wrong. Please try again.");
          }
      });
  });
  function showPopup(type, message) {
      let icon = type === "success" ? "✔️" : "❌";
      let bgColor = type === "success" ? "#28a745" : "#dc3545";
      let popup = `
          <div id="custom-popup" style="
              position: fixed;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%);
              background-color: ${bgColor};
              color: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
              text-align: center;
              z-index: 9999;
          ">
              <span style="font-size: 24px;">${icon}</span>
              <p style="margin: 10px 0;">${message}</p>
              <button id="popup-close" style="
                  background-color: white;
                  color: ${bgColor};
                  border: none;
                  padding: 8px 16px;
                  cursor: pointer;
                  border-radius: 4px;
              ">Close</button>
          </div>
      `;
      $("body").append(popup);
      $("#popup-close").click(function () {
          $("#custom-popup").remove();
          if (type === "success") {
              window.location.href = "index.php";
          }
      });
  }
});
