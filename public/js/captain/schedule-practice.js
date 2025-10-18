document.addEventListener("DOMContentLoaded", () => {
    const editButtons = document.querySelectorAll(".edit-btn");
    const form = document.querySelector("#scheduleForm");
    const createBtn = document.querySelector("#createBtn");


    editButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            // Populate form fields
            form.facility.value = btn.dataset.facility || '';
            form.date.value = btn.dataset.date || '';
            form.time.value = btn.dataset.time || '';
            form.description.value = btn.dataset.description || '';


            // Create or update hidden id field
            let hiddenId = form.querySelector("[name='id']");
            if (!hiddenId) {
                hiddenId = document.createElement("input");
                hiddenId.type = "hidden";
                hiddenId.name = "id";
                form.appendChild(hiddenId);
            }
            hiddenId.value = btn.dataset.id;


            /// Change button from create -> update
            if (createBtn) {
                createBtn.name = "update"; // controller looks for 'update'
                createBtn.textContent = "Update Practice";
                createBtn.id = "updateBtn"; // optional
            }
        });
    });

    const restoreCreate = () => {
        if (createBtn) {
            createBtn.name = "create";
            createBtn.textContent = "Schedule Practice";
            createBtn.id = "createBtn";
        }
        // remove hidden id
        const hid = form.querySelector("[name='id']");
        if (hid) hid.remove();
    };

    // If user edits manually and clears the id hidden input, restore button
    form.addEventListener("reset", restoreCreate);
});
