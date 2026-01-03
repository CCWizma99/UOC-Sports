<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Feed | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/news-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="news-grid-container">
        <div class="news-grid-left">
            <section id="search-posts">
                <h2>Search Posts</h2>
                <input type="text" id="search-query" placeholder="Search posts..." />
                <button id="search-btn">Search</button>

                <div id="search-results"></div>
            </section>
        </div>

        <div class="news-grid-right">
            <section id="add-post">
                <h2>Add a New Post</h2>
                <form id="add-post-form" enctype="multipart/form-data" novalidate>
                    <p class="required-note"><span>*</span> Required fields</p>
                    
                    <div class="input-div">
                        <label for="post-title">Title <span class="required">*</span></label>
                        <input type="text" id="post-title" name="title" 
                               placeholder="Enter post title..." 
                               aria-required="true" 
                               aria-describedby="title-error"
                               required>
                        <div class="error" id="title-error" role="alert">Title cannot be empty!</div>
                    </div>

                    <div class="input-div">
                        <label for="post-desc">Description <span class="required">*</span></label>
                        <textarea id="post-desc" name="description" 
                                  placeholder="Write something interesting..." 
                                  rows="4" 
                                  aria-required="true"
                                  aria-describedby="desc-error"
                                  required></textarea>
                        <div class="error" id="desc-error" role="alert">Description cannot be empty!</div>
                    </div>

                    <div class="input-div">
                        <label for="post-files">Upload Images</label>
                        <input type="file" id="post-files" name="files[]" multiple accept="image/*">
                        <div id="file-preview"></div>
                    </div>

                    <div class="settings">
                        <h3>Additional Settings</h3>
                        <div class="radio-group">
                            <label>
                                <input type="checkbox" id="disable-comments" name="allow-comments" value="no">
                                Disable Commenting
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn">Add Post</button>
                </form>
            </section>
        </div>
    </div>
</div>

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
                    <option value="ACTIVE">Active</option>
                    <option value="INACTIVE">Inactive</option>
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

// Also search on Enter key
searchQuery.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
        searchBtn.click();
    }
});

// Open modal and fill with post data
function openUpdateModal(post) {
    postIdField.value = post.post_id;
    titleField.value = post.title;
    descField.value = post.description;
    commentingField.checked = (post.commenting && post.commenting.toUpperCase() === "YES");
    statusField.value = post.status ? post.status.toUpperCase() : "ACTIVE";
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
        commenting: commentingField.checked ? "YES" : "NO",
        status: statusField.value
    };

    try {
        const response = await fetch("admin-post/update", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(formData)
        });

        const result = await response.json();
        
        if (result.status === "success") {
            showNotification(result.message || "Post updated successfully!", "success");
            modal.style.display = "none";
            document.body.style.overflow = "auto";
            searchBtn.click();
        } else {
            showNotification(result.message || "Failed to update post.", "error");
        }
    } catch (err) {
        showNotification("Error updating post: " + err.message, "error");
    }
});
</script>

<script>
// Add Post Scripts
const fileInput = document.getElementById("post-files");
const previewDiv = document.getElementById("file-preview");

fileInput.addEventListener("change", () => {
    previewDiv.innerHTML = "";
    [...fileInput.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement("img");
            img.src = e.target.result;
            img.style.width = "100px";
            img.style.height = "100px";
            img.style.objectFit = "cover";
            img.style.borderRadius = "8px";
            img.style.marginRight = "5px";
            previewDiv.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});

const form = document.getElementById("add-post-form");

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const commenting = document.getElementById("disable-comments").checked ? "NO" : "YES";
    formData.append("commenting", commenting);

    try {
        const response = await fetch("admin-post/add-post", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.status === "success") {
            showNotification("Post added successfully!", "success");
            form.reset();
            previewDiv.innerHTML = "";
        } else {
            showNotification(result.message, "error");
        }
    } catch (err) {
        showNotification("Something went wrong. Try again!", "error");
    }
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-news");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
