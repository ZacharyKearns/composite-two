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
