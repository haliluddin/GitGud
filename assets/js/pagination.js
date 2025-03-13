const rowsPerPage = 10; 
const table = document.querySelector(".salestable");
const pagination = document.querySelector(".saletabpag");

function generatePagination() {
    const rows = Array.from(table.querySelectorAll("tr")).slice(1); // Exclude header row
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    const maxVisiblePages = 10; // Maximum number of visible page numbers
    let currentPageGroup = 1; // Start at the first group

    pagination.innerHTML = ""; // Clear existing pagination

    // Function to show a specific set of pages
    function showPageGroup(group) {
        pagination.innerHTML = ""; // Clear existing pagination

        const startPage = (group - 1) * maxVisiblePages + 1;
        const endPage = Math.min(group * maxVisiblePages, pageCount);

        // Add back arrow if not the first group
        if (group > 1) {
            const backArrow = document.createElement("i");
            backArrow.className = "fa-solid fa-arrow-left";
            backArrow.addEventListener("click", () => {
                currentPageGroup--;
                showPageGroup(currentPageGroup);
                showPage((currentPageGroup - 1) * maxVisiblePages + 1); // Set active to the first page in the new group
            });
            pagination.appendChild(backArrow);
        }

        // Add visible page numbers
        for (let i = startPage; i <= endPage; i++) {
            const pageNumber = document.createElement("span");
            pageNumber.textContent = i;
            pageNumber.classList.add("page-number");
            pageNumber.addEventListener("click", () => showPage(i));
            pagination.appendChild(pageNumber);
        }

        // Add next arrow if not the last group
        if (group < Math.ceil(pageCount / maxVisiblePages)) {
            const nextArrow = document.createElement("i");
            nextArrow.className = "fa-solid fa-arrow-right";
            nextArrow.addEventListener("click", () => {
                currentPageGroup++;
                showPageGroup(currentPageGroup);
                showPage((currentPageGroup - 1) * maxVisiblePages + 1); // Set active to the first page in the new group
            });
            pagination.appendChild(nextArrow);
        }
    }

    // Show rows for a specific page
    function showPage(page) {
        const rows = Array.from(table.querySelectorAll("tr")).slice(1); // Exclude header row
        rows.forEach((row, index) => {
            row.style.display =
                index >= (page - 1) * rowsPerPage && index < page * rowsPerPage
                    ? "table-row"
                    : "none";
        });

        // Update active page styling
        const pageNumbers = pagination.querySelectorAll(".page-number");
        pageNumbers.forEach((pageNumber) => pageNumber.classList.remove("active"));
        const activePageNumber = Array.from(pageNumbers).find((p) => parseInt(p.textContent) === page);
        if (activePageNumber) activePageNumber.classList.add("active");
    }

    // Show the first group and initialize
    showPageGroup(currentPageGroup);
    showPage(1);
}

// Initialize pagination
generatePagination();
