<?php
function get_users($db) {
   // set up query to fetch book list
   $query = "SELECT
                id,
                firstname,
                lastname,
                email,
                user_image,
                about,
                locality,
                state,
                country
             FROM photopro_users
             WHERE active = 1
             ORDER BY created_at DESC";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

function get_user($db, $email) {
   // set up query to fetch book list
   $query = "SELECT
                email,
                email_hash,
                active,
                firstname,
                lastname,
                user_image,
                about,
                locality,
                state,
                country
             FROM photopro_users
             WHERE email = '$email'
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

function update_user(
   $db,
   $email,
   $firstname,
   $lastname,
   $about,
   $locality,
   $state,
   $country
) {
   $errors = array();

   if (strlen(trim($firstname)) < 1) {
      $errors['firstname'] = '<p class="error">
                                 Please enter a first name.
                              </p>';
   }

   if (strlen(trim($lastname)) < 1) {
      $errors['lastname'] = '<p class="error">
                                 Please enter a last name.
                              </p>';
   }

   if (
      strlen(trim($locality)) < 1 ||
      strlen(trim($state)) < 1 ||
      strlen(trim($country)) < 1
   ) {
      $errors['location'] = '<p class="error">
                                 Please be more specific.
                              </p>';
   }

   // if (filter_var($image_url, FILTER_VALIDATE_URL) === FALSE) {
   //    $errors['image_url'] = '<p class="error">
   //                               Please enter a valid image url.
   //                            </p>';
   // }

   // if (intval($id) < 1) {
   //    $errors['id'] = '<p class="error">
   //                        Book Id is not valid.
   //                     </p>';
   // }

   if (count($errors) == 0) {
      $firstname = sanitize($db, $firstname);
      $lastname = sanitize($db, $lastname);
      $locality = sanitize($db, $locality);
      $state = sanitize($db, $state);
      $country = sanitize($db, $country);
      $about = sanitize($db, $about);
      $email = sanitize($db, $email);

      $query = "UPDATE photopro_users
                SET
                 firstname = '$firstname',
                 lastname = '$lastname',
                 locality = '$locality',
                 state = '$state',
                 country = '$country',
                 about = '$about',
                 active = 1
                WHERE email = '$email'";

      // send query to the db server and wait for result
      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if ($result == true) {
         $_SESSION['firstname'] = $firstname;
         $large_folder_path = "images/user-galleries/$email/large/";
         $thumb_folder_path = "images/user-galleries/$email/thumb/";
         if (!file_exists($large_folder_path) && !file_exists($thumb_folder_path)) {
            mkdir($large_folder_path, 0777, true);
            mkdir($thumb_folder_path, 0777, true);
         }
         redirect("/editprofile?email=$email");
      }
   }

   return $errors;
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
