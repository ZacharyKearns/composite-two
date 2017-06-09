<?php
/**
 * Sanitizes data for use in a mysqli query.
 *
 * @param link $db The link resource for the database connection
 * @param string $data Value to sanitize
 *
 * @return string Sanitized version of the data.
 */
function sanitize($db, $data) {
   $data = trim($data);
   $data = strip_tags($data);
   $data = mysqli_real_escape_string($db, $data);
   return $data;
}

/**
 * Redirect the browser to the given url, using a 301 redirect.
 *
 * @param string $url The address to redirect to.
 */
function redirect($url) {
   @header('Location: ' . $url);
   die("Redirect to <a href=\"$url\">$url</a> failed.");
}

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

      $query = "SELECT id, firstname, password_hash FROM photopro_users WHERE email = '$email' LIMIT 1";

      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if (mysqli_num_rows($result) > 0) {
         // user was in the database
         $row = mysqli_fetch_assoc($result);
         // compare the encrypted version of the passwords
         if (password_verify($password, $row['password_hash'])) {
            // passwords match, log the user in
            // store login info in the session
            $_SESSION['login_token'] = LOGGED_IN;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['firstname'];
            redirect('/editphotos');
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
   $_SESSION['username'] = null;
   unset($_SESSION['login_token']);
   unset($_SESSION['user_id']);
   unset($_SESSION['username']);
   session_destroy();
   redirect('/login');
}

/**
 * Create a new user account
 *
 * @param resource $db The database connection resource.
 * @param string $email The email of the user trying to sign up.
 *
 * @return array An associative array of error messages generated.
 */
function sign_up($db, $email, $password, $confirm_password) {
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
   } else if ($password != $confirm_password) {
      $errors['password'] = '<p class="error">
                             Passwords must match.
                             </p>';
   }

   if (count($errors) == 0) {
      $email = sanitize($db, $email);
      $query = "SELECT email FROM photopro_users WHERE email = '$email' LIMIT 1";
      $unique_email = mysqli_query($db, $query) or die(mysqli_error($db));

      if (mysqli_num_rows($unique_email) > 0) {
         $errors['email'] = '<p class="error">Email must be unique.</p>';
      } else {
         // create hash of email
         $email_hash = sha1($email . time());
         $password_hash = password_hash($password, PASSWORD_DEFAULT);

         $query = "INSERT INTO photopro_users(email, email_hash, password_hash)
                  VALUES('$email', '$email_hash', '$password_hash')";

         // send query to the db server and wait for result
         $result = mysqli_query($db, $query) or die(mysqli_error($db));

         // include PHPMailer, and stop PHP if it is not found
         require( 'PHPMailerAutoload.php' );

         // create an instance of PHPMailer
         $mail = new PHPMailer();

         // set destination address
         $mail->addAddress($email);

         // set the 'from' address
         $mail->setFrom('noreply@photopro.zacharykearns.ca');

         // set the subject line
         $mail->Subject = 'Verify Email Address.';

         // set up the HTML version of the email message
         $message  = '<h2>Please click this link to verify your email address.</h2>' .
                     '<a href=\"localhost:8888/?action=verify&hash=' .
                     $email_hash . '\" target=\"_blank\">Verify Email Address</a>';

         // set this email to use HTML (if the email client supports it)
         // vs plain text
         $mail->isHTML( true );

         // set the HTML message
         $mail->Body = $message;

         // set the plain text message
         $mail->AltBody = strip_tags( $message );

         if ($result == true && $mail->send()) {
            redirect("/emailsent?hash=$email_hash");
         } else {
            $errors['server'] = '<p class="error server">
                                    There was a problem sending
                                    your email, please contact the administrator.
                                 </p>';
         }
      }
   }

   return $errors;
}

/**
 * Verify Users Account
 *
 * @param resource $db The database connection resource.
 * @param string $hash A hash used to verify the user's account.
 */
 function verify_account($db, $hash) {
   $hash = sanitize($db, $hash);

   $query = "SELECT email_hash
             FROM photopro_users
             WHERE email_hash = '$hash'";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   if (mysqli_num_rows($result) > 0) {
      $query = "UPDATE photopro_users
                SET email_hash = 'verified'
                WHERE email_hash = '$hash'";

      $result = mysqli_query($db, $query) or die(mysqli_error($db));
      redirect('/editprofile');
   } else {
      redirect('/');
   }

}

/**
 * Retrieve list of books from the database.
 *
 * @param link $db The link resource for the database connection
 *
 * @return array Results of the database call
 */
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
             ORDER BY created_at DESC";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}
