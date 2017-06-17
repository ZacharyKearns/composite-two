<?php
   $page_title = 'Edit Gallery';
   include('includes/templates/header.tpl.php');
?>
<div class="edit-form-container">
   <h1 class="form-heading">Edit <?php echo $gallery['name']; ?></h1>
   <?php echo $errors['gallery_id']; ?>
   <!-- Form to update gallery information -->
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
   <!-- Output table of images -->
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
                     <input class="image-name-input" type="text" name="updated_image_name" value="<?php echo $image['name']; ?>">
                     <input class="edit-gallery-btn update-name-btn" type="submit" value="Update Name">
                  </form>
               </td>
               <td><img class="edit-thumbnail" src=<?php echo $thumbnail; ?> alt="thumbnail"></td>
               <td>
                  <?php if ($image['filename'] == $gallery['featured_image']): ?>
                     <span>Current</span>
                  <?php else:
                     // link to change featured image
                     $set_featured_link = "/setfeatured?featured={$image['filename']}&id={$gallery['id']}"; ?>
                     <a href=<?php echo $set_featured_link; ?>>Set</a>
                  <?php endif ?>
               </td>
               <!-- Link to delete image -->
               <?php $delete_link = "/delete-image?filename={$image['filename']}&id={$gallery['id']}"; ?>
               <td><a href=<?php echo $delete_link; ?>>Delete</a></td>
            </tr>
         <?php endforeach ?>
      </tbody>
   </table>
   <h4 class="add-heading">Add An Image</h4>
   <!-- Form to upload and image -->
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
      <div class="file-input-container">
         <input type="file" name="gallery-image">
         <span>
            Browse Images &hellip;
         </span>
      </div>
      <input type="submit" value="Add Image">
   </form>
   <!-- Button to delete gallery -->
   <?php $delete_gallery_link = "/delete-gallery?id={$gallery['id']}"; ?>
   <a
   class="edit-gallery-btn delete-gallery-btn"
   href="<?php echo $delete_gallery_link; ?>"
   onclick="confirm('This will delete all the images in this gallery as well. Are You Sure')">
      Delete Gallery
   </a>
</div>
<?php include('includes/templates/footer.tpl.php'); ?>
