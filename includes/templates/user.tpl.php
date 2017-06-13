<?php
   $page_title = 'User';
   // If user exists in database set its
   // values to the row variable
   if (mysqli_num_rows($user) > 0):
   $row = mysqli_fetch_assoc($user);
   if (!$row['active']) {
      redirect('/');
   }
   $name = $row['firstname'] . ' ' . $row['lastname'];
   $email = $row['email'];
   include('includes/templates/header.tpl.php');
?>
<p class="error">
   <?php
      $message = 'Please verify your account to start uploading photos.';
      if ($row['email_hash'] != 'verified') { echo $message; }
   ?>
</p>
<div id="user-list">
   <div class="user" id="user-<?php echo $row['id']; ?>">
      <img class="user-image user-image-large" src="images/user-images/large/<?php echo $row['user_image']; ?>" alt="<?php echo $name; ?>">
      <div class="user-info">
         <p class="name">
            <a href="/user?email=<?php echo $email; ?>">
               <?php echo $name; ?>
            </a>
         </p>
         <?php $location = $row['locality'] . ', ' . $row['state'] . ', ' . $row['country']; ?>
         <p class="location"><?php echo $location; ?></p>
         <p class="about"><?php echo $row['about']; ?></p>
         <div class="email">
            <img src="images/misc/email.svg" alt="email">
            <span>
               <a href="mailto:<?php echo $email; ?>">
                  <?php echo $email; ?>
               </a>
            </span>
         </div>
      </div>
   </div>
</div>
<h2 class="center gallery-heading"><?php echo $row['firstname']; ?>'s Photo Galleries</h2>
<h3 class="center gallery-subheading">Click on a gallery to view the images</h3>
<?php if (mysqli_num_rows($galleries) == 0): ?>
   <p class="center">No image galleries to show.</p>
<?php else: ?>
   <!-- <a class="edit-books center" href="/home?edit=show">Edit Books</a> -->
   <div class="grid" id="image-galleries">
      <div class="grid-sizer"></div>
      <?php while($row = mysqli_fetch_assoc($galleries)): ?>
         <a href="/gallery?id=<?php echo $row['id']; ?>&amp;email=<?php echo $email; ?>">
            <div class="grid-item" id="gallery-<?php echo $row['id']; ?>">
               <?php $featured_image = "{$row['user_email']}/large/{$row['featured_image']}"; ?>
               <img src="images/user-galleries/<?php echo $featured_image; ?>" alt="featured image">
               <div class="overlay">
                  <div class="gallery-name"><?php echo $row['name']; ?></div>
                  <div class="gallery-description"><?php echo $row['description']; ?></div>
               </div>
            </div>
         </a>
      <?php endwhile ?>
   </div>
<?php endif ?>
<?php else: ?>
   <p class="center">User not found.</p>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>
