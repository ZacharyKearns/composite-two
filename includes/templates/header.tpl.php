<!doctype html>
<html lang="en">
<head>
   <meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1">

      <title><?php echo $page_title; ?> - Photo Pro</title>
      <!-- Main Stylesheet -->
      <link rel="stylesheet" href="css/main.css">

      <!-- masonry.js -->
      <script src="js/masonry.js"></script>

      <!-- main.js -->
      <script src="js/main.js"></script>

      <!-- HTML5 Shiv - adds HTML5 support for IE versions lower than 9 -->
      <!--[if lt IE 9]>
         <script src="js/html5shiv.min.js"></script>
      <![endif]-->
</head>
<body>
   <div id="fake-header"></div>
   <header id="header">
      <a href="index.php"><img src="images/misc/logo.svg" alt="logo for header" id="logo"></a>
         <img class="show" src="images/misc/menu-button.svg" alt="menu button" id="menu-button">
         <img src="images/misc/close-menu.svg" alt="close button" id="close-menu">
      <nav id="menu">
         <ul>

            <li>
               <a class="menu-item" href="/home">HOME</a>
            </li>
            <?php if (user_is_logged_in()): ?>
               <li>
                  <a class="menu-item" href="/editphotos">EDIT PHOTOS</a>
               </li>
               <li>
                  <a class="menu-item" href="/editprofile">EDIT PROFILE</a>
               </li>
               <li>
                  <a class="menu-item" href="/logout">LOGOUT</a>
               </li>
            <?php else: ?>
               <li>
                  <a class="menu-item" href="/login">LOG IN</a>
               </li>
               <li>
                  <a class="menu-item" href="/signup">SIGN UP</a>
               </li>
            <?php endif ?>
            <li>
               <a class="menu-item" href="/about">ABOUT</a>
            </li>
            <li>
               <a class="menu-item" href="/support">SUPPORT</a>
            </li>
         </ul>
      </nav>
      <div class="social-icons">
         <p class="follow-us">Follow Us</p>
         <img src="images/misc/facebook.png" alt="facebook">
         <img src="images/misc/twitter.png" alt="twitter">
         <img src="images/misc/pinterest.png" alt="pinterest">
      </div>
   </header>
   <main id="main">
