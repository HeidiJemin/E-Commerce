document.addEventListener("DOMContentLoaded", () => {
  const favButtons = document.querySelectorAll(".favourite-btn");

  favButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const produktId = button.getAttribute("data-produkt-id");

      // Add or Remove
      const action = button.classList.contains("favourited") ? "remove" : "add";

      // AJAX request
      fetch("./controllers/favourites_handler.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          produkt_id: produktId,
          action: action,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Toggle
            button.classList.toggle("favourited");
            if (button.classList.contains("favourited")) {
              button.textContent = "Remove from Favourites";
            } else {
              button.textContent = "Add to Favourites";
            }
          } else {
            alert(data.message || "An error occurred.");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    });
  });
});
