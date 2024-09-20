# TestWork47483 Sample Task

WordPress City Listing Table with AJAX

This project implements a custom WordPress functionality that displays a list of cities with corresponding countries and temperatures fetched from the OpenWeatherMap API. The table supports AJAX-powered dynamic loading, search functionality, and mobile responsiveness.

Features
- Custom Post Type: Cities – Displays city information.
- Custom Taxonomy: Countries – Associates cities with countries.
- OpenWeatherMap Integration – Fetches and displays real-time weather information for each city.
- AJAX-Powered Table – Loads and updates city data dynamically without page refresh.
- Search Functionality – Allows searching for cities by name.

Installation
1. Download the Files: Download and add the code into your WordPress child theme.
2. Create a Child Theme: If you haven’t already, create a WordPress child theme to ensure your changes are not lost during updates.
3. Add the Code to Your Child Theme:
- Add the PHP code in functions.php.
- Create a custom page template for displaying the table.
- Add the necessary JavaScript and CSS to your child theme.
4. Register the OpenWeatherMap API Key: You’ll need an OpenWeatherMap API key to fetch real-time temperature data. Replace the placeholder in the code with your API key.
- $api_key = 'YOUR_API_KEY';
5. Create Custom Post Type and Taxonomy: Add cities and countries via the WordPress dashboard after setting up the custom post type and taxonomy.

How to Use
1. City Listing Table with AJAX
- This table dynamically loads cities from the custom post type and displays their corresponding countries and real-time temperatures using AJAX. Users can search for cities they want to display per page.

Project Structure
Here’s how your project should be organized:

wp-content/themes/your-child-theme/
│
├── screenshot.png  # Thumbnail for child theme
├── style.css # Style CSS
├── functions.php  # Custom functions and hook registration
├── assets/
│   └── js/
│       └── city-list.js  # JavaScript for AJAX and pagination
├── assets/
│   └── css/
│       └── style.css  # Custom styles for the table and pagination
├── assets/
│   └── widgets/
│       └── city-weather-widget.css  # Custom widget
├── templates/
│   └── template-page.php  # Custom page template to display the table

Requirements
WordPress 5.0 or higher
- PHP 7.0 or higher
- OpenWeatherMap API Key

Known Issues & Limitations
- Ensure that the OpenWeatherMap API key is valid; otherwise, temperature data won't be fetched properly.
- The system does not support caching, so each table load triggers a new API request for temperature data, which could affect performance for large datasets.
