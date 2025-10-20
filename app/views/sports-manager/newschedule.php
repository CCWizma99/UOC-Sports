<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add a team member to the team</title>
  <link rel="stylesheet" type="text/css" href="form.css">
 
  
</head> 
<div class ="header">
   <?php require 'header-nav.php'; 

  ?>
  <div class="header-subnav">
     
       <a href="inbox.php" class="back">Back</a>
      <div class="nav-right">
        <a href="sent.php" class="member">Scheduled </br>Events/Practices</a>
      <a href="drafts.php" class="new">Schedule New +</a>
</div>
</div>
<body>


<form class="form" action="" id ="form" method="post">
    <h2>Schedule a New Event/ Practice</h2>
    <label>Event/Practice</label>
    <input type="text" name="event" placeholder="Add Name of the Event/Practice" required>

    <label>Selected Sport</label>
    <select name="sport" required>
  
  <option value="cricket">Cricket</option>
  <option value="football">Football</option>
  <option value="badminton">Badminton</option>
</select>

    <label>Select Participants </label>

      <div class="dropdown">
        <input type="text" id="searchBox" class="search-box" placeholder="Search participants...">
        <div id="optionsContainer" class="options"></div>
      </div>

      <p id="message"></p>

    <script>
    // Dummy data for participants
    const participants = [
      { id: "P001", name: "M Silva" },
      { id: "P002", name: "S S P Jayaweera" },
      { id: "P003", name: "P R Jayarathna" },
      { id: "P004", name: "L K Perera" },
      { id: "S005", name: "O Perera" },
      { id: "S006", name: "R S Fernando" },
      { id: "P007", name: "H K De Silva" },
      { id: "D008", name: "A B Wickrama" },
      { id: "P009", name: "K J Ranasinghe" },
      { id: "A010", name: "D P Ekanayake" }
    ];

    const searchBox = document.getElementById("searchBox");
    const optionsContainer = document.getElementById("optionsContainer");
    const message = document.getElementById("message");
    const form = document.getElementById("form");

    let selected = [];

    // Render participants (filtered or all)
    function renderOptions(filter = "") {
      optionsContainer.innerHTML = "";
      const filtered = participants.filter(p =>
        (p.id + " - " + p.name).toLowerCase().includes(filter.toLowerCase())
      );

      filtered.forEach(p => {
        const div = document.createElement("div");
        div.className = "option" + (selected.includes(p.id) ? " selected" : "");
        div.textContent = `${p.id} - ${p.name}`;
        div.onclick = () => toggleSelect(p.id);
        optionsContainer.appendChild(div);
      });

      if (filtered.length === 0) {
        const noResult = document.createElement("div");
        noResult.textContent = "No matches found.";
        noResult.style.color = "#777";
        noResult.style.textAlign = "center";
        optionsContainer.appendChild(noResult);
      }
    }

    // Toggle selection
    function toggleSelect(id) {
      if (selected.includes(id)) {
        selected = selected.filter(s => s !== id);
        message.textContent = "";
      } else {
        if (selected.length >= 2) {
          message.textContent = "You can select only 2 participants.";
          return;
        }
        selected.push(id);
      }
      renderOptions(searchBox.value);
    }

    // Filter as user types
    searchBox.addEventListener("input", () => {
      renderOptions(searchBox.value);
    });

    // Toggle dropdown open/close when clicking input
dropdownInput.addEventListener("click", () => {
  isOpen = !isOpen;
  optionsList.style.display = isOpen ? "block" : "none";
  if (isOpen) renderOptions();
});

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
  if (!e.target.closest(".dropdown-container")) {
    optionsList.style.display = "none";
    isOpen = false;
  }
});
  
function updateSelectedList() {
  if (selected.length === 0) {
    selectedList.textContent = "No participants selected.";
  } else {
    const names = selected.map(id => {
      const p = participants.find(x => x.id === id);
      return p ? `${p.id} (${p.name})` : id;
    });
    selectedList.textContent = "Selected: " + names.join(", ");
  }

  // show selections inside the input field
  dropdownInput.value = selected.length ? selected.join(", ") : "";
}


    // Initial load
    renderOptions();
  </script>


    <label>Scheduled Date</label>
    <input type="date" name="date" placeholder="Select Date" required>



    <div class="buttons">
      <button type="reset" class="reset-btn">Reset</button>
      <button type="submit" class="submit-btn">Submit</button>
    </div>

</form>
 <?php require "../app/views/templates/equipment-manager/footer.php"; ?>

</body>
</html>