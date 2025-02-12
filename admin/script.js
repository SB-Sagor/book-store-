
const bookContainers = document.querySelectorAll(".book-container");
bookContainers.forEach((bookContainer) => {
  let isDown = false;
  let startX;
  let scrollLeft;

  bookContainer.addEventListener("mousedown", (e) => {
    isDown = true;
    bookContainer.classList.add("active");
    startX = e.pageX - bookContainer.offsetLeft;
    scrollLeft = bookContainer.scrollLeft;
  });

  bookContainer.addEventListener("mouseleave", () => {
    isDown = false;
    bookContainer.classList.remove("active");
  });

  bookContainer.addEventListener("mouseup", () => {
    isDown = false;
    bookContainer.classList.remove("active");
  });

  bookContainer.addEventListener("mousemove", (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - bookContainer.offsetLeft;
    const walk = (x - startX) * 2;
    bookContainer.scrollLeft = scrollLeft - walk;
  });
});

document.addEventListener("DOMContentLoaded", () => {
  document.querySelector(".navtext").addEventListener("click", () => {
    navigateTo("index.php");
  });
  document.getElementById("allBooksBtn").addEventListener("click", () => {
    navigateTo("books.php");
  });

  document.getElementById("categoryBtn").addEventListener("click", () => {
    navigateTo("category.php");
  });

  document.getElementById("uploadBtn").addEventListener("click", () => {
    navigateTo("upload.php");
  });

  document.getElementById("requestBtn").addEventListener("click", () => {
    navigateTo("request.php");
  });
 
});
function navigateTo(page) {
  window.location.href = page;
}
