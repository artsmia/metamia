<nav>
  <ul>
    <li><a onClick="jQuery('#darrow').trigger('click');">Help</a></li>
    <?php if(isset($_SESSION['access']) && $_SESSION['access']=="admin"){echo "<li><a href='".$base_url."admin/'>Admin</a></li>";}?>
    <li id="username"><a><?php if(isset($_SESSION['user'])){echo $_SESSION['user'];}?></a></li>
    <li><a href="<?php echo $base_url?>logout.php">Logout</a></li>
  </ul>
</nav>
