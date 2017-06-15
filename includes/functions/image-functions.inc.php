<?php
function resize_image($image_filepath, $destination_folder, $dimensions) {
   $info = getimagesize($image_filepath);
   // get the type of image it is
   $type = $info['mime'];
   // get original image width
   $original_width = $info[0];
   // get original image height
   $original_height = $info[1];
   // read the image into the web server's memory
   switch($type) {
      case 'image/png':
         $original_image = imagecreatefrompng($image_filepath);
      break;
      case 'image/gif':
         $original_image = imagecreatefromgif($image_filepath);
      break;
      case 'image/jpeg':
      case 'image/pjpeg':
         $original_image = imagecreatefromjpeg($image_filepath);
      break;
      default:
         return false;
      break;
   }

   // disable the blending of the alpha channel, which
   // would only create opaque pixels
   imagealphablending($original_image, false);
   // enable the complete alpha channel, so you can
   // get translucent pixels
   imagesavealpha($original_image, true);

   // calculate aspect ratio
   $aspect_ratio = $original_width / $original_height;
   // calculate resized width and height
   if ($aspect_ratio < 1) {
      // portrait image
      $resized_height = $dimensions;
      $resized_width = floor($resized_height * $aspect_ratio);
   } else {
      // landscape or square image
      $resized_width = $dimensions;
      $resized_height = floor($resized_width / $aspect_ratio);
   }
   // create a new empty image in memory to match
   // resized dimensions
   $resized_image = imagecreatetruecolor($resized_width, $resized_height);

   // create a transparent fill color
   $transparent = imagecolorallocatealpha($resized_image, 0, 0, 0, 127);

   // fill the image with transparency
   imagefill($resized_image, 0, 0, $transparent);

   // disable the blending of the alpha channel, which
   // would only create opaque pixels
   imagealphablending($resized_image, false);
   // enable the complete alpha channel, so you can
   // get translucent pixels
   imagesavealpha($resized_image, true);

   // copy and resample pixels from large image to small
   imagecopyresampled(
      $resized_image,
      $original_image,
      0, 0, 0, 0,
      $resized_width,
      $resized_height,
      $original_width,
      $original_height
   );

   // extract filename from file path
   $filename = explode('/', $image_filepath);
   $filename = array_pop($filename);

   // append filename to desired destination folder
   $resized_filepath = $destination_folder . $filename;

   // write the resized image to the destination folder
   switch($type) {
      case 'image/png':
         imagepng($resized_image, $resized_filepath, 6);
      break;
      case 'image/gif':
         imagegif($resized_image, $resized_filepath);
      break;
      case 'image/jpeg':
      case 'image/pjpeg':
         imagejpeg($resized_image, $resized_filepath, 80);
      break;
      default:
         return false;
      break;
   }

   // free up memory after the task is completed
   imagedestroy($original_image);
   imagedestroy($resized_image);

   return $image_filepath;
}



function update_user_image($db, $email, $old_image) {
   $errors = array();
   // check if there is a filename submitted
   if (strlen($_FILES['user-image']['name']) > 0) {

      $temp_location = $_FILES['user-image']['tmp_name'];

      if (
         $_FILES['user-image']['size'] > MAX_USER_IMAGE_FILE_SIZE ||
         $_FILES['user-image']['error'] == UPLOAD_ERR_INI_SIZE
      ) {
         // file is too big
         $maxSize = round(MAX_USER_IMAGE_FILE_SIZE / 1024);
         $errors['size'] = "<p class=\"error\">
                               The file uploaded is too large,
                               please upload an image smaller
                               than $maxSize KB.
                            </p>";
      }

      $info = getimagesize($temp_location);
      if (!$info || strpos(ALLOWED_FILE_TYPES, $info['mime']) === false) {
         // file is either corrupted or not the correct type of file
         $errors['type'] = '<p class="error">
                               The file is either corrupted or not one of the
                               allowed types (JPEG, GIF, or PNG)
                            </p>';
      }

      if (count($errors) == 0) {
         if (RANDOMIZE_FILENAME) {
            // unique hash for the filename
            $hash = sha1(microtime());
            // get the original extension
            $extension = explode('.', $_FILES['user-image']['name']);
            $extension = array_pop($extension);
            // combine it all together
            $final_location = USER_IMAGE_FOLDER . "{$hash}.{$extension}";
         } else {
            $final_location = USER_IMAGE_FOLDER . $_FILES['user-image']['name'];
         }


         if (move_uploaded_file($temp_location, $final_location)) {
            // file was moved OK

            resize_image(
               $final_location,
               USER_IMAGE_FOLDER_LARGE,
               320
            );

            resize_image(
               $final_location,
               USER_IMAGE_FOLDER_MEDIUM,
               160
            );

            resize_image(
               $final_location,
               USER_IMAGE_FOLDER_SMALL,
               40
            );

            // insert into the database

            // get the filename on its own
            $filename = explode('/', $final_location);
            $filename = array_pop($filename);

            // gather other details
            $email = sanitize($db, $_POST['email']);

            $query = "UPDATE photopro_users
                      SET user_image = '$filename'
                      WHERE email = '$email'";

            $result = mysqli_query($db, $query) or die(mysqli_error($db));

            if ($result == true) {
               $_SESSION['user_image'] = $filename;
               unlink(USER_IMAGE_FOLDER . $filename);
               if ($old_image != 'empty.png') {
                  unlink(USER_IMAGE_FOLDER_LARGE . $old_image);
                  unlink(USER_IMAGE_FOLDER_MEDIUM . $old_image);
                  unlink(USER_IMAGE_FOLDER_SMALL . $old_image);
               }
               redirect("/editprofile?email=$email");
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

// END UPDATE USER IMAGE

// ADD GALLERY IMAGE

function add_gallery_image($db, $gallery_id, $image_name) {
   $errors = array();

   if (strlen(trim($image_name)) < 1) {
      $errors['image_name'] = '<p class="error">
                                  Please enter an image name.
                               </p>';
   }

   if (intval($gallery_id) < 1) {
      $errors['add_image_gallery_id'] = '<p class="error">
                                            Gallery Id is not valid.
                                         </p>';
   }

   $user_email = sanitize($db, $_POST['email']);
   $user_folder = USER_GALLERIES_FOLDER . "$user_email/";
   $image_name = sanitize($db, $image_name);

   // check if there is a filename submitted
   if (strlen($_FILES['gallery-image']['name']) > 0) {

      $temp_location = $_FILES['gallery-image']['tmp_name'];

      if (
         $_FILES['gallery-image']['size'] > MAX_FILE_SIZE ||
         $_FILES['gallery-image']['error'] == UPLOAD_ERR_INI_SIZE
      ) {
         // file is too big
         $maxSize = round(MAX_FILE_SIZE / 1024);
         $errors['size'] = "<p class=\"error\">
                               The file uploaded is too large,
                               please upload an image smaller
                               than $maxSize KB.
                            </p>";
      }

      $info = getimagesize($temp_location);
      if (!$info || strpos(ALLOWED_FILE_TYPES, $info['mime']) === false) {
         // file is either corrupted or not the correct type of file
         $errors['type'] = '<p class="error">
                               The file is either corrupted or not one of the
                               allowed types (JPEG, GIF, or PNG)
                            </p>';
      }

      if (count($errors) == 0) {
         if (RANDOMIZE_FILENAME) {
            // unique hash for the filename
            $hash = sha1(microtime());
            // get the original extension
            $extension = explode('.', $_FILES['gallery-image']['name']);
            $extension = array_pop($extension);
            // combine it all together
            $final_location = "{$user_folder}{$hash}.{$extension}";
         } else {
            $final_location = $user_folder . $_FILES['gallery-image']['name'];
         }


         if (move_uploaded_file($temp_location, $final_location)) {
            // file was moved OK

            $user_folder_large = $user_folder . "large/";
            $user_folder_thumb = $user_folder . "thumb/";

            resize_image(
               $final_location,
               $user_folder_large,
               600
            );

            resize_image(
               $final_location,
               $user_folder_thumb,
               100
            );

            // insert into the database

            // get the filename on its own
            $filename = explode('/', $final_location);
            $filename = array_pop($filename);

            $query = "INSERT INTO photopro_images(name, filename, gallery_id)
                      VALUES('$image_name', '$filename', $gallery_id)";

            $result = mysqli_query($db, $query) or die(mysqli_error($db));

            if ($result == true) {
               unlink($user_folder . $filename);
               redirect("/editgallery?id=$gallery_id");
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

function get_images($db, $id) {
   // set up query to fetch galleries
   $query = "SELECT
                id,
                name,
                filename
             FROM photopro_images
             WHERE gallery_id = $id
             ORDER BY created_at ASC";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

function update_image_name($db, $image_id, $image_name, $gallery_id) {
   $errors = array();

   if (strlen(trim($image_name)) < 1) {
      $errors['updated_image_name'] = '<p class="error">
                                          Image name cannot be empty.
                                       </p>';
   }

   if (count($errors) == 0) {
      $image_id = sanitize($db, $image_id);
      $image_name = sanitize($db, $image_name);

      $query = "UPDATE photopro_images
                SET name = '$image_name'
                WHERE id = $image_id";

      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if ($result == true) {
         redirect("/editgallery?id=$gallery_id");
      }
   }
   return $errors;
}

function delete_image($db, $filename, $gallery_id) {
   $filename = sanitize($db, $filename);

   $query = "DELETE FROM photopro_images WHERE filename = '$filename' LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   if ($result == true) {
      unlink(USER_GALLERIES_FOLDER . $_SESSION['email'] . "/large/$filename");
      unlink(USER_GALLERIES_FOLDER . $_SESSION['email'] . "/thumb/$filename");
      redirect("/editgallery?id=$gallery_id");
   } else {
      redirect('/');
   }
}
