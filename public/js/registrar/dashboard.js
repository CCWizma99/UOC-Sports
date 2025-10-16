function toggleMenu(btn) {
    const menu = btn.nextElementSibling;
    document.querySelectorAll(".dropdown-menu").forEach(m => {
        if (m !== menu) m.style.display = "none";
    });
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

document.addEventListener("click", function (e) {
    if (!e.target.matches(".dots")) {
        document.querySelectorAll(".dropdown-menu").forEach(menu => {
            menu.style.display = "none";
        });
    }
});

function approve(btn) {
    const row = btn.closest(".table-row");
    row.classList.add("approved");
    row.classList.remove("rejected");
    alert("Approved successfully!");
}

function reject(btn) {
    const row = btn.closest(".table-row");
    row.classList.add("rejected");
    row.classList.remove("approved");
    alert("Rejected successfully!");
}

function searchTable() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    document.querySelectorAll(".table-row").forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "grid" : "none";
    });
}
