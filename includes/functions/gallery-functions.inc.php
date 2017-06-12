<?php
function get_galleries($db, $email) {
   // set up query to fetch book list
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
