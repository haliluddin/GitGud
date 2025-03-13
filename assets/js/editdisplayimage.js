function displayProductImage(event) {
    const file = event.target.files[0];
    const productImageContainer = document.getElementById('productimageContainer');

    if (file && file.size <= 500 * 1024) { // Check if file size is less than 500KB
        const reader = new FileReader();
        reader.onload = function(e) {
            // Set the new background image
            productImageContainer.style.backgroundImage = `url(${e.target.result})`;
            productImageContainer.style.backgroundSize = 'cover';
            productImageContainer.style.backgroundPosition = 'center';
            
            // Remove the inner HTML to hide the icon and text
            productImageContainer.innerHTML = 
                `<input type="file" id="productimage" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayProductImage(event)">`;
        };
        reader.readAsDataURL(file);
    } else if (file) {
        alert('File is too large or not supported. Please select a JPG, JPEG, or PNG image under 500KB.');
    }
}

// Set initial image when the page loads (for editing mode)
document.addEventListener('DOMContentLoaded', () => {
    const productImageContainer = document.getElementById('productimageContainer');
    productImageContainer.style.backgroundImage = "url('assets/images/foodpark.jpg')";
    productImageContainer.style.backgroundSize = 'cover';
    productImageContainer.style.backgroundPosition = 'center';

    // Clear inner HTML initially to hide icon and text when the default image is displayed
    productImageContainer.innerHTML = 
        `<input type="file" id="productimage" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayProductImage(event)">`;
});


function displayProfilePic(event) { 
    const file = event.target.files[0];
    const profilePicContainer = document.getElementById('profilepicContainer');

    if (file && file.size <= 500 * 1024) { // Check if file size is less than 500KB
        const reader = new FileReader();
        reader.onload = function(e) {
            // Set the new background image
            profilePicContainer.style.backgroundImage = `url(${e.target.result})`;
            profilePicContainer.style.backgroundSize = 'cover';
            profilePicContainer.style.backgroundPosition = 'center';
            
            // Remove the inner HTML to hide the icon and text
            profilePicContainer.innerHTML = 
                `<input type="file" id="profilepic" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayProfilePic(event)">`;
        };
        reader.readAsDataURL(file);
    } else if (file) {
        alert('File is too large or not supported. Please select a JPG, JPEG, or PNG image under 500KB.');
    }
}

// Set initial image when the page loads (for editing mode)
document.addEventListener('DOMContentLoaded', () => {
    const profilePicContainer = document.getElementById('profilepicContainer');
    profilePicContainer.style.backgroundImage = "url('assets/images/defaultprofile.jpg')";
    profilePicContainer.style.backgroundSize = 'cover';
    profilePicContainer.style.backgroundPosition = 'center';

    // Clear inner HTML initially to hide icon and text when the default image is displayed
    profilePicContainer.innerHTML = 
        `<input type="file" id="profilepic" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayProfilePic(event)">`;
});


function replaceImage() {
    const fileInput = document.getElementById('fileInput');
    const profileImage = document.getElementById('profileImage');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            profileImage.src = e.target.result;
        };

        reader.readAsDataURL(fileInput.files[0]);
    }
}