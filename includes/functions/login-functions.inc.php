<?php
/**
 * Determine if the current user is logged in,
 * and redirects them to login page if they are not.
 */
function check_login() {
   if (strcmp($_SESSION['login_token'], LOGGED_IN) != 0) {
      redirect('/login');
   }
}

/**
 * Determine if the current user is logged in,
 * and redirects them to the home page if they are.
 */
function check_logout() {
   if (strcmp($_SESSION['login_token'], LOGGED_IN) == 0) {
      redirect('/');
   }
}

/**
 * Conditionally render HTML based on
 * whether or not the user is logged in
 *
 * @return bool Returns true if the users is logged in
 * and false if not.
 */
function user_is_logged_in() {
   return strcmp($_SESSION['login_token'], LOGGED_IN) == 0 ? true : false;
}

/**
* Compare provided credentials with those in the
* database and logs the user in, or rejects them.
*
* @param resource $db The database connection resource.
* @param string $email The email of the user trying to log in.
* @param string $password The password of the user trying to log in.
*
* @return array An associative array of error messages generated.
*/
function log_in($db, $email, $password) {
   $errors = array();

   // email validation
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $errors['email'] = '<p class="error">
                                Please enter a valid email.
                             </p>';
   }

   // password validation
   if (strlen($password) < 1) {
      $errors['password'] = '<p class="error">
                             Please enter a valid password.
                             </p>';
   }

   if (count($errors) == 0) {

      $email = sanitize($db, $email);

      // query to select user information from the database
      $query = "SELECT id, email, firstname, user_image, password_hash FROM photopro_users WHERE email = '$email' LIMIT 1";

      // send query and wait for result
      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      // if user exists
      if (mysqli_num_rows($result) > 0) {
         // user was in the database
         $row = mysqli_fetch_assoc($result);
         // compare the encrypted version of the passwords
         if (password_verify($password, $row['password_hash'])) {
            // passwords match, log the user in
            // store login info in the session
            $_SESSION['login_token'] = LOGGED_IN;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_image'] = $row['user_image'];
            redirect("/user?email=$email");
         } else {
            $errors['password'] = '<p class="error">Incorrect password.</p>';
         }
      } else {
         // user was not found
         $errors['email'] = '<p class="error">No such email in the system.</p>';
      }

   }

   return $errors;
}

/**
 * Deletes the login session information and sends
 * the user back to the login page.
 */
function logout() {
   $_SESSION['login_token'] = null;
   $_SESSION['user_id'] = null;
   $_SESSION['firstname'] = null;
   $_SESSION['email'] = null;
   $_SESSION['user_image'] = null;
   unset($_SESSION['login_token']);
   unset($_SESSION['user_id']);
   unset($_SESSION['firstname']);
   unset($_SESSION['email']);
   unset($_SESSION['user_image']);
   session_destroy();
   redirect('/login');
}
