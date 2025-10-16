<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Header subnav</title>
  <style>
    @import url("/uoc-sports/public/css/sports-manager/header.css");
    </style>
</head> 

<body>
    
  <div class="sub-nav">
    <a href="#" class="back-link">
     
       Report
    </a>
    
    <span class="search-container">
    
    <form class="search-form" action="#" method="get">
      <input 
          list="searchHistory"
          type="text" 
          name="search" 
          id="searchInput"
          placeholder="Search by Event Name or Event Code" 
          autocomplete="off"
          required>
         

          <datalist id="searchHistory"> </datalist>
          
    </form>
</span>

    <a href="#" class="report-title">
      <span>Add New +</span>
    </a>
  </div>

<script>
    const events = [
  {name: "Annual Meet", code: "AM01"},
  {name: "Sports Day", code: "SD02"},
  {name: "Cricket Tournament", code: "CT03"}
];

searchInput.addEventListener("keypress", (e) => {
  if(e.key === "Enter") {
    e.preventDefault();
    const term = searchInput.value.trim().toLowerCase();
    const results = events.filter(ev => ev.name.toLowerCase().includes(term) || ev.code.toLowerCase().includes(term));
    console.log(results); // show filtered results
  }
});

const searchInput = document.getElementById("searchInput");
const searchHistory = document.getElementById("searchHistory");

let previousSearches = JSON.parse(localStorage.getItem("eventSearchHistory")) || [];
updateList();

function updateList() {
  searchHistory.innerHTML = "";
  previousSearches.forEach(term => {
    const option = document.createElement("option");
    option.value = term;
    searchHistory.appendChild(option);
  });
}

searchInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    const term = searchInput.value.trim();
    if (!term) return;

    if (!previousSearches.includes(term)) {
      previousSearches.unshift(term);
      if (previousSearches.length > 10) previousSearches.pop();
      localStorage.setItem("eventSearchHistory", JSON.stringify(previousSearches));
      updateList();
    }

    
  }
});
</script>


</body>
</html>