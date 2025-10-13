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

const truncateText = (text, limit = 40) => {
    return text.length > limit ? text.substring(0, limit) + "..." : text;
};

searchBtn.addEventListener("click", async () => {
    const q = searchQuery.value.trim();
    if (!q) return;

    searchResults.innerHTML = "Searching...";
    try {
        const response = await fetch(`search-post?q=${encodeURIComponent(q)}`);
        const result = await response.json();

        if (result.status === "success") {
            searchResults.innerHTML = "";
            result.data.forEach(post => {
                // Create card as a clickable link
                const card = document.createElement("a");
                card.href = `/uoc-sports/public/post/${post.post_id}`;
                card.classList.add("post-card");
                card.style.textDecoration = "none";
                card.style.color = "inherit";
                
                const title = document.createElement("h3");
                title.textContent = post.title;
                
                const desc = document.createElement("p");
                desc.textContent = truncateText(post.description, 180);
                
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