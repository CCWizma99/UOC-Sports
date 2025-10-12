<section id="search-posts">
  <h2>Search Posts</h2>
  <input type="text" id="search-query" placeholder="Search posts..." />
  <button id="search-btn">Search</button>

  <div id="search-results"></div>
</section>

<script>
const searchBtn = document.getElementById("search-btn");
const searchQuery = document.getElementById("search-query");
const searchResults = document.getElementById("search-results");

searchBtn.addEventListener("click", async () => {
    const q = searchQuery.value.trim();
    if (!q) return;

    searchResults.innerHTML = "Searching...";
    try {
        const response = await fetch(`admin-post/search?q=${encodeURIComponent(q)}`);
        const result = await response.json();

        if (result.status === "success") {
            searchResults.innerHTML = "";
            result.data.forEach(post => {
                const card = document.createElement("div");
                card.classList.add("post-card");
                
                const title = document.createElement("h3");
                title.textContent = post.title;
                
                const desc = document.createElement("p");
                desc.textContent = post.description;
                
                const imagesDiv = document.createElement("div");
                imagesDiv.classList.add("post-images");
                post.images.forEach(imgPath => {
                    const img = document.createElement("img");
                    img.src = imgPath;
                    imagesDiv.appendChild(img);
                });

                card.appendChild(title);
                card.appendChild(desc);
                card.appendChild(imagesDiv);

                searchResults.appendChild(card);
            });
        } else {
            searchResults.innerHTML = `<p style="color:red">${result.message}</p>`;
        }
    } catch (err) {
        searchResults.innerHTML = `<p style="color:red">Error fetching posts.</p>`;
    }
});

</script>