document.addEventListener("DOMContentLoaded", function () {
  let inactivityTimer = null;
  let lastUpdate = 0;
  const inactivityTimeout = 15 * 30 * 1000; // 15min

  function updateLastActivityOnServer() {
    const now = Date.now();
    if (now - lastUpdate >= 500) {
      lastUpdate = now;
      fetch("./controllers/update_activity.php")
        .then((response) => response.text())
        .then((data) => console.log("Activity updated on server:", data))
        .catch((error) => console.error("Error updating activity:", error));
    }
  }

  // reset the inactivity timer
  function resetInactivityTimer() {
    clearTimeout(inactivityTimer); // Fshi timer ekzistues
    inactivityTimer = setTimeout(() => {
      // Call logout.php after timeout
      window.location.href = "./logout.php";
    }, inactivityTimeout);
    updateLastActivityOnServer();
  }

  fetch("./controllers/check_inactivity.php")
    .then((response) => response.text())
    .then((data) => {
      if (data === "active") {
        console.log("User is logged in. Starting inactivity timer.");
        document.addEventListener("keydown", resetInactivityTimer);
        document.addEventListener("click", resetInactivityTimer);
        document.addEventListener("mousemove", resetInactivityTimer);
        window.addEventListener("wheel", resetInactivityTimer);
        document.addEventListener("touchstart", resetInactivityTimer); // Touchpad initial touch
        document.addEventListener("touchmove", resetInactivityTimer); // Touchpad movement
        document.addEventListener("pointermove", resetInactivityTimer); // General pointer movement
        resetInactivityTimer();
      } else {
        console.log("User is not logged in. No inactivity tracking applied.");
      }
    })
    .catch((error) =>
      console.error("Error initializing activity tracking:", error)
    );
});
