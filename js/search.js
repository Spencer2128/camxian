 // Real-time search function
 $(document).ready(function() {
    $('#searchQuery').on('input', function() {
        var query = $(this).val();

        // AJAX request
        $.ajax({
            url: 'search.php',
            type: 'GET',
            data: { query: query },
            success: function(response) {
                $('#product-list').html(response);  // Update product list
            }
        });
    });
});