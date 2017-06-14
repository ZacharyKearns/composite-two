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

function printVar($var) {
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
