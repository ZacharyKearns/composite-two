<?php
/**
 * Sanitizes data for use in a mysqli query.
 *
 * @param link $db The link resource for the database connection
 * @param string $data Value to sanitize
 *
 * @return string Sanitized version of the data.
 */
function sanitize($db, $data) {
   $data = trim($data);
   $data = strip_tags($data);
   $data = mysqli_real_escape_string($db, $data);
   return $data;
}

/**
 * Redirect the browser to the given url, using a 301 redirect.
 *
 * @param string $url The address to redirect to.
 */
function redirect($url) {
   @header('Location: ' . $url);
   die("Redirect to <a href=\"$url\">$url</a> failed.");
}

/**
 * Prints out formatted verion of passed in variable.
 *
 * @param array $var Array to be formatted.
 */
function print_var($var) {
   echo '<pre>';
   echo print_r($var);
   echo '</pre>';
}

/**
 * Get a gallery with it's images from the database.
 *
 * @param link $db The link resource for the database connection
 * @param string $gallery mysql object of gallery
 *
 * @return array Associative array of the gallery with its images
 */
function get_gallery_with_images($db, $gallery) {
   // array containing gallery information
   $gallery = mysqli_fetch_assoc($gallery);

   // mysql object of gallery images
   $images_result = get_images($db, $gallery['id']);

   // initialize images array
   $images = [];
   // create array of images
   while ($row = $images_result->fetch_assoc()) {
      $images[] = $row;
   }

   // merge two arrays
   $gallery_with_images = array(
      'id' => $gallery['id'],
      'name' => $gallery['name'],
      'description' => $gallery['description'],
      'featured_image' => $gallery['featured_image'],
      'user_email' => $gallery['user_email'],
      'images' => $images
   );

   return $gallery_with_images;
}

/**
 * Format error message if file is too big.
 *
 * @param int $max_size Max file size in bytes
 *
 * @return string $error Formatted error message
 */
function max_size_error($max_size) {
   // file is too big
   $max_size_rounded = round($max_size / 1024);
   $error = "<p class=\"error\">
                         The file uploaded is too large,
                         please upload an image smaller
                         than $max_size_rounded KB.
                      </p>";
   return $error;
}

/**
 * Create path for new image file.
 *
 * @param string $filename Name of new file
 * @param string $folder_path Folder path to save new file in
 *
 * @return string Path to location of file to be created
 */
function create_final_location($filename, $folder_path) {
   // make filename random if set to true
   if (RANDOMIZE_FILENAME) {
      // unique hash for the filename
      $hash = sha1(microtime());
      // get the original extension
      $extension = explode('.', $filename);
      $extension = array_pop($extension);
      // combine it all together
      $final_location = $folder_path . "{$hash}.{$extension}";
   } else {
      $final_location = $folder_path . $filename;
   }
   return $final_location;
}
