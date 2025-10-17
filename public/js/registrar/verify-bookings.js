function updateStatus(button, newStatus) {
  const row = button.closest(".table-row");
  const statusCell = row.querySelector(".status");

  statusCell.textContent = newStatus;
  statusCell.className = "status " + newStatus.toLowerCase();
}
