<section id="search-posts">
  <h2>Search Posts</h2>

  <div class="search-bar">
    <input type="text" id="search-query" placeholder="Search posts..." />
    <button id="search-btn">Search</button>
  </div>

  <div id="search-results"></div>
</section>

<script>
const searchBtn = document.getElementById("search-btn");
const searchQuery = document.getElementById("search-query");
const searchResults = document.getElementById("search-results");

const truncateText = (text, limit = 40) => {
  return text.length > limit ? text.substring(0, limit) + "..." : text;
};

searchBtn.addEventListener("click", async () => {
  const q = searchQuery.value.trim();
  if (!q) return;

  searchResults.innerHTML = "<p>Searching...</p>";

  try {
    const response = await fetch(`search-post?q=${encodeURIComponent(q)}`);
    const result = await response.json();

    if (result.status === "success") {
      searchResults.classList.add('show');
      searchResults.innerHTML = "";

      result.data.forEach(post => {
        const card = document.createElement("a");
        card.href = `/uoc-sports/public/post/${post.post_id}`;
        card.classList.add("post-card");

        card.innerHTML = `
          <h3>${post.title}</h3>
          <p>${truncateText(post.description, 180)}</p>
          <div class="post-images">
            ${post.images.map(img => `<img src="${img}">`).join("")}
          </div>
        `;

        searchResults.appendChild(card);
      });
    } else {
        searchResults.classList.add('show');
      searchResults.innerHTML = `<p class="error-msg">${result.message}</p>`;
    }
  } catch (err) {
    searchResults.classList.add('show');
    searchResults.innerHTML = `<p class="error-msg">Error fetching posts.</p>`;
  }
});
</script>
