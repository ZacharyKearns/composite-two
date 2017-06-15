<?php

/**
 * Database Settings
 */

// Development
define('DB_HOST',     'localhost');
define('DB_USER',     'root');
define('DB_PASSWORD', 'root');
define('DB_NAME',     'composite_two');

// Production
// define('DB_HOST',     'localhost');
// define('DB_USER',     'zachary8_zach');
// define('DB_PASSWORD', 'password111');
// define('DB_NAME',     'zachary8_wddm');

/**
 * Timezone Settings
 */

date_default_timezone_set( 'America/Toronto' );

/**
 * Login Settings
 */

define('LOGGED_IN', 'sdg7987sga9f87gf9fhd76fdgh84nsihnfy48djsc8ey');

/**
 * Error Reporting
 */

error_reporting(E_ALL & ~E_NOTICE);

if (!ini_get('display_errors')) {
   ini_set('display_errors', '1');
}

/**
 * Include Path Settings
 */

// folders where PHP should look for
// any include files needed
$paths = array(
   'includes/',
   'includes/libraries/',
   'includes/libraries/PHPMailer/'
);

set_include_path(
   get_include_path()
   . PATH_SEPARATOR
   . implode( PATH_SEPARATOR, $paths )
);

/**
 * Google Maps API
 */

define('GOOGLE_MAPS_API_KEY', 'AIzaSyCywxXdrdRcqgeACGZFzbH6oXnv-lAwrBs');

/**
 * Image Settings
 */

// Max size of user image 800kb
define('MAX_USER_IMAGE_FILE_SIZE', 819200);

// Max size of gallery image 5mb
define('MAX_FILE_SIZE', 5242880);

// User image folder
define('USER_IMAGE_FOLDER', 'images/user-images/');

// User image folder large
define('USER_IMAGE_FOLDER_LARGE', 'images/user-images/large/');

// User image folder medium
define('USER_IMAGE_FOLDER_MEDIUM', 'images/user-images/medium/');

// User image folder small
define('USER_IMAGE_FOLDER_SMALL', 'images/user-images/small/');

// User galleries folder
define('USER_GALLERIES_FOLDER', 'images/user-galleries/');

// Randomize the image filenames
define('RANDOMIZE_FILENAME', true);

// Types of image files allowed
define('ALLOWED_FILE_TYPES', 'image/jpeg, image/png, image/gif, image/pjpeg');

// Quality of the images files
define('IMAGE_QUALITY', 10);



/******** GAGAN'S CONFIG SETTINGS ************/
