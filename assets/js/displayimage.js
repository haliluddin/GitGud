function displayImage(event) {
    const file = event.target.files[0];
    if (file && file.size <= 5 * 1024 * 1024) { // Check if file size is less than 5MB
        const reader = new FileReader();
        reader.onload = function(e) {
            const logoContainer = document.getElementById('logoContainer');
            logoContainer.style.backgroundImage = `url(${e.target.result})`;
            logoContainer.style.backgroundSize = 'cover';
            logoContainer.style.backgroundPosition = 'center';
            logoContainer.innerHTML = `
                <input type="file" id="stalllogo" accept="image/jpeg, image/png, image/jpg" style="display:none;" onchange="displayImage(event)">
            `;
        };
        reader.readAsDataURL(file);
    } else {
        alert('File is too large or not supported. Please select a JPG, JPEG, or PNG image under 5MB.');
    }
}


function displayProductImage(event) {
    const file = event.target.files[0];
    if (file && file.size <= 5 * 1024 * 1024) { // Check if file size is less than 5MB
        const reader = new FileReader();
        reader.onload = function(e) {
            const productImageContainer = document.getElementById('productimageContainer');
            productImageContainer.style.backgroundImage = `url(${e.target.result})`;
            productImageContainer.style.backgroundSize = 'cover';
            productImageContainer.style.backgroundPosition = 'center';
            // delete div "id = product_image_div"
            var element = document.getElementById("product_image_div");
            element.parentNode.removeChild(element);
        };
        reader.readAsDataURL(file);
    } else {
        alert('File is too large or not supported. Please select a JPG, JPEG, or PNG image under 5MB.');
    }
}