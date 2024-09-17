
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.add-to-cart');

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            console.log(productId);
            fetch('/cart/ajax_add_to_cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken") ?>', // Token CSRF si es necesario
                },
                body: JSON.stringify({
                    extension_id: productId, 
                    quantity: 1
                })
            })
                .then(response => response.json()) // Convertir la respuesta a JSON
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert('Error adding product to cart');
                    }
                })
                .catch(error => {
                    alert('Request failed: ' + error.message);
                });
        });
    });
});
