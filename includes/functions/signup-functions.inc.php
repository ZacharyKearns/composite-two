<?php
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
      if ($result == true) {
         return true;
      } else {
         return false;
      }
   } else {
      return false;
   }

}
