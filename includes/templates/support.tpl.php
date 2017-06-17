<?php
   $errors = array();

   $page_title = 'Support';

   /* check if the 'name' key exists in the $_POST
      superglobal array - if so, then the form must
      have been submitted by the user and we can begin
      processing the information entered. */

   if (isset( $_POST['name'])) {
      // check that the name has at least two letters
      if (strlen($_POST['name']) < 2 ) {
         $errors['name'] = '<p class="error">Please enter your name.</p>';
      }

      if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
         $errors['email'] = '<p class="error">Please enter a valid email address.</p>';
      }

      if (strlen($_POST['subject']) < 2) {
         $errors['subject'] = '<p class="error">Please enter subject.</p>';
      }

      // check that the message has at least two letters
      if (strlen($_POST['message']) < 2) {
         $errors['message'] = '<p class="error">Please enter a message.</p>';
      }

      // check the reCAPTCHA and verify it
      $post_data = array(
         'secret' => RECAPTCHA_SECRET,
         'response' => $_POST['g-recaptcha-response'],
         'remoteip' => $_SERVER['REMOTE_ADDR']
      );

      // initialize the cURL library
      $curl = curl_init();

      // prepare our connection options
      $options = array(
         CURLOPT_URL => RECAPTCHA_VERIFY_URL, // the URL
         CURLOPT_CUSTOMREQUEST => 'POST',     // use the POST method
         CURLOPT_POSTFIELDS => $post_data,    // use this POST form data
         CURLOPT_RETURNTRANSFER => true,      // don't echo the result, store it instead
         CURLOPT_HEADER => false,             // we don't need the http headers
         CURLOPT_SSL_VERIFYPEER => false      // no need to verify SSL certificate
      );

      // apply the connection options
      curl_setopt_array( $curl, $options );

      // make the connection; take the response and convert it into a PHP object
      $response = json_decode( curl_exec( $curl ) );

      // close the connection
      curl_close( $curl );

      // validate the result to check if human or spambot
      if (!$response->success) {
         $errors['reCAPTCHA'] = '<p class="error">Please prove you are human.</p>';
      }

      if (count($errors) == 0) {

         // include PHPMailer, and stop PHP if it is not found
         require('PHPMailerAutoload.php');

         // create an instance of PHPMailer
         $mail = new PHPMailer();

         // set destination address
         $mail->addAddress('gaganshankar@hotmail.com');

         // set the 'from' address
         $mail->setFrom($_POST['email']);

         // set the subject line
         $mail->Subject = 'Contact form email from ' . $_POST['subject'];

         // set up the HTML version of the email message
         $message  = "<h2>Email:</h2>
                      {$_POST['email']}
                      <br><br>"
                      . nl2br($_POST['message'])
                      . '<img src="https://upload.wikimedia.org/wikipedia/en/9/99/MarioSMBW.png" alt="email image">';

         // set this email to use HTML (if the email client supports it)
         // vs plain text
         $mail->isHTML(true);

         // set the HTML message
         $mail->Body = $message;

         // set the plain text message
         $mail->AltBody = strip_tags($message);

         if($mail->send()) {
            // email was sent succesfully
            header('Location: ' . $_SERVER[ 'REQUEST_URI' ] . '?success');
         } else {
            $errors['server'] = '<p class="error server">There was a problem sending
                                 your email, please contact the administrator.</p>';
            }
        }
    }
   include('includes/templates/header.tpl.php');
?>
<div id="map">
   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2883.1119125629175!2d-79.61041114851771!3d43.72900195544064!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882b3a5391c12321%3A0x747f0c3b5cdecaa0!2sHumber+College!5e0!3m2!1sen!2sca!4v1496775244120"  frameborder="0" style="border:0" allowfullscreen></iframe>
</div>
<div id="mainContainer">
   <section id="contactAddress">
      <h5>Our Office</h5>
      <div>
         <img id="locationImg" src="images/support-page/location.jpg" alt="locationimage">
      </div>
      <h5>Contact Info</h5>
      <ul>
         <li>
            <img src="images/support-page/home_icon.png" class="imageicon" alt="homeicon">
            <span>13 Elwin Road, Brampton</span>
         </li>
         <li>
            <img src="images/support-page/mobile_icon.png" class="imageicon" alt="mobileicon">
            <span id="mobileNum">+1-(647)261-0602</span>
         </li>
         <li>
            <img src="images/support-page/mail_icon.png" class="imageicon" alt="mailicon">
            <span>gaganshankar@hotmail.com</span>
         </li>
      </ul>
   </section>
   <section id="contactForm">
      <h5>Reach out to us</h5>
      <p>We love to listen and we are eagerly waiting to talk to you regarding your project.Get in touch with us if you have any queries and we will get back to you as soon as possible.</p>
      <?php if (isset($_GET['success'])): ?>
         <h6>Success!</h6>
         <p id="success">Your email was successfully sent, and we will respond to you as soon as we can.</p>
      <?php else: ?>
         <!-- the form action is being set to the name of this file automatically -->
         <?php echo $errors['server']; ?>
         <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <ol>
               <li>
                  <?php echo $errors['name'];?>
                  <input type="text" name="name" size="80" value="<?php echo $_POST['name']; ?>" placeholder="Enter Name">
               </li>
               <li>
                  <?php echo $errors['email'];?>
                  <input type="text" name="email" size="80" value="<?php echo $_POST['email'] ?>" placeholder="Enter Email">
               </li>
               <li>
                  <?php echo $errors['subject'];?>
                  <input type="text" name="subject" size="80" value="<?php echo $_POST['subject'] ?>" placeholder="Enter Subject">
               </li>
               <li>
                  <?php echo $errors['message'];?>
                  <textarea name="message" rows="20" cols="50" placeholder="Enter Message"><?php echo $_POST['message'] ?></textarea>
               </li>
               <?php echo $errors[ 'reCAPTCHA' ]; ?>
               <div id="recapcha" class="g-recaptcha" data-sitekey="6LcyNh8UAAAAAIHlNKlLumTbo3kRVn9lvWm3Wx8C"></div>
               <li>
                  <input type="submit" value="Send" />
               </li>
            </ol>
         </form>
      <?php endif; ?>
      </section>
      <a href="#map"><img id="goUp" src="images/support-page/goup_image.png"></a>
   </div>
<?php include('includes/templates/footer.tpl.php'); ?>
