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

function get_gallery($db, $email) {
   $query = "SELECT
                id,
                name,
                description,
                featured_image,
                user_email
             FROM photopro_galleries
             WHERE user_email = '$email'";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

function get_galleries_with_images($db, $email) {

   $query = "SELECT
                photopro_images.id AS image_id,
                photopro_galleries.name gallery_name,
                photopro_images.name AS image_name,
                description as gallery_description,
                filename as image_filename,
                user_email
             FROM photopro_images
             INNER JOIN photopro_galleries
             ON photopro_galleries.id = photopro_images.gallery_id
             WHERE user_email = '$email'";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}
