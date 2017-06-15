<?php
   $page_title = 'Edit Galleries';
   include('includes/templates/header.tpl.php');
   $user_row = mysqli_fetch_assoc($user);
?>
<h1 class="edit-heading center"><?php echo $user_row['firstname']; ?>'s Galleries</h1>
<div class="edit-container">
   <p>Add A Gallery</p>
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

      <p>Featured Image:</p>

      <?php echo $errors['featured_image']; ?>
      <label class="label">Image Name:</label>
      <input class="text-input" type="text" name="featured_image" size="80" maxlength="140">

      <?php
         echo $errors['image_upload']['file'];
         echo $errors['image_upload']['size'];
         echo $errors['image_upload']['type'];
      ?>
      <input type="file" name="gallery-image">
      <input type="submit" value="Add Gallery">
   </form>
   <?php
   if (count($galleries) > 0):
      foreach($galleries as $gallery):
      $featured_image = "images/user-galleries/{$gallery['user_email']}/thumb/{$gallery['featured_image']}";
   ?>
         <div class="edit-btn-container">
            <a class="edit-gallery-btn" href="/editgallery?id=<?php echo $gallery['id']; ?>">Edit</a>
         </div>
         <h3 class="edit-subheading">Gallery Name: <?php echo $gallery['name']; ?></h3>
         <p class="edit-gallery-p">Description: <?php echo $gallery['description']; ?></p>
         <p class="edit-gallery-p">
            Featured Image:
            <img class="edit-gallery-image" src=<?php echo $featured_image; ?> alt="featured image">
         </p>
      <?php endforeach ?>
   <?php else: ?>
      <p>No galleries to show.</p>
   <?php endif ?>
</div>
<?php include('includes/templates/footer.tpl.php'); ?>
