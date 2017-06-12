<?php
switch($_GET['action']) {
   case 'home':
      $result = get_users($db);
   break;
   case 'user':
      if (isset($_GET['email'])) {
         $user = get_user($db, $_GET['email']);
         $galleries = get_galleries($db, $_GET['email']);
      } else {
         redirect('/');
      }
      $template = 'user.tpl.php';
   break;
   case 'editprofile':
      check_login();
      if (isset($_POST['submitted'])) {
         // Check if email belongs to user
         check_user_email($db, $_POST['email']);
         $errors = update_user_image(
            $db,
            $_POST['email'],
            $_POST['old-image']
         );
      } else if (isset($_POST['firstname'])) {
         // Check if email belongs to user
         check_user_email($db, $_POST['email']);
         $errors = update_user(
            $db,
            $_POST['email'],
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['about'],
            $_POST['locality'],
            $_POST['state'],
            $_POST['country']
         );
         $result = get_user($db, $_SESSION['user_id']);
      }
      if (isset($_GET['email'])) {
         // Check if book belongs to user
         check_user_email($db, $_GET['email']);
         $result = get_user($db, $_SESSION['user_id']);
      }
      $template = 'edit-profile.tpl.php';
   break;
   case 'login':
      // Hide page if user is logged in
      check_logout();
      if (isset($_POST['email'])) {
         $errors = log_in(
            $db,
            $_POST['email'],
            $_POST['password']
         );
      }
      $template = 'login.tpl.php';
   break;
   case 'logout':
      logout();
   break;
   case 'signup':
      // Hide page if user is logged in
      check_logout();
      $template = 'signup.tpl.php';
      if ( isset($_POST['email'])) {
         $errors = sign_up(
            $db,
            $_POST['email'],
            $_POST['password'],
            $_POST['confirm_password']
         );
      }
   break;
   case 'emailsent':
      // Hide page if user is logged in
      check_logout();
      $template = 'emailsent.tpl.php';
   break;
   case 'verify':
      if (isset($_GET['hash'])) {
         $verified = verify_account($db, $_GET['hash']);
         if ($verified) {
            $template = 'verify.tpl.php';
         } else {
            redirect('/');
         }
      } else {
         redirect('/');
      }
   break;
   case 'about':
      $template = 'about.tpl.php';
   break;
   case 'support':
      $template = 'support.tpl.php';
   break;
}
