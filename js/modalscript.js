function openModal(tableName) {
    // Set the table name in the hidden input field
    document.getElementById('tableName').value = tableName;
    // Open the modal
    document.getElementById('addProductModal').style.display = "block";
}


// Close the modal
function closeModal() {
    document.getElementById('addProductModal').style.display = "none";
}

// Handle the form submission with AJAX
document.getElementById('add-product-form').addEventListener('submit', function (e) {
    e.preventDefault();

    var formData = new FormData(this);  // Collect form data
    formData.append('add_product', true);  // Add custom flag for identifying the request

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'add_product.php', true);  // Send data to add_product.php
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);  // Parse the JSON response from the server
            if (response.status === 'success') {
                alert('Product added/updated successfully');  // Notify the user of success
                closeModal();  // Close the modal
                location.reload();  // Reload the page to reflect the changes
            } else {
                alert('Error: ' + response.message);  // Notify the user of an error
            }
        }
    };
    xhr.send(formData);  // Send the form data to the server
});
