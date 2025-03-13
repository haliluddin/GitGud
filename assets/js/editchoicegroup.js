// Trigger file input for image upload
function triggerFileInput(rowId) {
    const inputFile = document.getElementById(`choice-group-image-${rowId}`);
    inputFile.value = ''; // Reset input to allow re-upload
    inputFile.click();
}

// Display the selected image
function displaySelectedImage(rowId) {
    const inputFile = document.getElementById(`choice-group-image-${rowId}`);
    const imageContainer = document.getElementById(`choice-group-imageContainer-${rowId}`);

    if (inputFile.files && inputFile.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            imageContainer.style.backgroundImage = `url(${e.target.result})`;
            imageContainer.querySelector('.overlay').style.display = 'none';
        };
        reader.readAsDataURL(inputFile.files[0]);
    }
}

// Add a new choice group row
function addChoiceGroupRow() {
    const rowId = Date.now();
    const choiceGroupRowsContainer = document.getElementById("choice-group-rows-container");
    choiceGroupRowsContainer.insertAdjacentHTML("beforeend", createChoiceGroupRow(rowId));
}

// Remove a choice group row
function removeChoiceGroupRow(button) {
    const choiceGroupRow = button.parentNode;
    choiceGroupRow.remove();
}

// Rename the choice group
function renameChoiceGroup() {
    const newTitle = prompt("Enter a new name for this choice group:");
    if (newTitle && newTitle.trim()) {
        document.getElementById("choice-group-title").textContent = newTitle.trim();
    }
}

// Create a new choice group row
function createChoiceGroupRow(rowId) {
    return `
        <div class="choice-group-row" id="choice-group-row-${rowId}">
            <div class="choice-group-image text-center" id="choice-group-imageContainer-${rowId}" onclick="triggerFileInput(${rowId})">
                <div class="overlay">
                    <i class="fa-solid fa-arrow-up-long mb-1"></i><br>
                    <span>Choice Image</span>
                </div>
                <input type="file" id="choice-group-image-${rowId}" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displaySelectedImage(${rowId})">
            </div>
            <input type="text" name="choice_group_name[]" placeholder="Choice Group Name">
            <input type="number" name="choice_group_additional_price[]" placeholder="0.00" min="0" step="0.01">
            <input type="number" name="choice_group_subtract_price[]" placeholder="0.00" min="0" step="0.01">
            <select name="choice_group_availability[]" class="availability-dropdown">
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
            <button type="button" class="choice-group-btn delete" onclick="removeChoiceGroupRow(this)">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    `;
}

// Edit existing choice group by loading data dynamically
function editChoiceGroup(existingData) {
    document.getElementById("choice-group-title").textContent = existingData.title || "Choice Group";
    const choiceGroupRowsContainer = document.getElementById("choice-group-rows-container");
    choiceGroupRowsContainer.innerHTML = ''; // Clear existing rows

    existingData.rows.forEach((row, index) => {
        const rowId = Date.now() + index; // Generate a unique ID for each row
        choiceGroupRowsContainer.insertAdjacentHTML("beforeend", createChoiceGroupRow(rowId));
        
        document.querySelector(`#choice-group-row-${rowId} input[name='choice_group_name[]']`).value = row.name;
        document.querySelector(`#choice-group-row-${rowId} input[name='choice_group_additional_price[]']`).value = row.additionalPrice;
        document.querySelector(`#choice-group-row-${rowId} input[name='choice_group_subtract_price[]']`).value = row.subtractPrice;
        document.querySelector(`#choice-group-row-${rowId} select[name='choice_group_availability[]']`).value = row.availability;
    });
}
