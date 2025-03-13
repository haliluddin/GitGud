function toggleDropdown() {
    const dropdown = document.getElementById("dropdownMenu");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

window.onclick = function(event) {
    if (!event.target.matches('.dropdown-content') && !event.target.closest('a')) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            const openDropdown = dropdowns[i];
            openDropdown.style.display === "block" ? "none" : "block";
        }
    }
}