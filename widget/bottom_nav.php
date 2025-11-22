<link rel="stylesheet" href="../style/bottom-nav.css">

<div class="bottom-nav">
  <a href="../index.php">
    <ion-icon name="home-sharp"></ion-icon>
    <div>Home</div>
  </a>

  <a href="../qr_show.php">
    <ion-icon name="qr-code-sharp"></ion-icon>
    <div>QR</div>
  </a>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const current = window.location.pathname.split("/").pop();

  document.querySelectorAll(".bottom-nav a").forEach(a => {
    const target = a.getAttribute("href").split("/").pop();
    if(current === target){
      a.classList.add("active");
    }
  });
});
</script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
