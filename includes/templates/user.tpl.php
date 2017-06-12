<?php
   $page_title = 'User';
   include('includes/templates/header.tpl.php');
   // If user exists in database set its
   // values to the row variable
   if (mysqli_num_rows($result) > 0):
   $row = mysqli_fetch_assoc($result);
?>
<p class="error">
   <?php
      $message = 'Please verify your account to start uploading photos.';
      if ($row['email_hash'] != 'verified') { echo $message; }
   ?>
</p>
<?php else: ?>
   <p class="center">User not found.</p>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>
