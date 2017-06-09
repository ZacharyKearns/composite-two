<?php
   $page_title = 'Success';
   include('includes/templates/header.tpl.php');
?>
<p>Success! An email with a verification link was sent to your inbox.
   Please click the link to verify your account.</p>
<a href="/verify?hash=<?php echo $_GET['hash']; ?>">Verify Here</a>
<?php include('includes/templates/footer.tpl.php'); ?>
