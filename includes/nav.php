<?php
if(isset($_SESSION['username'])){
}
?>
<script src="../assn3/scripts/Nav.js"></script>
<nav class="flexbox-item-1">
      <ul class="nav">
          <li><a href="index.php">Home</a></li>            
          <li class="<?= !isset($_SESSION['username']) ? 'hidden' : "" ?>"> <div class="dropdown 1"><a name="Videos">Videos &nbsp;
          <button type="submit" class="fa fa-caret-down"></button>
          </a>
      <ul class="dropdown-container 1">
        <li class="<?= !isset($_SESSION['username']) ? 'hidden' : "" ?>"><a href="search.php">Search My Videos</a></li>
        <li class="<?= !isset($_SESSION['username']) ? 'hidden' : "" ?>"><a href="addvid.php">Add Video</a></il>
      </ul></div></li>
      <li class="dropdown 2"><a> Account &nbsp;
          <button class="fa fa-caret-down"></button>
          </a>
      <ul class="dropdown-container 2">
        <li class="<?= isset($_SESSION['username']) ? 'hidden' : "" ?>"><a href="login.php">Log In</a></li>
        <li class="<?= !isset($_SESSION['username']) ? 'hidden' : "" ?>"><a href="login.php">Log Out</a></il>
        <li class="<?= !isset($_SESSION['username']) ? 'hidden' : "" ?>"><a href="EditAccount.php">Edit Account</a></li>
        <li class="<?= !isset($_SESSION['username']) ? 'hidden' : "" ?>"><a href="DeleteAccout.php">Delete Account</a></li>
        <li class="<?= isset($_SESSION['username']) ? 'hidden' : "" ?>"><a href="register.php">Create Account</a></li>
      </ul></li>
  </ul>
  </nav>