<?php
switch($_GET['action']) {
   case 'home':
      $result = get_users($db);
   break;
   case 'editphotos':
      check_login();
      $template = 'edit-photos.tpl.php';
   break;
   case 'editprofile':
      if (isset($_SESSION['activate_account'])) {
         $template = 'edit-profile.tpl.php';
      } else {
         check_login();
         $template = 'edit-profile.tpl.php';
      }
   break;
   case 'login':
      // Hide page if user is logged in
      check_logout();
      $template = 'login.tpl.php';
      if ( isset($_POST['email'])) {
         $errors = log_in(
            $db,
            $_POST['email'],
            $_POST['password']
         );
      }
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
      // Hide page if user is logged in
      check_logout();
      if (isset($_GET['hash'])) {
         verify_account($db, $_GET['hash']);
      } else {
         redirect('/lalala');
      }
   case 'about':
      $template = 'about.tpl.php';
   break;
   case 'support':
      $template = 'support.tpl.php';
   break;
}
