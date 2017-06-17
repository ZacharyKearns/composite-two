<?php
   $page_title = 'Home';
   include('includes/templates/header.tpl.php');
?>
<div class="users-container">
   <h1 id="users-heading">Find Professional Photographers In Your City</h1>
   <?php while($row = mysqli_fetch_assoc($result)):
   $name = $row['firstname'] . ' ' . $row['lastname']; ?>
   <div class="user-profile" id="user-<?php echo $row['id']; ?>">
      <img class="user-image user-image-medium" src="images/user-images/medium/<?php echo $row['user_image']; ?>" alt="<?php echo $name; ?>">
      <div class="user-info">
         <p class="name">
            <a href="/user?email=<?php echo $row['email']; ?>">
               <?php echo $name; ?>
            </a>
         </p>
         <?php $location = $row['locality'] . ', ' . $row['state'] . ', ' . $row['country']; ?>
         <p class="location"><?php echo $location; ?></p>
         <p class="about-p"><?php echo $row['about']; ?></p>
         <div class="email">
            <img src="images/misc/email.svg" alt="email">
            <span>
               <a href="mailto:<?php echo $row['email']; ?>">
                  Email <?php echo $row['firstname']; ?>
               </a>
            </span>
         </div>
      </div>
   </div>
   <?php endwhile ?>
</div>
<?php include('includes/templates/footer.tpl.php'); ?>
