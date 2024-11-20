// Function to show the modal when the user clicks on the logout link
function confirmLogout() {
    console.log("Logout clicked, showing modal");
    document.getElementById("logoutModal").style.display = "block"; // Show the modal
    return false; // Prevent default link behavior
}

// Function to close the modal
function closeModal() {
    console.log("Closing modal");
    document.getElementById("logoutModal").style.display = "none"; // Hide the modal
}

// Function to log out the user
function logout() {
    console.log("Logging out...");
    window.location.href = "logout.php"; // Redirect to the logout page
}

// Function to highlight matching text in the main content
function highlightText() {
    let query = document.getElementById('search-input').value.trim().toLowerCase();
    let rows = document.querySelectorAll('#product-list tr');

    rows.forEach(row => {
        let columns = row.getElementsByTagName('td');
        let matchFound = false;

        // Loop through columns in each row
        for (let i = 0; i < columns.length; i++) {
            let columnText = columns[i].innerText.toLowerCase();
            if (columnText.includes(query) && query !== "") {
                matchFound = true;
                columns[i].classList.add('highlight');
            } else {
                columns[i].classList.remove('highlight');
            }
        }

        // If a match is found in the row, scroll it into view
        if (matchFound) {
            row.classList.add('highlight-scroll');
            row.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            row.classList.remove('highlight-scroll');
        }
    });
}

// Trigger highlightText function on keyup in search bar
document.getElementById('search-input').addEventListener('keyup', highlightText);

// Function to add predefined fields for barcode, number series, model, resolution, description, and quantity
function addField() {
    const fieldsContainer = document.getElementById('fields-container');

    // Define an array of predefined fields with their labels and placeholders
    const predefinedFields = [
        { label: 'Barcode', placeholder: 'Barcode', name: 'barcode' },
        { label: 'Number Series', placeholder: 'Series', name: 'number_series' },
        { label: 'Model', placeholder: 'Model', name: 'model' },
        { label: 'Resolution', placeholder: 'Resolution', name: 'resolution' },
        { label: 'Description', placeholder: 'Description', name: 'description' },
        { label: 'Quantity', placeholder: 'Quantity', name: 'quantity' }
    ];

    // Loop through predefined fields and create input elements
    predefinedFields.forEach(field => {
        const fieldWrapper = document.createElement('div');
        fieldWrapper.classList.add('field-wrapper'); // Optional for styling

        // Create the label
        const label = document.createElement('label');
        label.innerText = field.label;
        fieldWrapper.appendChild(label);

        // Create the input field
        const input = document.createElement('input');
        input.type = 'text';
        input.name = field.name;  // Use the predefined name
        input.placeholder = field.placeholder;
        fieldWrapper.appendChild(input);

        // Create a delete button to remove the field
        const deleteButton = document.createElement('button');
        deleteButton.innerText = 'Remove';
        deleteButton.type = 'button';
        deleteButton.classList.add('remove-field');  // Optional for styling
        deleteButton.onclick = function () {
            fieldsContainer.removeChild(fieldWrapper);
        };
        fieldWrapper.appendChild(deleteButton);

        // Append the field to the container
        fieldsContainer.appendChild(fieldWrapper);
    });
}

// Optional: Function to reset the fields container (e.g., after submitting the form)
function resetFields() {
    const fieldsContainer = document.getElementById('fields-container');
    fieldsContainer.innerHTML = '';  // Clear the fields container
}


//function to delete table//
function deleteTable(tableName) {
    if (confirm(`Are you sure you want to delete the table ${tableName}?`)) {
        // Redirect to delete_table.php with the table name as a query parameter
        window.location.href = `delete_table.php?table=${tableName}`;
        
    }

    
}
document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".sidebar-links li a");

    links.forEach(link => {
        link.addEventListener("click", function () {
            // Remove active class from all links
            links.forEach(l => l.classList.remove("active"));

            // Add active class to the clicked link
            this.classList.add("active");
        });
    });
});

