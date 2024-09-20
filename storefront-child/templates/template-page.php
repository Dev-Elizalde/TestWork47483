<?php
/*
Template Name: City List
*/

get_header();

// Custom Action Hook before table
do_action('before_city_table');
?>

<div id="city-list-container" class="table-container">
    <div class="controls">
        <label for="items-per-page">Show 
            <select id="items-per-page">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select> Rows
        </label>

        <input type="text" id="city-search-field" placeholder="Search" class="search-input" />
    </div>

    <div class="responsive-table">
        <table id="city-list-table" class="custom-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>City</th>
                    <th>Temperature (°C)</th>
                </tr>
            </thead>
            <tbody id="city-list-data">
                <!-- Data will be loaded here using Ajax -->
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        <span id="pagination-info" class="pagination-info">Page 1</span>
        <div class="pagination-controls">
            <button id="first-page" class="pagination-button">
                <span class="button-text">First</span>
                <i class="fas fa-angle-double-left"></i>
            </button>
            <button id="prev-page" class="pagination-button">
                <span class="button-text">Previous</span>
                <i class="fas fa-angle-left"></i>
            </button>
            <button id="next-page" class="pagination-button">
                <i class="fas fa-angle-right"></i>
                <span class="button-text">Next</span>
            </button>
            <button id="last-page" class="pagination-button">
                <i class="fas fa-angle-double-right"></i>
                <span class="button-text">Last</span>
            </button>
        </div>
    </div>

    <div id="total-entries" class="total-entries">Total entries: 0</div>
</div>

<?php
// Custom Action Hook after table
do_action('after_city_table');

get_footer();
?>