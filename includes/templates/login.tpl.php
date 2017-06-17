<?php
   $page_title = 'Log in';
   include('includes/templates/header.tpl.php');
?>
<div class="form-container">
   <h1 class="form-heading">Log in</h1>
   <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
      <?php echo $errors['email']; ?>
      <label>Email:</label>
      <input type="email" name="email" size="80" maxlength="140"
      value="<?php echo $_POST['email']; ?>">

      <?php echo $errors['password']; ?>
      <label>Password</label>
      <input type="password" name="password" size="80" maxlength="140"
      value="<?php echo $_POST['password']; ?>">

      <input type="submit" value="Log in">
   </form>
</div>
<?php include('includes/templates/footer.tpl.php'); ?>
