<?php
$page_title = 'Edit Profile';
include('includes/templates/header.tpl.php'); ?>
   <?php
      // If user exists in database set its
      // values to the row variable
      if (mysqli_num_rows($result) > 0):
      $row = mysqli_fetch_assoc($result);
   ?>
   <div class="edit-form-container">
      <h1 class="form-heading">Edit Profile</h1>
      <p class="error">
         <?php
            $message = 'Please add your name and location to make your profile public';
            if (!$row['active']) { echo $message; }
         ?>
      </p>
      <img class="profile-image" src="images/user-images/large/<?php echo $row['user_image']; ?>" alt="user image">
      <h4 class="add-heading">Profile Image</h4>
      <!-- Form to update the users profile image -->
      <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
         <!-- hidden input to detect form submission -->
         <input type="hidden" name="submitted">
         <input type="hidden" name="email" value="<?php echo $row['email']; ?>" />
         <input type="hidden" name="old-image" value="<?php echo $row['user_image']; ?>" />
         <?php
            echo $errors['file'];
            echo $errors['size'];
            echo $errors['type'];
         ?>
         <div class="file-input-container">
            <input type="file" name="user-image">
            <span>
               Browse Images &hellip;
            </span>
         </div>
         <input type="submit" value="Upload">
      </form>
      <!-- Form to update the users information -->
      <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
         <?php echo $errors['email']; ?>
         <input type="hidden" name="email" value="<?php echo $row['email']; ?>" />
         <input id="locality" type="hidden" name="locality" value="<?php echo $row['locality']; ?>" />
         <input id="state" type="hidden" name="state" value="<?php echo $row['state']; ?>" />
         <input id="country" type="hidden" name="country" value="<?php echo $row['country']; ?>" />

         <?php echo $errors['firstname']; ?>
         <label class="label">First Name:</label>
         <input class="text-input" type="text" name="firstname" size="80" maxlength="140"
         value="<?php echo $row['firstname']; ?>">

         <?php echo $errors['lastname']; ?>
         <label class="label">Last Name:</label>
         <input class="text-input" type="text" name="lastname" size="80" maxlength="140"
         value="<?php echo $row['lastname']; ?>">

         <?php
            $location = $row['locality'] . ', ' . $row['state'] . ', ' . $row['country'];
            $location = strlen($location) < 7 ? '' : $location;
         ?>
         <p class="current-location">
            <span class="current-location-label">Current Location:</span>
            <?php echo $location; ?>
         </p>
         <p class="current-location" id="new-location">
            <span class="current-location-label">New Location:</span>
            <span data-geo="locality"></span>,
            <span data-geo="administrative_area_level_1"></span>,
            <span data-geo="country"></span>
         </p>

         <?php echo $errors['location']; ?>
         <label class="label">Search Locations:</label>
         <input id="geocomplete" type="text" placeholder="Type in an address" size="90" />

         <?php echo $errors['about']; ?>
         <label class="textarea-label">About Me:</label>
         <textarea class="textarea" type="text" name="about" rows="4" cols="80"><?php echo $row['about']; ?></textarea>

         <input type="submit" value="Save Changes">
      </form>
      <!-- Button to delete gallery -->
      <?php $delete_profile_link = "/delete-profile?email={$row['email']}"; ?>
      <a
      class="edit-gallery-btn delete-gallery-btn"
      href="<?php echo $delete_profile_link; ?>"
      onclick="confirm('This will delete your account and all your images. Are You Sure?')">
         Delete Account
      </a>
   </div>
   <script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo GOOGLE_MAPS_API_KEY; ?>"></script>
   <script src="js/jquery.geocomplete.min.js"></script>
   <script src="js/edit-profile-location.js"></script>
<?php else: ?>
   <p class="center">User not found.</p>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>
