<?php
switch($_GET['action']) {
   // show users on home page
   case 'home':
      $result = get_users($db);
   break;
   // show user and their photo galleries
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
         $result = get_user($db, $_SESSION['email']);
      }

      if (isset($_GET['email'])) {
         // Check if email belongs to user
         check_user_email($db, $_GET['email']);

         $result = get_user($db, $_GET['email']);
      } else {
         redirect('/');
      }

      $template = 'edit-profile.tpl.php';
   break;
   case 'delete-profile':
      check_login();
      // check if email belongs to user
      check_user_email($db, $_GET['email']);

      if (isset($_GET['email'])) {
         delete_user($db, $_GET['email']);
      } else {
         redirect('/');
      }
   break;
   case 'editgalleries':
      check_login();

      if (isset($_POST['submitted'])) {
         $errors = add_gallery(
            $db,
            $_POST['gallery_name'],
            $_POST['description'],
            $_POST['featured_image']
         );
      }

      if(isset($_GET['email'])) {
         // Check if email belongs to user
         check_user_email($db, $_GET['email']);

         $user = get_user($db, $_GET['email']);
         $galleries = get_galleries($db, $_GET['email']);
      } else {
         redirect('/');
      }

      $template = 'edit-galleries.tpl.php';
   break;
   case 'editgallery':
      check_login();

      if (isset($_POST['submitted'])) {
         // check if gallery belongs to user
         check_user_gallery($db, $_POST['gallery_id']);

         $errors = add_gallery_image(
            $db,
            $_POST['gallery_id'],
            $_POST['image_name']
         );
      } else if (isset($_POST['updated_image_name'])) {
         // check if gallery belongs to user
         check_user_gallery($db, $_POST['gallery_id']);

         $errors = update_image_name(
            $db,
            $_POST['image_id'],
            $_POST['updated_image_name'],
            $_POST['gallery_id']
         );
      } else if (isset($_POST['name'])) {
         // check if gallery belongs to user
         check_user_gallery($db, $_POST['id']);

         $errors = update_gallery(
            $db,
            $_POST['id'],
            $_POST['name'],
            $_POST['description']
         );
      }

      if (isset($_GET['id']) && is_numeric($_GET['id'])) {
         check_user_gallery($db, $_GET['id']);

         $temp_gallery = get_gallery($db, $_GET['id']);
         $gallery = get_gallery_with_images($db, $temp_gallery);
      } else {
         redirect('/');
      }

      $template = 'edit-gallery.tpl.php';
   break;
   case 'setfeatured':
      check_login();

      if (isset($_GET['featured']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
         // check if gallery belongs to user
         check_user_gallery($db, $_GET['id']);
         // check if image belongs to gallery
         check_gallery_image($db, $_GET['featured'], $_GET['id']);

         set_featured_image(
            $db,
            $_GET['featured'],
            $_GET['id']
         );
      } else {
         redirect('/');
      }
   break;
   case 'delete-image':
      check_login();

      if (isset($_GET['filename']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
         // check if gallery belongs to user
         check_user_gallery($db, $_GET['id']);
         // check if image belongs to gallery
         check_gallery_image($db, $_GET['filename'], $_GET['id']);

         delete_image(
            $db,
            $_GET['filename'],
            $_GET['id']
         );
      } else {
         redirect('/');
      }
   break;
   case 'delete-gallery':
      check_login();

      if (isset($_GET['id']) && is_numeric($_GET['id'])) {
         // check if gallery belongs to user
         check_user_gallery($db, $_GET['id']);

         delete_gallery(
            $db,
            $_GET['id'],
            false
         );
      } else {
         redirect('/');
      }
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

      if ( isset($_POST['email'])) {
         $errors = sign_up(
            $db,
            $_POST['email'],
            $_POST['password'],
            $_POST['confirm_password']
         );
      }

      $template = 'signup.tpl.php';
   break;
   case 'emailsent':
      // Hide page if user is logged in
      check_logout();

      $template = 'emailsent.tpl.php';
   break;
   case 'verify':
      if (isset($_GET['hash'])) {
         // sets $verified to true if hash is good
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
   case 'gallery':
      if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['email'])) {
         $user = get_user($db, $_GET['email']);
         $gallery = get_gallery($db, $_GET['id']);
         $images = get_images($db, $_GET['id']);
      } else {
         redirect('/');
      }

      $template = 'gallery.tpl.php';
   break;
   case 'about':
      $template = 'about.tpl.php';
   break;
   case 'support':
      $template = 'support.tpl.php';
   break;
}
