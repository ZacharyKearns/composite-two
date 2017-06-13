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

function check_user_email($db, $email) {
   // set up query to fetch book list
   $query = "SELECT
                email
             FROM photopro_users
             WHERE email = '$email'
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   $row = mysqli_fetch_assoc($result);

   if ($row['email'] != $_SESSION['email']) {
      redirect('/');
   }

   return false;
}

function printVar($var) {
   echo '<pre>';
   echo print_r($var);
   echo '</pre>';
}

function get_edit_table_arr($db, $galleries) {
   $galleries = mysqli_fetch_all($galleries, MYSQLI_ASSOC);
   $galleries_with_images = array();

   foreach($galleries as $gallery) {
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
      array_push($galleries_with_images, $gallery_with_images);
   }
   return $galleries_with_images;
}
