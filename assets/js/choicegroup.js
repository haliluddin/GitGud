function triggerFileInput(rowId) {
    const inputFile = document.getElementById(`choice-group-image-${rowId}`);
    inputFile.value = ''; // Reset input to allow re-upload
    inputFile.click();
}

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

function addChoiceGroupRow() {
    const rowId = Date.now();
    const choiceGroupRowsContainer = document.getElementById("choice-group-rows-container");
    choiceGroupRowsContainer.insertAdjacentHTML("beforeend", createChoiceGroupRow(rowId));
}

function removeChoiceGroupRow(button) {
    const choiceGroupRow = button.parentNode;
    choiceGroupRow.remove();
}

function renameChoiceGroup() {
    const newTitle = prompt("Enter a new name for this choice group:");
    if (newTitle && newTitle.trim()) {
        document.getElementById("choice-group-title").textContent = newTitle.trim();
    }
}

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
                <option value="available"><i class="fa-solid fa-circle"></i> Available</option>
                <option value="unavailable"><i class="fa-solid fa-circle"></i> Unavailable</option>
            </select>
            <button type="button" class="choice-group-btn delete" onclick="removeChoiceGroupRow(this)">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    `;
}
