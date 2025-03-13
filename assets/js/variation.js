let variationFormCount = 0;
function addVariationForm() {
    variationFormCount++;

    const variationForm = document.createElement("div");
    variationForm.className = "variation-form";
    variationForm.id = `variation-form-${variationFormCount}`;

    variationForm.innerHTML = `
        <div class="variation-header">
            <span id="variation-title-${variationFormCount}" class="fw-bold fs-5">Variation ${variationFormCount}</span>
            <button type="button" class="variation-btn rename" onclick="renameVariation(${variationFormCount})">
                <i class="fa-solid fa-pen"></i>
            </button>
            <input type="hidden" name="variation_title_${variationFormCount}" id="variation-input-${variationFormCount}" value="Variation ${variationFormCount}">
        </div>

        <div class="variation-rows-container my-2" id="variation-rows-container-${variationFormCount}">
            ${createVariationRow(variationFormCount, 1)}
            ${createVariationRow(variationFormCount, 2)}
            ${createVariationRow(variationFormCount, 3)}
        </div>
        <div class="variation-btn-group">
            <button type="button" class="variation-btn addrem" onclick="addVariationRow(${variationFormCount})">Add New Row</button>
            <button type="button" class="variation-btn addrem" onclick="removeVariationForm(${variationFormCount})">Remove Variation</button>
        </div>
    `;

    document.getElementById("variation-forms-list").appendChild(variationForm);
}

function createVariationRow(variationFormId, rowId = Date.now()) {
    return `
        <div class="variation-row" id="variation-row-${variationFormId}-${rowId}">
            <div class="variationimage text-center" id="variationimageContainer-${variationFormId}-${rowId}" onclick="triggerFileInput(${variationFormId}, ${rowId})">
                <div class="overlay">
                    <i class="fa-solid fa-arrow-up-long mb-1"></i>
                    <span>Variation Image</span>
                </div>
                <input type="file" name="variationimage_${variationFormId}[]" id="variationimage-${variationFormId}-${rowId}" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displaySelectedImage(${variationFormId}, ${rowId})">
            </div>

            <input type="text" name="variation_name_${variationFormId}[]" placeholder="Variation Name">
            
            <div class="d-flex align-items-center addpeso">
                <input type="number" name="variation_additional_price_${variationFormId}[]" placeholder="0.00" min="0" step="0.01">
            </div>
            <div class="d-flex align-items-center minuspeso">
                <input type="number" name="variation_subtract_price_${variationFormId}[]" placeholder="0.00" min="0" step="0.01">
            </div>

            <button type="button" class="variation-btn delete" onclick="removeVariationRow(this)">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    `;
}

function triggerFileInput(variationFormId, rowId) {
    const inputFile = document.getElementById(`variationimage-${variationFormId}-${rowId}`);
    inputFile.value = ''; 
    inputFile.click();
}

function displaySelectedImage(variationFormId, rowId) {
    const inputFile = document.getElementById(`variationimage-${variationFormId}-${rowId}`);
    const imageContainer = document.getElementById(`variationimageContainer-${variationFormId}-${rowId}`);

    if (inputFile.files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (e) {
            imageContainer.style.backgroundImage = `url(${e.target.result})`;
            imageContainer.querySelector('.overlay').style.display = 'none'; // Hide overlay
        };
        reader.readAsDataURL(inputFile.files[0]);
    } else {
        console.warn("No file selected!");
    }
}


function addVariationRow(variationFormId) {
    const rowId = Date.now();
    const variationRowsContainer = document.getElementById(`variation-rows-container-${variationFormId}`);
    variationRowsContainer.insertAdjacentHTML("beforeend", createVariationRow(variationFormId, rowId));
}

function removeVariationRow(button) {
    const variationRow = button.parentNode;
    variationRow.remove();
}

function removeVariationForm(variationFormId) {
    const variationForm = document.getElementById(`variation-form-${variationFormId}`);
    variationForm.remove();
}

function renameVariation(variationFormId) {
    const newTitle = prompt("Enter a new name for this variation:");
    if (newTitle && newTitle.trim()) {
        document.getElementById(`variation-title-${variationFormId}`).textContent = newTitle.trim();
        document.getElementById(`variation-input-${variationFormId}`).value = newTitle.trim(); // Update hidden input value
    }
}