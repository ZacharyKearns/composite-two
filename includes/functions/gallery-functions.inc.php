<?php
function get_galleries($db, $email) {
   // set up query to fetch galleries
   $query = "SELECT
                id,
                name,
                description,
                featured_image,
                user_email
             FROM photopro_galleries
             WHERE user_email = '$email'
             ORDER BY created_at DESC";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

function get_gallery($db, $id) {
   $query = "SELECT
                id,
                name,
                description,
                featured_image,
                user_email
             FROM photopro_galleries
             WHERE id = $id
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

function check_user_gallery($db, $id) {

   if (intval($id) < 1) {
      redirect('/');
   }

   // set up query to fetch book list
   $query = "SELECT user_email
             FROM photopro_galleries
             WHERE id = $id
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   $row = mysqli_fetch_assoc($result);

   if ($row['user_email'] != $_SESSION['email']) {
      redirect('/');
   }

   return false;
}

function update_gallery($db, $id, $name, $description) {
   $errors = array();

   if (strlen(trim($name)) < 1) {
      $errors['name'] = '<p class="error">
                                 Please enter a gallery name.
                              </p>';
   }

   if (strlen(trim($description)) < 1) {
      $errors['description'] = '<p class="error">
                                 Please enter a gallery description.
                              </p>';
   }

   if (intval($id) < 1) {
      $errors['gallery_id'] = '<p class="error">
                                  Gallery Id is not valid.
                               </p>';
   }

   if (count($errors) == 0) {
      $name = sanitize($db, $name);
      $description = sanitize($db, $description);

      $query = "UPDATE photopro_galleries
                SET
                 name = '$name',
                 description = '$description'
                WHERE id = $id";

      // send query to the db server and wait for result
      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if ($result == true) {
         redirect("/editgallery?id=$id");
      }
   }

   return $errors;
}

function check_gallery_image($db, $image_name, $gallery_id) {

   if (intval($gallery_id) < 1 || strlen(trim($image_name)) < 1) {
      redirect('/');
   }

   // set up query to fetch book list
   $query = "SELECT
               gallery_id
             FROM photopro_images
             WHERE name = '$image_name'
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   $row = mysqli_fetch_assoc($result);

   if ($row['gallery_id'] != $gallery_id) {
      redirect('/');
   }

   return false;
}

function set_featured_image($db, $featured_image, $gallery_id) {
   $featured_image = sanitize($db, $featured_image);
   $gallery_id = sanitize($db, $gallery_id);

   $query = "UPDATE photopro_galleries
             SET featured_image = '$featured_image'
             WHERE id = $gallery_id";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   if ($result == true) {
      redirect("/editgallery?id=$gallery_id");
   }
}
