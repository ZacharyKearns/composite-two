<?php
   $page_title = 'User';
   include('includes/templates/header.tpl.php');
   // If user exists in database set its
   // values to the row variable
   if (mysqli_num_rows($user) > 0):
   $row = mysqli_fetch_assoc($user);
   $name = $row['firstname'] . ' ' . $row['lastname'];
?>
<p class="error">
   <?php
      $message = 'Please verify your account to start uploading photos.';
      if ($row['email_hash'] != 'verified') { echo $message; }
   ?>
</p>
<div id="user-list">
   <div class="user" id="user-<?php echo $row['id']; ?>">
      <img class="user-image" src="images/user-images/large/<?php echo $row['user_image']; ?>" alt="<?php echo $name; ?>">
      <div class="user-info">
         <p class="name">
            <a href="#">
               <?php echo $name; ?>
            </a>
         </p>
         <?php $location = $row['locality'] . ', ' . $row['state'] . ', ' . $row['country']; ?>
         <p class="location"><?php echo $location; ?></p>
         <p class="about"><?php echo $row['about']; ?></p>
         <div class="email">
            <img src="images/misc/email.svg" alt="email">
            <span>
               <a href="mailto:<?php echo $row['email']; ?>">
                  <?php echo $row['email']; ?>
               </a>
            </span>
         </div>
      </div>
   </div>
</div>
<?php if (mysqli_num_rows($galleries) == 0): ?>
   <p class="center">No image galleries to show.</p>
<?php else: ?>
   <!-- <a class="edit-books center" href="/home?edit=show">Edit Books</a> -->
   <div class="grid" id="image-galleries">
      <div class="grid-sizer"></div>
      <?php while($row = mysqli_fetch_assoc($galleries)): ?>
      <div class="grid-item" id="gallery-<?php echo $row['id']; ?>">
         <?php if ($edit_buttons):
         $delete = $_SERVER['PHP_SELF'] . "?action=delete&amp;id={$row['id']}";
         $edit = $_SERVER['PHP_SELF'] . "?action=edit&amp;id={$row['id']}";
         ?>
            <a class="edit-books center" href="<?php echo $edit; ?>">Edit</a>
            <a class="edit-books center" href="<?php echo $delete; ?>">Delete</a>
         <?php endif ?>
         <?php $featured_image = "{$row['user_email']}/{$row['featured_image']}"; ?>
         <img src="images/user-galleries/<?php echo $featured_image; ?>" alt="featured image">
         <p class="title"><?php echo $row['title']; ?></p>
         <p class="description"><?php echo $row['description']; ?></p>
      </div>
      <?php endwhile ?>
   </div>
<?php endif ?>
<?php else: ?>
   <p class="center">User not found.</p>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>
