const rowsPerPage = 5;
const maxVisiblePages = 5;

document.querySelectorAll('.section-content').forEach(section => {
  const table = section.querySelector('.salestable');
  const pagination = section.querySelector('.saletabpag');
  
  if (table && pagination) {
    generatePaginationForSection(table, pagination);
  }
});

function generatePaginationForSection(table, pagination) {
  // Get all table rows except the header
  const rows = Array.from(table.querySelectorAll("tr")).slice(1);
  const pageCount = Math.ceil(rows.length / rowsPerPage);
  let currentPageGroup = 1;
  
  // Clear any existing pagination
  pagination.innerHTML = "";
  
  function showPageGroup(group) {
    pagination.innerHTML = "";
    const startPage = (group - 1) * maxVisiblePages + 1;
    const endPage = Math.min(group * maxVisiblePages, pageCount);
    
    // If not in the first group, add a back arrow
    if (group > 1) {
      const backArrow = document.createElement("i");
      backArrow.className = "fa-solid fa-arrow-left";
      backArrow.style.cursor = "pointer";
      backArrow.addEventListener("click", () => {
        currentPageGroup--;
        showPageGroup(currentPageGroup);
        showPage((currentPageGroup - 1) * maxVisiblePages + 1);
      });
      pagination.appendChild(backArrow);
    }
    
    for (let i = startPage; i <= endPage; i++) {
      const pageNumber = document.createElement("span");
      pageNumber.textContent = i;
      pageNumber.classList.add("page-number");
      pageNumber.style.cursor = "pointer";
      pageNumber.addEventListener("click", () => showPage(i));
      pagination.appendChild(pageNumber);
    }
    
    // If there are more groups, add a next arrow
    if (group < Math.ceil(pageCount / maxVisiblePages)) {
      const nextArrow = document.createElement("i");
      nextArrow.className = "fa-solid fa-arrow-right";
      nextArrow.style.cursor = "pointer";
      nextArrow.addEventListener("click", () => {
        currentPageGroup++;
        showPageGroup(currentPageGroup);
        showPage((currentPageGroup - 1) * maxVisiblePages + 1);
      });
      pagination.appendChild(nextArrow);
    }
  }
  
  function showPage(page) {
    rows.forEach((row, index) => {
      row.style.display =
        index >= (page - 1) * rowsPerPage && index < page * rowsPerPage
          ? "table-row"
          : "none";
    });
    
    const pageNumbers = pagination.querySelectorAll(".page-number");
    pageNumbers.forEach(pageNumber => pageNumber.classList.remove("active"));
    const activePageNumber = Array.from(pageNumbers).find(
      p => parseInt(p.textContent) === page
    );
    if (activePageNumber) activePageNumber.classList.add("active");
  }
  
  // Initialize pagination
  showPageGroup(currentPageGroup);
  showPage(1);
}
