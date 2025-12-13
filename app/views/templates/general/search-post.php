<section id="search-posts">
  <h2>Search Posts</h2>

  <div class="search-bar">
    <div class="search-input-wrapper">
      <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <path d="m21 21-4.35-4.35"></path>
      </svg>
      <input type="text" id="search-query" placeholder="Type to search posts..." />
      <div class="loading-indicator" id="loading-indicator">
        <div class="spinner-small"></div>
      </div>
    </div>
  </div>

  <div id="search-results"></div>
</section>

<script>
const searchQuery = document.getElementById("search-query");
const searchResults = document.getElementById("search-results");
const loadingIndicator = document.getElementById("loading-indicator");

let debounceTimer;

const truncateText = (text, limit = 40) => {
  return text.length > limit ? text.substring(0, limit) + "..." : text;
};

const performSearch = async (query) => {
  if (!query.trim()) {
    searchResults.innerHTML = "";
    searchResults.classList.remove('show');
    return;
  }

  loadingIndicator.classList.add('active');

  try {
    const response = await fetch(`search-post?q=${encodeURIComponent(query)}`);
    const result = await response.json();

    loadingIndicator.classList.remove('active');

    if (result.status === "success") {
      searchResults.classList.add('show');
      searchResults.innerHTML = "";

      if (result.data.length === 0) {
        searchResults.innerHTML = `
          <div class="no-results">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            <h3>No posts found</h3>
            <p>Try different keywords</p>
          </div>
        `;
        return;
      }

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
    loadingIndicator.classList.remove('active');
    searchResults.classList.add('show');
    searchResults.innerHTML = `<p class="error-msg">Error fetching posts. Please try again.</p>`;
  }
};

// Live search with debouncing
searchQuery.addEventListener("input", (e) => {
  clearTimeout(debounceTimer);
  
  debounceTimer = setTimeout(() => {
    performSearch(e.target.value);
  }, 500); // 500ms delay after user stops typing
});

// Also trigger search on Enter key
searchQuery.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    clearTimeout(debounceTimer);
    performSearch(e.target.value);
  }
});
</script>
