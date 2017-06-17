<?php
   $page_title = 'Edit Galleries';
   include('includes/templates/header.tpl.php');
   // user information
   $user_row = mysqli_fetch_assoc($user);
?>
<div class="edit-form-container">
   <h4 class="add-heading">Add A Gallery</h4>
   <form id="add-gallery" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
      <!-- hidden input to detect form submission -->
      <input type="hidden" name="submitted">
      <input type="hidden" name="email" value="<?php echo $user_row['email']; ?>" />

      <?php echo $errors['gallery_name']; ?>
      <label class="label">Gallery Name:</label>
      <input class="text-input" type="text" name="gallery_name" size="80" maxlength="140">

      <?php echo $errors['description']; ?>
      <label class="textarea-label">Gallery Description:</label>
      <textarea class="textarea" type="text" name="description" rows="4" cols="80"></textarea>

      <h4 class="add-heading">Add A Featured Image</h4>

      <?php echo $errors['featured_image']; ?>
      <label class="label">Image Name:</label>
      <input class="text-input" type="text" name="featured_image" size="80" maxlength="140">

      <?php
         echo $errors['image_upload']['file'];
         echo $errors['image_upload']['size'];
         echo $errors['image_upload']['type'];
      ?>
      <div class="file-input-container">
         <input type="file" name="gallery-image">
         <span>
            Browse Images &hellip;
         </span>
      </div>
      <input type="submit" value="Add Gallery">
   </form>
</div>
<div class="galleries-container">
   <h1 class="edit-heading center"><?php echo $user_row['firstname']; ?>'s Galleries</h1>
   <?php
   if (mysqli_num_rows($galleries) > 0):
      foreach($galleries as $gallery):
      $featured_image = "images/user-galleries/{$gallery['user_email']}/thumb/{$gallery['featured_image']}";
   ?>
         <div class="gallery-item">
            <div class="edit-btn-container fl">
               <a class="edit-gallery-btn" href="/editgallery?id=<?php echo $gallery['id']; ?>">Edit</a>
            </div>
            <h3 class="edit-subheading">Gallery Name: <?php echo $gallery['name']; ?></h3>
            <h4 class="edit-gallery-heading">Description</h4>
            <p class="edit-gallery-p"><?php echo $gallery['description']; ?></p>
            <h4 class="edit-gallery-heading">Featured Image</h4>
            <img class="edit-gallery-image" src=<?php echo $featured_image; ?> alt="featured image">
         </div>
      <?php endforeach ?>
   <?php else: ?>
      <p class="center">No galleries to show.</p>
   <?php endif ?>
</div>
<?php include('includes/templates/footer.tpl.php'); ?>
