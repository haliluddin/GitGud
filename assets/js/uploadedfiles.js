function handleFileUpload(inputId, containerId) {
    const fileInput = document.getElementById(inputId);
    const uploadedFilesContainer = document.getElementById(containerId);

    fileInput.addEventListener('change', () => {
        const newFile = fileInput.files[0]; // Get the selected file

        // Clear the container for a single displayed file
        uploadedFilesContainer.innerHTML = '';

        if (newFile) {
            // Create the container for the selected file
            const fileEntry = document.createElement('div');
            fileEntry.classList.add('uploaded-file');
            fileEntry.style.display = 'flex';
            fileEntry.style.alignItems = 'center';
            fileEntry.style.justifyContent = 'space-between';

            const checkIcon = document.createElement('i');
            checkIcon.className = 'fa-solid fa-circle-check icon-btn';
            checkIcon.style.color = 'green';
            checkIcon.style.marginRight = '8px';

            const fileName = document.createElement('span');
            fileName.textContent = newFile.name;
            fileName.classList.add('file-name');
            fileName.style.flexGrow = '1';

            const deleteIcon = document.createElement('i');
            deleteIcon.className = 'fa-regular fa-trash-can icon-btn';
            deleteIcon.style.color = 'red';
            deleteIcon.style.marginLeft = '8px';
            deleteIcon.onclick = () => {
                // Clear the displayed file on delete
                fileInput.value = ''; // Reset the input field
                fileEntry.remove();
            };

            fileEntry.appendChild(checkIcon);
            fileEntry.appendChild(fileName);
            fileEntry.appendChild(deleteIcon);
            uploadedFilesContainer.appendChild(fileEntry);
        }
    });
}

// Attach event listeners for both file inputs
handleFileUpload('fplogo', 'uploaded-files');
handleFileUpload('businesslogo', 'uploaded-parkfiles');