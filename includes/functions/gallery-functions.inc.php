<?php
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

function get_gallery($db, $id) {
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

function add_gallery(
   $db,
   $gallery_name,
   $description,
   $featured_image
) {
   $errors = array();

   if (strlen(trim($gallery_name)) < 1) {
      $errors['gallery_name'] = '<p class="error">
                                    Please enter a gallery name.
                                 </p>';
   }

   if (strlen(trim($description)) < 1) {
      $errors['description'] = '<p class="error">
                                 Please enter a description.
                              </p>';
   }

   if (strlen(trim($featured_image)) < 1) {
      $errors['featured_image'] = '<p class="error">
                                      Please enter an image name.
                                   </p>';
   }

   $user_email = $_SESSION['email'];
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
            $user_folder_large = $user_folder . "large/";
            $user_folder_thumb = $user_folder . "thumb/";

            resize_image($final_location, $user_folder_large, 600);
            resize_image($final_location,$user_folder_thumb,100);

            // insert into the database

            // get the filename on its own
            $filename = explode('/', $final_location);
            $filename = array_pop($filename);

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

            $add_gallery_result = mysqli_query($db, $add_gallery_query) or die(mysqli_error($db));

            $galleries = get_galleries($db, $_SESSION['email']);
            $galleries = mysqli_fetch_all($galleries, MYSQLI_ASSOC);
            $gallery = $galleries[0];
            $id = $gallery['id'];

            $add_image_query = "INSERT INTO photopro_images(name, filename, gallery_id)
                      VALUES('$featured_image', '$filename', $id)";

            $add_image_result = mysqli_query($db, $add_image_query) or die(mysqli_error($db));

            if ($add_image_result && $add_gallery_result) {
               unlink($user_folder . $filename);
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

function check_user_gallery($db, $id) {

   if (intval($id) < 1) {
      redirect('/');
   }

   // set up query to fetch book list
   $query = "SELECT user_email
             FROM photopro_galleries
             WHERE id = $id
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   $row = mysqli_fetch_assoc($result);

   if ($row['user_email'] != $_SESSION['email']) {
      redirect('/');
   }

   return false;
}

function update_gallery($db, $id, $name, $description) {
   $errors = array();

   if (strlen(trim($name)) < 1) {
      $errors['name'] = '<p class="error">
                                 Please enter a gallery name.
                              </p>';
   }

   if (strlen(trim($description)) < 1) {
      $errors['description'] = '<p class="error">
                                 Please enter a gallery description.
                              </p>';
   }

   if (intval($id) < 1) {
      $errors['gallery_id'] = '<p class="error">
                                  Gallery Id is not valid.
                               </p>';
   }

   if (count($errors) == 0) {
      $name = sanitize($db, $name);
      $description = sanitize($db, $description);

      $query = "UPDATE photopro_galleries
                SET
                 name = '$name',
                 description = '$description'
                WHERE id = $id";

      // send query to the db server and wait for result
      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if ($result == true) {
         redirect("/editgallery?id=$id");
      }
   }

   return $errors;
}

function check_gallery_image($db, $filename, $gallery_id) {

   if (intval($gallery_id) < 1 || strlen(trim($filename)) < 1) {
      redirect('/');
   }

   // set up query to fetch book list
   $query = "SELECT
               gallery_id
             FROM photopro_images
             WHERE filename = '$filename'
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   $row = mysqli_fetch_assoc($result);

   if ($row['gallery_id'] != $gallery_id) {
      redirect('/');
   }

   return false;
}

function set_featured_image($db, $featured_image, $gallery_id) {
   $featured_image = sanitize($db, $featured_image);
   $gallery_id = sanitize($db, $gallery_id);

   $query = "UPDATE photopro_galleries
             SET featured_image = '$featured_image'
             WHERE id = $gallery_id";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   if ($result == true) {
      redirect("/editgallery?id=$gallery_id");
   } else {
      redirect('/');
   }
}
