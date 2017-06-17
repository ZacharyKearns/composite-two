<?php
   $page_title = 'Sign up';
   include('includes/templates/header.tpl.php');
?>
<div class="form-container">
   <h1 class="form-heading">Sign up</h1>
   <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">

      <?php echo $errors['email']; ?>
      <label>Email:</label>
      <input type="email" name="email" size="80" maxlength="140"
      value="<?php echo $_POST['email']; ?>">

      <?php echo $errors['password']; ?>
      <label>Password:</label>
      <input type="password" name="password" size="80" maxlength="140"
      value="<?php echo $_POST['password']; ?>">

      <?php echo $errors['confirm_password']; ?>
      <label>Confirm Password:</label>
      <input type="password" name="confirm_password" size="80" maxlength="140"
      value="<?php echo $_POST['confirm_password']; ?>">

      <input type="submit" value="Sign up">
   </form>
</div>
<?php include('includes/templates/footer.tpl.php'); ?>
