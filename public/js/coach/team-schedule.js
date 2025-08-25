
document.addEventListener("DOMContentLoaded", () => {
  // ====== REMOVE BUTTON FUNCTION ======
  const removeButtons = document.querySelectorAll(".remove-btn");

  removeButtons.forEach(button => {
    button.addEventListener("click", (e) => {
      const row = e.target.closest("tr");
      if (row) {
        row.remove();
      }
    });
  });

  // ====== SEARCH FUNCTION ======
  const searchBars = document.querySelectorAll(".search-bar");

  searchBars.forEach(bar => {
    bar.addEventListener("keyup", () => {
      const searchValue = bar.value.toLowerCase();
      const table = bar.nextElementSibling; // the table right after the search bar
      const rows = table.querySelectorAll("tbody tr");

      rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        if (rowText.includes(searchValue)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  });
});
