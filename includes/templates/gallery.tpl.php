<?php
   $page_title = 'Gallery';
   if (
      mysqli_num_rows($user) == 0 ||
      mysqli_num_rows($gallery) == 0 ||
      mysqli_num_rows($images) == 0
   ) {
      redirect('/');
   }

   $user_row = mysqli_fetch_assoc($user);
   if (!$user_row['active']) {
      redirect('/');
   }
   $gallery_row = mysqli_fetch_assoc($gallery);
   $email = $user_row['email'];
   include('includes/templates/header.tpl.php');
?>

    <!-- #region Jssor Slider Begin -->
    <script src="js/jssor.slider-24.1.5.min.js" type="text/javascript"></script>
    <script src="js/jssor.js"></script>
    <div class="gallery-heading-container">
      <h2 class="gallery-heading"><?php echo $gallery_row['name']; ?></h2>
      <h3 class="gallery-subheading"><?php echo $gallery_row['description']; ?></h3>
    </div>
    <div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:960px;height:480px;overflow:hidden;visibility:hidden;background-color:#24262e;">
        <!-- Loading Screen -->
        <div data-u="loading" style="position:absolute;top:0px;left:0px;background:url('img/loading.gif') no-repeat 50% 50%;background-color:rgba(0, 0, 0, 0.7);"></div>
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:240px;width:720px;height:480px;overflow:hidden;">
           <?php
              while($image_row = mysqli_fetch_assoc($images)):
              $src_large = "images/user-galleries/$email/large/{$image_row['filename']}";
              $src_thumb = "images/user-galleries/$email/thumb/{$image_row['filename']}";
           ?>
               <div>
                   <img data-u="image" src=<?php echo $src_large; ?> />
                   <img data-u="thumb" src=<?php echo $src_thumb; ?> />
                   <div data-u="caption" style="position:absolute;bottom:0;left:0;width:350px;height:30px;z-index:0;background-color:rgba(235,81,0,0.5);font-size:20px;color:#ffffff;line-height:30px;text-align:center;">
                      <?php echo $image_row['name']; ?>
                   </div>
               </div>
            <?php endwhile ?>
            <a data-u="any" href="https://www.jssor.com" style="display:none">slider bootstrap</a>
        </div>
        <!-- Thumbnail Navigator -->
        <div data-u="thumbnavigator" class="jssort01-99-66" style="position:absolute;left:0px;top:0px;width:240px;height:480px;" data-autocenter="2">
            <!-- Thumbnail Item Skin Begin -->
            <div data-u="slides" style="cursor: default;">
                <div data-u="prototype" class="p">
                    <div class="w">
                        <div data-u="thumbnailtemplate" class="t"></div>
                    </div>
                    <div class="c"></div>
                </div>
            </div>
            <!-- Thumbnail Item Skin End -->
        </div>
        <!-- Arrow Navigator -->
        <span data-u="arrowleft" class="jssora05l" style="top:0px;left:248px;width:40px;height:40px;" data-autocenter="2"></span>
        <span data-u="arrowright" class="jssora05r" style="top:0px;right:8px;width:40px;height:40px;" data-autocenter="2"></span>
    </div>
    <!-- #endregion Jssor Slider End -->

<?php include('includes/templates/footer.tpl.php'); ?>
