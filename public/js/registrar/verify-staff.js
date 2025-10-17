function updateStatus(button, approved) {
  const row = button.closest(".table-row");
  const status = row.querySelector(".status");

  if (approved) {
    status.textContent = "Approved";
    status.className = "status approved";
  } else {
    status.textContent = "Rejected";
    status.className = "status rejected";
  }
}

function filterStaff() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const rows = document.querySelectorAll(".table-row");

  rows.forEach(row => {
    const name = row.children[0].textContent.toLowerCase();
    const department = row.children[1].textContent.toLowerCase();
    if (name.includes(input) || department.includes(input)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}
