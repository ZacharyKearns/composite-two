<?php

/**
 * Retrieve list of users galleries
 *
 * @param link $db The link resource for the database connection
 * @param string $email The email of the user
 *
 * @return array Results of the database call
 */
function get_galleries($db, $email) {
   // set up query to fetch galleries
   $query = "SELECT
                id,
                name,
                description,
                featured_image,
                user_email
             FROM photopro_galleries
             WHERE user_email = '$email'
             ORDER BY created_at DESC";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

/**
 * Retrieve list of users galleries
 *
 * @param link $db The link resource for the database connection
 * @param int $id The id of the user
 *
 * @return array Results of the database call
 */
function get_gallery($db, $id) {
   // set up query to fetch a gallery
   $query = "SELECT
                id,
                name,
                description,
                featured_image,
                user_email
             FROM photopro_galleries
             WHERE id = $id
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

/**
 * Add a gallery to the database.
 *
 * @param link $db The link resource for the database connection
 * @param string $gallery_name Name of new gallery
 * @param string $description Description of new gallery
 * @param string $featured_image Name of featured image
 *
 * @return array Associative array of error messages generated
 */
function add_gallery(
   $db,
   $gallery_name,
   $description,
   $featured_image
) {
   $errors = array();

   // Gallery name field is empty
   if (strlen(trim($gallery_name)) < 1) {
      $errors['gallery_name'] = '<p class="error">
                                    Please enter a gallery name.
                                 </p>';
   }

   // Description field is empty
   if (strlen(trim($description)) < 1) {
      $errors['description'] = '<p class="error">
                                 Please enter a description.
                              </p>';
   }

   // Featured image field is empty
   if (strlen(trim($featured_image)) < 1) {
      $errors['featured_image'] = '<p class="error">
                                      Please enter an image name.
                                   </p>';
   }

   $user_email = $_SESSION['email'];

   // Folder path for user's images
   $user_folder = USER_GALLERIES_FOLDER . "$user_email/";

   // check if there is a filename submitted
   if (strlen($_FILES['gallery-image']['name']) > 0) {

      $temp_location = $_FILES['gallery-image']['tmp_name'];

      if (
         $_FILES['gallery-image']['size'] > MAX_FILE_SIZE ||
         $_FILES['gallery-image']['error'] == UPLOAD_ERR_INI_SIZE
      ) {
         $errors['size'] = max_size_error(MAX_FILE_SIZE);
      }

      $info = getimagesize($temp_location);
      if (!$info || strpos(ALLOWED_FILE_TYPES, $info['mime']) === false) {
         // file is either corrupted or not the correct type of file
         $error = '<p class="error">
                      The file is either corrupted or not one of the
                      allowed types (JPEG, GIF, or PNG)
                   </p>';
      }

      if (count($errors) == 0) {
         $gallery_name = sanitize($db, $gallery_name);
         $description = sanitize($db, $description);
         $featured_image = sanitize($db, $featured_image);
         $final_location = create_final_location($_FILES['gallery-image']['name'], $user_folder);


         if (move_uploaded_file($temp_location, $final_location)) {
            // file was moved OK

            // Folder paths for different images sizes
            $user_folder_large = $user_folder . "large/";
            $user_folder_thumb = $user_folder . "thumb/";

            resize_image($final_location, $user_folder_large, 600);
            resize_image($final_location, $user_folder_thumb, 100);

            // insert into the database

            // get the filename on its own
            $filename = explode('/', $final_location);
            $filename = array_pop($filename);

            // set up query to add the gallery details
            $add_gallery_query = "INSERT INTO photopro_galleries(
                         name,
                         description,
                         featured_image,
                         user_email
                      )
                      VALUES(
                         '$gallery_name',
                         '$description',
                         '$filename',
                         '$user_email'
                      )";

            // send query to the db server and wait for result
            $add_gallery_result = mysqli_query($db, $add_gallery_query) or die(mysqli_error($db));

            // retrieve msql object of all the galleries
            $galleries_result = get_galleries($db, $_SESSION['email']);

            // create an associative array with gallery information
            $galleries = [];
            while ($row = $galleries_result->fetch_assoc()) {
               $galleries[] = $row;
            }

            // save newly created gallery into $gallery
            $gallery = $galleries[0];
            $id = $gallery['id'];

            // query to insert image details in the images table
            $add_image_query = "INSERT INTO photopro_images(name, filename, gallery_id)
                      VALUES('$featured_image', '$filename', $id)";

            // true if successful
            $add_image_result = mysqli_query($db, $add_image_query) or die(mysqli_error($db));

            if ($add_image_result && $add_gallery_result) {
               // delete file created in user folder
               unlink($user_folder . $filename);
               // go back to edit page
               redirect("/editgallery?id=$id");
            }
         } else {
            // could not move file
            $errors['file'] = '<p class="error">
                                  Upload failed. Please try again.
                               </p>';
         }
      }
   } else {
      $errors['file'] = '<p class="error">
                            Please select an image to upload.
                         </p>';
   }
   return $errors;
}

/**
 * Delete a gallery.
 *
 * @param link $db The link resource for the database connection
 * @param int $id Id of the gallery to be deleted
 */
function delete_gallery($db, $id) {
   $id = sanitize($db, $id);

   // get mysql object of gallery
   $gallery = get_gallery($db, $id);

   // associative array of the gallery with its images
   $gallery = get_gallery_with_images($db, $gallery);

   // images associated with the gallery
   $images = $gallery['images'];

   // queries to delete the gallery and its images
   $delete_gallery_query = "DELETE FROM photopro_galleries WHERE id = $id LIMIT 1";
   $delete_images_query = "DELETE FROM photopro_images WHERE gallery_id = $id";

   // send queries to the db server and wait for result
   $delete_gallery_result = mysqli_query($db, $delete_gallery_query) or die(mysqli_error($db));
   $delete_images_result = mysqli_query($db, $delete_images_query) or die(mysqli_error($db));

   if ($delete_gallery_result && $delete_images_result) {
      // iterate over images array and delete the images
      for ($i = 0; $i < count($images); $i++) {
         unlink(USER_GALLERIES_FOLDER . $_SESSION['email'] . "/large/{$images[$i]['filename']}");
         unlink(USER_GALLERIES_FOLDER . $_SESSION['email'] . "/thumb/{$images[$i]['filename']}");
      }
      // redirect back to edit page
      redirect("/editgalleries?email={$gallery['user_email']}");
   } else {
      redirect('/');
   }
}

/**
 * Makes sure gallery belongs to logged in user.
 *
 * @param link $db The link resource for the database connection
 * @param int $id The id of the gallery
 *
 * @return false
 */
function check_user_gallery($db, $id) {

   // id is not a number
   if (intval($id) < 1) {
      redirect('/');
   }

   // query to fetch gallery from the database
   $query = "SELECT user_email
             FROM photopro_galleries
             WHERE id = $id
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   // associate array of gallery information
   $row = mysqli_fetch_assoc($result);

   // redirect to the home page if the email
   // associated with the gallery does not match
   // the logged in users email
   if ($row['user_email'] != $_SESSION['email']) {
      redirect('/');
   }

   return false;
}

/**
 * Updates a gallery.
 *
 * @param link $db The link resource for the database connection
 * @param int $id ID of the gallery to update
 * @param string $name Name of the gallery
 * @param string $description Description of the gallery
 *
 * @return array Associative array of error messages generated
 */
function update_gallery($db, $id, $name, $description) {
   $errors = array();

   // Gallery name field is empty
   if (strlen(trim($name)) < 1) {
      $errors['name'] = '<p class="error">
                                 Please enter a gallery name.
                              </p>';
   }

   // Gallery description field is empty
   if (strlen(trim($description)) < 1) {
      $errors['description'] = '<p class="error">
                                 Please enter a gallery description.
                              </p>';
   }

   // ID is not a number
   if (intval($id) < 1) {
      $errors['gallery_id'] = '<p class="error">
                                  Gallery Id is not valid.
                               </p>';
   }

   if (count($errors) == 0) {
      $name = sanitize($db, $name);
      $description = sanitize($db, $description);

      // Query to update gallery details
      $query = "UPDATE photopro_galleries
                SET
                 name = '$name',
                 description = '$description'
                WHERE id = $id";

      // send query to the db server and wait for result
      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if ($result == true) {
         // redirect back to edit page
         redirect("/editgallery?id=$id");
      }
   }

   return $errors;
}

/**
 * Check if image belongs to gallery.
 *
 * @param link $db The link resource for the database connection
 * @param string $filename Unique name of file
 * @param int $gallery_id ID of the gallery being checked
 *
 * @return false
 */
function check_gallery_image($db, $filename, $gallery_id) {

   // Redirect to home if data is invalid
   if (intval($gallery_id) < 1 || strlen(trim($filename)) < 1) {
      redirect('/');
   }

   // Query to retrieve image information
   $query = "SELECT
               gallery_id
             FROM photopro_images
             WHERE filename = '$filename'
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   // associative array of image information
   $row = mysqli_fetch_assoc($result);

   // Redirect to home page if the gallery ID
   // of the image does not match the gallery
   // ID passed in
   if ($row['gallery_id'] != $gallery_id) {
      redirect('/');
   }

   return false;
}

/**
 * Set the featured image of the gallery.
 *
 * @param link $db The link resource for the database connection
 * @param string $featured_image Filename of the image to be featured
 * @param int $gallery_id ID of the gallery being updated
 */
function set_featured_image($db, $featured_image, $gallery_id) {
   $featured_image = sanitize($db, $featured_image);
   $gallery_id = sanitize($db, $gallery_id);

   // query to update featured image
   $query = "UPDATE photopro_galleries
             SET featured_image = '$featured_image'
             WHERE id = $gallery_id";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   if ($result == true) {
      // redirect to edit page if image updates
      redirect("/editgallery?id=$gallery_id");
   } else {
      redirect('/');
   }
}
