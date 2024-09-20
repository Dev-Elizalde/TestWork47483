jQuery(document).ready(function($) {
    var currentPage = 1;
    var itemsPerPage = 10;

    function fetchCityData(search = '', page = 1, itemsPerPage = 10) {
        // Display a loading message while data is being fetched
        $('#city-list-data').html('<tr><td colspan="3">Loading data...</td></tr>');
        
        $.ajax({
            url: city_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'fetch_city_data',
                search: search,
                page: page,
                items_per_page: itemsPerPage
            },
            success: function(response) {
                // Insert the new data into the table
                $('#city-list-data').html(response.html);
                
                var totalPages = Math.ceil(response.total_results / itemsPerPage);
                $('#pagination-info').text('Page ' + response.current_page + ' of ' + totalPages);
                $('#total-entries').text('Total entries: ' + response.total_results);

                // Handle button states for pagination
                if (response.current_page === 1) {
                    $('#first-page, #prev-page').attr('disabled', true);
                } else {
                    $('#first-page, #prev-page').attr('disabled', false);
                }

                if (response.current_page === totalPages || totalPages === 0) {
                    $('#next-page, #last-page').attr('disabled', true);
                } else {
                    $('#next-page, #last-page').attr('disabled', false);
                }

                // Trigger the "Data Loading Completed" message after the data is fully loaded
                do_action('after_city_table');
            },
            error: function() {
                // If there is an error, show an error message
                $('#city-list-data').html('<tr><td colspan="3">Failed to load data.</td></tr>');
            }
        });
    }

    // Initial load
    fetchCityData();

    // Search on input
    $('#city-search-field').on('keyup', function() {
        var searchTerm = $(this).val();
        fetchCityData(searchTerm, currentPage, itemsPerPage);
    });

    // Items per page change
    $('#items-per-page').on('change', function() {
        itemsPerPage = $(this).val();
        fetchCityData($('#city-search-field').val(), currentPage = 1, itemsPerPage); // reset to page 1
    });

    // Pagination buttons
    $('#next-page').on('click', function() {
        currentPage++;
        fetchCityData($('#city-search-field').val(), currentPage, itemsPerPage);
    });

    $('#prev-page').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            fetchCityData($('#city-search-field').val(), currentPage, itemsPerPage);
        }
    });

    $('#first-page').on('click', function() {
        currentPage = 1;
        fetchCityData($('#city-search-field').val(), currentPage, itemsPerPage);
    });

    $('#last-page').on('click', function() {
        currentPage = Math.ceil($('#total-entries').text().split(': ')[1] / itemsPerPage);
        fetchCityData($('#city-search-field').val(), currentPage, itemsPerPage);
    });
});