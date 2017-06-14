<?php
   $page_title = 'Edit Galleries';
   include('includes/templates/header.tpl.php');
   $user_row = mysqli_fetch_assoc($user);
   // printVar($edit_table_arr);
?>
<h1 class="edit-heading center"><?php echo $user_row['firstname']; ?>'s Galleries</h1>
<?php
if (count($galleries) > 0):
   foreach($galleries as $gallery):
   $featured_image = "images/user-galleries/{$gallery['user_email']}/thumb/{$gallery['featured_image']}";
?>
      <div class="edit-container">
         <div class="edit-btn-container">
            <a class="edit-gallery-btn" href="/editgallery?id=<?php echo $gallery['id']; ?>">Edit</a>
         </div>
         <h3 class="edit-subheading">Gallery Name: <?php echo $gallery['name']; ?></h3>
         <p class="edit-gallery-p">Description: <?php echo $gallery['description']; ?></p>
         <p class="edit-gallery-p">
            Featured Image:
            <img class="edit-gallery-image" src=<?php echo $featured_image; ?> alt="featured image">
         </p>
      </div>
   <?php endforeach ?>
<?php else: ?>
   <p>No galleries to show.</p>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>
