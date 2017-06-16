<?php
/**
 * Retrieve list of users from the database.
 *
 * @param link $db The link resource for the database connection
 *
 * @return array Results of the database call
 */
function get_users($db) {
   // set up query to fetch user list
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

/**
 * Retrieve user from the database.
 *
 * @param link $db The link resource for the database connection
 * @param int $user_id Id of the user being retrieved from the database
 *
 * @return array Results of the database call
 */
function get_user($db, $email) {
   // set up query to fetch user
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

/**
 * Updates user information.
 *
 * @param link $db The link resource for the database connection
 * @param string $email User's email
 * @param string $firstname User's firstname
 * @param string $lastname User's lastname
 * @param string $about User's about section
 * @param string $locality User's locality
 * @param string $state User's state
 * @param string $country User's country
 *
 * @return array Associative array of error messages generated
 */
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

   // Firstname field is empty
   if (strlen(trim($firstname)) < 1) {
      $errors['firstname'] = '<p class="error">
                                 Please enter a first name.
                              </p>';
   }

   // Lastname field is empty
   if (strlen(trim($lastname)) < 1) {
      $errors['lastname'] = '<p class="error">
                                 Please enter a last name.
                              </p>';
   }

   // Location is incomplete
   if (
      strlen(trim($locality)) < 1 ||
      strlen(trim($state)) < 1 ||
      strlen(trim($country)) < 1
   ) {
      $errors['location'] = '<p class="error">
                                 Please be more specific.
                              </p>';
   }

   if (count($errors) == 0) {
      $firstname = sanitize($db, $firstname);
      $lastname = sanitize($db, $lastname);
      $locality = sanitize($db, $locality);
      $state = sanitize($db, $state);
      $country = sanitize($db, $country);
      $about = sanitize($db, $about);
      $email = sanitize($db, $email);

      // query to update user information
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
         // updates users session to reflect changes
         $_SESSION['firstname'] = $firstname;
         // folder paths for resized gallery images
         $large_folder_path = "images/user-galleries/$email/large/";
         $thumb_folder_path = "images/user-galleries/$email/thumb/";
         // create folders if they do not exist yet
         if (!file_exists($large_folder_path) && !file_exists($thumb_folder_path)) {
            mkdir($large_folder_path, 0777, true);
            mkdir($thumb_folder_path, 0777, true);
         }
         // redirect back to the edit page
         redirect("/editprofile?email=$email");
      }
   }

   return $errors;
}

/**
 * Make sure email matches logged in users email.
 *
 * @param link $db The link resource for the database connection
 * @param string $email Email being passed to router
 *
 * @return bool Returns false
 */
function check_user_email($db, $email) {
   // set up query to fetch email
   $query = "SELECT
                email
             FROM photopro_users
             WHERE email = '$email'
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   // array of user information
   $row = mysqli_fetch_assoc($result);

   // redirect to home if emails do not match
   if ($row['email'] != $_SESSION['email']) {
      redirect('/');
   }

   return false;
}
