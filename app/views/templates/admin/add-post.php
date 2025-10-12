<section id="add-post">
  <h2>Add a New Post</h2>
  <!-- enctype is important for file uploads -->
  <form id="add-post-form" enctype="multipart/form-data">
    <div class="input-div">
      <label for="post-title">Title</label>
      <input type="text" id="post-title" name="title" placeholder="Enter post title..." required>
      <div class="error">Title cannot be empty!</div>
    </div>

    <div class="input-div">
      <label for="post-desc">Description</label>
      <textarea id="post-desc" name="description" placeholder="Write something interesting..." rows="4" required></textarea>
      <div class="error">Description cannot be empty!</div>
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
    <div id="form-message"></div>
  </form>
</section>

<script>
  // ============ IMAGE PREVIEW ============
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

  // ============ FORM SUBMISSION ============
  const form = document.getElementById("add-post-form");
  const msg = document.getElementById("form-message");

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
        msg.textContent = "Post added successfully!";
        msg.style.color = "green";
        form.reset();
        previewDiv.innerHTML = "";
      } else {
        msg.textContent = result.message;
        msg.style.color = "red";
      }
    } catch (err) {
      msg.textContent = "Something went wrong. Try again!";
      msg.style.color = "red";
    }
  });
</script>
