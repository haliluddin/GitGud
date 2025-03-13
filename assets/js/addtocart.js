document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-product-id');
            const quantity = parseInt(document.getElementById('quantity').innerText);
            const stall = document.querySelector('#stall-id');
            const stallId = stall.getAttribute('data-stall-id');
            alert('Stall ID: ' + stallId + '\nProduct ID: ' + productId + '\nQuantity: ' + quantity);

            fetch('./add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    product_id: productId,
                    quantity: quantity,
                    stall_id: stallId
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                } else {
                    alert('Failed to add product to cart.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});