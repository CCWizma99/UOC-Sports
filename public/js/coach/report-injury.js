// Toggle substitute field
document.getElementById("toggleSubstitute").addEventListener("change", function () {
  const subField = document.getElementById("substituteField");
  subField.style.display = this.checked ? "block" : "none";
});

// Handle form submission
document.getElementById("injuryForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const playerId = document.getElementById("playerId").value;
  const injuryDate = document.getElementById("injuryDate").value;
  const injuryType = document.getElementById("injuryType").value;
  const substituteId = document.getElementById("toggleSubstitute").checked 
                      ? document.getElementById("substituteId").value 
                      : "-";

  // Add new row
  const table = document.querySelector(".injury-table");
  const newRow = document.createElement("div");
  newRow.classList.add("table-row");

  newRow.innerHTML = `
    <div>${playerId}</div>
    <div>${injuryDate}</div>
    <div>${injuryType}</div>
    <div>${substituteId}</div>
  `;

  table.appendChild(newRow);

  // Reset form
  this.reset();
  document.getElementById("substituteField").style.display = "none";
});
