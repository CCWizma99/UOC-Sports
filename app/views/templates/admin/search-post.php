<section id="search-posts">
  <h2>Search Posts</h2>
  <input type="text" id="search-query" placeholder="Search posts..." />
  <button id="search-btn">Search</button>

  <div id="search-results"></div>
</section>

<!-- Modal for Update -->
<div id="update-modal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <h2>Update Post</h2>

    <form id="update-post-form">
      <div class="form-group">
        <label for="post-id">Post ID</label>
        <input type="text" id="post-id" name="post_id" readonly />
      </div>

      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required />
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" required></textarea>
      </div>

      <div class="form-group toggle-group">
        <label for="commenting">Allow Commenting</label>
        <label class="switch">
          <input type="checkbox" id="commenting" name="commenting" />
          <span class="slider"></span>
        </label>
      </div>

      <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status" required>
          <option value="active">Active</option>
          <option value="removed">Removed</option>
        </select>
      </div>

      <div class="form-group">
        <label for="date-posted">Date Posted</label>
        <input type="text" id="date-posted" name="date_posted" readonly />
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-save">Save Changes</button>
        <button type="button" class="btn btn-cancel close-btn">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
const searchBtn = document.getElementById("search-btn");
const searchQuery = document.getElementById("search-query");
const searchResults = document.getElementById("search-results");
const modal = document.getElementById("update-modal");
const closeBtns = document.querySelectorAll(".close-btn");

const postIdField = document.getElementById("post-id");
const titleField = document.getElementById("title");
const descField = document.getElementById("description");
const commentingField = document.getElementById("commenting");
const statusField = document.getElementById("status");
const dateField = document.getElementById("date-posted");

// Fetch and render posts
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

        const btnGroup = document.createElement("div");
        btnGroup.classList.add("button-group");

        const viewBtn = document.createElement("button");
        viewBtn.textContent = "View";
        viewBtn.classList.add("btn", "btn-view");
        viewBtn.addEventListener("click", () => {
          window.location.href = `post/${post.post_id}`;
        });

        const updateBtn = document.createElement("button");
        updateBtn.textContent = "Update";
        updateBtn.classList.add("btn", "btn-update");
        updateBtn.addEventListener("click", () => {
          openUpdateModal(post);
        });

        btnGroup.appendChild(viewBtn);
        btnGroup.appendChild(updateBtn);

        card.appendChild(title);
        card.appendChild(desc);
        card.appendChild(imagesDiv);
        card.appendChild(btnGroup);

        searchResults.appendChild(card);
      });
    } else {
      searchResults.innerHTML = `<p style="color:red">${result.message}</p>`;
    }
  } catch (err) {
    searchResults.innerHTML = `<p style="color:red">Error fetching posts.</p>`;
  }
});

// Open modal and fill with post data
function openUpdateModal(post) {
  postIdField.value = post.post_id;
  titleField.value = post.title;
  descField.value = post.description;
  commentingField.checked = post.commenting === "allowed";
  statusField.value = post.status;
  dateField.value = post.date_posted;

  modal.style.display = "flex";
  document.body.style.overflow = "hidden";
}

// Close modal
closeBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  });
});

// Submit update form
document.getElementById("update-post-form").addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = {
    post_id: postIdField.value,
    title: titleField.value,
    description: descField.value,
    commenting: commentingField.checked ? "allowed" : "not_allowed",
    status: statusField.value
  };

  const response = await fetch("admin-post/update", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(formData)
  });

  const result = await response.json();
  alert(result.message || "Post updated!");
  modal.style.display = "none";
});
</script>
