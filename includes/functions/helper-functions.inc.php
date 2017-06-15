<?php
function sanitize($db, $data) {
   $data = trim($data);
   $data = strip_tags($data);
   $data = mysqli_real_escape_string($db, $data);
   return $data;
}

function redirect($url) {
   @header('Location: ' . $url);
   die("Redirect to <a href=\"$url\">$url</a> failed.");
}

function print_var($var) {
   echo '<pre>';
   echo print_r($var);
   echo '</pre>';
}

function get_gallery_with_images($db, $gallery) {
   $gallery = mysqli_fetch_assoc($gallery);

      $images = get_images($db, $gallery['id']);
      $images = mysqli_fetch_all($images, MYSQLI_ASSOC);
      $gallery_with_images = array(
         'id' => $gallery['id'],
         'name' => $gallery['name'],
         'description' => $gallery['description'],
         'featured_image' => $gallery['featured_image'],
         'user_email' => $gallery['user_email'],
         'images' => $images
      );

   return $gallery_with_images;

   return $gallery;
}

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

function create_final_location($filename, $folder_path) {
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
