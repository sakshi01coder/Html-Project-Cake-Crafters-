document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.card'); // Select all cards
    const selectedWeight = document.querySelectorAll('.cakeWeight');
    const selectedWeightElement = document.querySelectorAll('.selectedWeight');
    const cakePriceElement = document.querySelectorAll('.cakePrice');

    function updateCakePrice(kgs, index) {
        const cakePrice = cakePriceElement[index];
        const weight = selectedWeightElement[index];

        if (+kgs === 1) {
            weight.innerHTML = '1 kg';
            cakePrice.innerHTML = (+cakePrice.dataset.price * 2).toFixed(2); // Multiply price by 2 for 1 kg
        }

        if (+kgs === 0.5) {
            weight.innerHTML = '1/2 kg';
            cakePrice.innerHTML = (+cakePrice.dataset.price).toFixed(2); // Use original price for 0.5 kg
        }
    }

    // Attach change event listeners to each select element for weight selection
    selectedWeight.forEach((select, index) => {
        select.addEventListener('change', function (e) {
            updateCakePrice(e.target.value, index);
        });
    });

    // Attach click event listeners to "Add to Cart" buttons in each card
    cards.forEach((card, index) => {
        const addToCartButton = card.querySelector('.btn-secondary[title="Add to Cart"]');
        
        addToCartButton.addEventListener('click', function () {
            let productId = card.querySelector('.cakePrice').dataset.productId;
            let selectedWeight = card.querySelector('.cakeWeight').value;
            
            // Create a form data object to send the information to the server
            let formData = new FormData();
            formData.append('product_id', productId);
            formData.append('weight', selectedWeight);
            
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item added to cart successfully!');
                } else {
                    alert('Failed to add item to cart. Please try again.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
