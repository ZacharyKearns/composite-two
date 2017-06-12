<?php
function check_login() {
   if (strcmp($_SESSION['login_token'], LOGGED_IN) != 0) {
      redirect('/login');
   }
}

function check_logout() {
   if (strcmp($_SESSION['login_token'], LOGGED_IN) == 0) {
      redirect('/');
   }
}

function user_is_logged_in() {
   return strcmp($_SESSION['login_token'], LOGGED_IN) == 0 ? true : false;
}

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

      $query = "SELECT id, email, firstname, user_image, password_hash FROM photopro_users WHERE email = '$email' LIMIT 1";

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
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_image'] = $row['user_image'];
            redirect("/editphotos?email=$email");
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
