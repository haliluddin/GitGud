document.addEventListener('DOMContentLoaded', () => {
    // Event listener for when the modal is shown
    const modal = document.getElementById('editfoodpark');
    modal.addEventListener('shown.bs.modal', () => {
        const fileInput = document.getElementById('fplogo');
        const uploadedFilesContainer = document.getElementById('uploaded-files');
        const logoContainer = document.querySelector('.logocon');

        // Function to display uploaded file
        function displayUploadedFile(fileName = null) {
            uploadedFilesContainer.innerHTML = ''; // Clear existing content

            const fileEntry = document.createElement('div');
            fileEntry.className = 'uploaded-file d-flex align-items-center justify-content-between';

            // Check icon
            const checkIcon = document.createElement('i');
            checkIcon.className = 'fa-solid fa-circle-check';
            checkIcon.style.color = 'green';
            checkIcon.style.marginRight = '8px';

            // File name
            const fileNameSpan = document.createElement('span');
            fileNameSpan.textContent = fileName || 'No file selected';
            fileNameSpan.className = 'file-name';
            fileNameSpan.style.flexGrow = '1';

            // Delete icon
            const deleteIcon = document.createElement('i');
            deleteIcon.className = 'fa-regular fa-trash-can';
            deleteIcon.style.color = 'red';
            deleteIcon.style.marginLeft = '8px';
            deleteIcon.onclick = () => {
                fileInput.value = ''; // Reset the file input
                uploadedFilesContainer.innerHTML = '<span>No file selected</span>'; // Default placeholder
            };

            // Append elements to the file entry
            fileEntry.appendChild(checkIcon);
            fileEntry.appendChild(fileNameSpan);
            fileEntry.appendChild(deleteIcon);

            // Append file entry to the container
            uploadedFilesContainer.appendChild(fileEntry);
        }

        // Event listener for the upload area click
        logoContainer.addEventListener('click', () => {
            fileInput.click(); // Trigger the file input
        });

        // Event listener for file input change
        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0]; // Get the selected file
            if (file) {
                displayUploadedFile(file.name); // Update UI with file name
            }
        });

        // If there is a preloaded file, display it (e.g., editing a profile)
        const preloadedFile = 'example-permit.pdf'; // Replace with actual data dynamically if available
        if (preloadedFile) {
            displayUploadedFile(preloadedFile);
        }
    });
});
