document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('editfoodpark');
    modal.addEventListener('shown.bs.modal', () => {
        const fileInput = document.getElementById('fplogo');
        const uploadedFilesContainer = document.getElementById('uploaded-files');
        const logoContainer = document.querySelector('.logocon');

        function displayUploadedFile(fileName = null) {
            uploadedFilesContainer.innerHTML = ''; 

            const fileEntry = document.createElement('div');
            fileEntry.className = 'uploaded-file d-flex align-items-center justify-content-between';

            const checkIcon = document.createElement('i');
            checkIcon.className = 'fa-solid fa-circle-check';
            checkIcon.style.color = 'green';
            checkIcon.style.marginRight = '8px';

            const fileNameSpan = document.createElement('span');
            fileNameSpan.textContent = fileName || 'No file selected';
            fileNameSpan.className = 'file-name';
            fileNameSpan.style.flexGrow = '1';

            const deleteIcon = document.createElement('i');
            deleteIcon.className = 'fa-regular fa-trash-can';
            deleteIcon.style.color = 'red';
            deleteIcon.style.marginLeft = '8px';
            deleteIcon.onclick = () => {
                fileInput.value = ''; 
                uploadedFilesContainer.innerHTML = '<span>No file selected</span>'; 
            };

            fileEntry.appendChild(checkIcon);
            fileEntry.appendChild(fileNameSpan);
            fileEntry.appendChild(deleteIcon);

            uploadedFilesContainer.appendChild(fileEntry);
        }

        logoContainer.addEventListener('click', () => {
            fileInput.click(); 
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0]; 
            if (file) {
                displayUploadedFile(file.name); 
            }
        });

        const preloadedFile = 'example-permit.pdf'; 
        if (preloadedFile) {
            displayUploadedFile(preloadedFile);
        }
    });
});
