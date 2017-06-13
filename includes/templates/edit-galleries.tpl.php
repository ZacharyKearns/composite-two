<?php
   $page_title = 'Edit Galleries';
   include('includes/templates/header.tpl.php');
   $user_row = mysqli_fetch_assoc($user);
   // printVar($edit_table_arr);
?>
<h1 class="edit-heading center"><?php echo $user_row['firstname']; ?>'s Galleries</h1>
<?php
if (count($edit_table_arr) > 0):
   foreach($edit_table_arr as $gallery):
   $featured_image = "images/user-galleries/{$gallery['user_email']}/thumb/{$gallery['featured_image']}";
?>
      <div class="edit-container">
         <h3 class="edit-subheading">Gallery Name: <?php echo $gallery['name']; ?></h3>
         <div class="edit-btn-container">
            <a class="edit-gallery-btn" href="#">Edit</a>
            <a class="edit-gallery-btn" href="#">Delete</a>
         </div>
         <p class="edit-gallery-p">Description: <?php echo $gallery['description']; ?></p>
         <p class="edit-gallery-p">
            Featured Image:
            <img class="edit-gallery-image" src=<?php echo $featured_image; ?> alt="featured image">
         </p>

         <table class="edit-table">
            <thead>
               <th>Name</th>
               <th>Thumbnail</th>
               <th>Edit</th>
               <th>Delete</th>
            </thead>
            <tbody>
               <?php
                  foreach($gallery['images'] as $image):
                  $thumbnail = "images/user-galleries/{$gallery['user_email']}/thumb/{$image['filename']}";
               ?>
                  <tr>
                     <td><?php echo $image['name']; ?></td>
                     <td><img class="edit-thumbnail" src=<?php echo $thumbnail; ?> alt="thumbnail"></td>
                     <td><a href="#">Edit</a></td>
                     <td><a href="#">Delete</a></td>
                  </tr>
               <?php endforeach ?>
            </tbody>
         </table>
      </div>
   <?php endforeach ?>
<?php else: ?>
   <p>No galleries to show.</p>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>
