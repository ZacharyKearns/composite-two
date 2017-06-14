<?php
   $page_title = 'Edit Gallery';
   include('includes/templates/header.tpl.php');
?>
<h1 class="center">Edit <?php echo $gallery['name']; ?></h1>
<div class="edit-container">
   <?php echo $errors['gallery_id']; ?>
   <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
      <input type="hidden" name="id" value="<?php echo $gallery['id']; ?>" />

      <?php echo $errors['name']; ?>
      <label class="label">Gallery Name:</label>
      <input class="text-input" type="text" name="name" size="80" maxlength="140"
      value="<?php echo $gallery['name']; ?>">

      <?php echo $errors['description']; ?>
      <label class="textarea-label">Gallery Description:</label>
      <textarea class="textarea" type="text" name="description" rows="4" cols="80"><?php echo $gallery['description']; ?></textarea>

      <input class="edit-gallery-btn" type="submit" value="Update Gallery Info">
   </form>
   <?php echo $errors['updated_image_name']; ?>
   <table class="edit-table">
      <thead>
         <th>Name</th>
         <th>Thumbnail</th>
         <th>Featured</th>
         <th>Delete</th>
      </thead>
      <tbody>
         <?php
            foreach($gallery['images'] as $image):
            $thumbnail = "images/user-galleries/{$gallery['user_email']}/thumb/{$image['filename']}";
         ?>
            <tr>
               <td>
                  <form class="update-name-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                     <input type="hidden" name="gallery_id" value="<?php echo $gallery['id']; ?>" />
                     <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>" />
                     <input type="text" name="updated_image_name" value="<?php echo $image['name']; ?>">
                     <input class="edit-gallery-btn update-name-btn" type="submit" value="Update Name">
                  </form>
               </td>
               <td><img class="edit-thumbnail" src=<?php echo $thumbnail; ?> alt="thumbnail"></td>
               <td>
                  <?php if ($image['filename'] == $gallery['featured_image']): ?>
                     <span>Current</span>
                  <?php else:
                     $set_featured = "/setfeatured?featured={$image['filename']}&id={$gallery['id']}"; ?>
                     <a href=<?php echo $set_featured; ?>>Set</a>
                  <?php endif ?>
               </td>
               <td><a href="#">Delete</a></td>
            </tr>
         <?php endforeach ?>
      </tbody>
   </table>
   <p>Add An Image</p>
   <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
      <!-- hidden input to detect form submission -->
      <input type="hidden" name="submitted">
      <input type="hidden" name="email" value="<?php echo $gallery['user_email']; ?>" />
      <input type="hidden" name="gallery_id" value="<?php echo $gallery['id']; ?>" />

      <?php echo $errors['image_name']; ?>
      <label class="label">Image Name:</label>
      <input class="text-input" type="text" name="image_name" size="80" maxlength="140">

      <?php
         echo $errors['add_image_gallery_id'];
         echo $errors['file'];
         echo $errors['size'];
         echo $errors['type'];
      ?>
      <input type="file" name="gallery-image">
      <input type="submit" value="Add Image">
   </form>
   <a class="edit-gallery-btn delete-gallery-btn" href="#">Delete Gallery</a>
</div>
<?php include('includes/templates/footer.tpl.php'); ?>
