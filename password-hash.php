<?php
$password = 'a';
$hash = password_hash($password, PASSWORD_DEFAULT);
?>
<textarea name="hash" id="hash" cols="30" rows="10"><?php echo $hash . ' ' . strlen($hash); ?></textarea>
