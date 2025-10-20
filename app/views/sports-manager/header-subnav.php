<nav class="sub-nav">
  <ul>
    <li><a href="#">Report</a></li>
    <li><a href="#">Transaction +</a></li>
    <li><a href="#">Events</a></li>
    <li><a href="#">Messages</a></li>
  </ul>

  <form class="search-form" action="#" method="get">
    <input 
      list="searchHistory"
      type="text"
      name="search"
      id="searchInput"
      placeholder="Search by Event Name or Event Code"
      autocomplete="off"
      required
    >
    <datalist id="searchHistory"></datalist>
  </form>
</nav>

<script>
const events = [
  { name: "Annual Meet", code: "AM01" },
  { name: "Sports Day", code: "SD02" },
  { name: "Cricket Tournament", code: "CT03" }
];

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

    // Save search term
    if (!previousSearches.includes(term)) {
      previousSearches.unshift(term);
      if (previousSearches.length > 10) previousSearches.pop();
      localStorage.setItem("eventSearchHistory", JSON.stringify(previousSearches));
      updateList();
    }

    // Filter results
    const results = events.filter(ev =>
      ev.name.toLowerCase().includes(term.toLowerCase()) ||
      ev.code.toLowerCase().includes(term.toLowerCase())
    );
    console.log(results);
  }
});
</script>
