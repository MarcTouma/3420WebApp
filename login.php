<?php
session_start();


$_SESSION = array();

// Finally, destroy the session.
session_destroy();


/*****************************************
 * Put form processing here
 ***********************************************/
/* Declare `errors` array and get everything from $_POST, coalescing to `null`
 * to avoid errors */
$errors = array();
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
$remember = $_POST['checkbox'] ?? null;


/*****************************************
 * Include library, make database connection,
 * and query for dropdown list information here
 ***********************************************/
// Only run this section if the form has just been submitted
if (isset($_POST['LogInButton'])) {

  /* ------ Error validation (from the last lab) ------ */

  // `name` is invalid if it is empty: a valid name can be pretty much anything,
  // don't enforce much.
  if (strlen($username) == 0) {
    $errors['user'] = true;
  }

  // `email` is invalid if `filter_var` returns `false`.
  if (strlen($password) == 0) {
    $errors['pass'] = true;
  }

  /* ------ </from-the-last-lab> ------ */


  /* If there are no errors, do database work */
  if (count($errors) === 0) {
    /********************************************
    * Put the code to write to the database here
    ********************************************/
    include "includes/library.php";

    /* Connect to DB */
    $pdo = connectDB();

    /* Add the vote to `allvotes` */
    $query="SELECT `username`, `Password` FROM `Users` WHERE username = ?" ;
    $stmt=$pdo->prepare($query);
    $stmt->execute([$username]);
    $FETCHED=$stmt->fetch(); 
    if($FETCHED){
      if($FETCHED['username']==$username){
        if(password_verify($password, $FETCHED['Password'])){
          session_start();      
          $_SESSION['username']=$FETCHED['username'];
        
        header('Location: index.php');
        exit();
        }
        else{
        $errors['pass']=true;
        }
      }
      else{$errors['user']=true;}
  }
    else{
      $errors['user']=true;
    }   
  }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <link rel="stylesheet" href="./styles/main.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Vote for Wonka's Next Candy!</title>
</head>
<body>
<?php include 'includes/header.php'?>

 <main>
  <div class="flexbox-container">
      
  <?php include 'includes/nav.php'?>

        <div class="flexbox-item-2">
          <h3 title="Log In">Log In</h3>
          <Form id="Log-in-form" method="post">
          <Fieldset>
            <div class="userinput">
              <label for="Username">Username:</label>
              <input id="Username" name="username" type="text" placeholder="John Smith"  />
              <span class="error <?= !isset($errors['user']) ? 'hidden' : "" ?>">Please enter a valid username</span>
            </div>
      
            <div class="userinput">
              <label for="Password">Password:</label>
              <input id="Password" name="password" type="Password" placeholder="********"  />
              <span class="error <?= !isset($errors['pass']) ? 'hidden' : "" ?>">Please enter a valid password</span>
              <a href="forgot.php">Forgot your password?</a>
            </div>

            <div class="centered">
              <input id="Remember" name="checkbox" type="checkbox" name="agree" />
              <label for="Remember">
               <a>Remember Me</a>.
              </label>
              <span class="error <?= !isset($errors['Year']) ? 'hidden' : "" ?>">Please Enter the Year of release</span>
            </div>
            <button id="logInBtn" name="LogInButton" class="centered">Log In</button>
          </Fieldset>
          </Form>
        </div>


     </div>
</main> 
  

<?php include 'includes/footer.php'?>


  <!-- Fix for Chrome bug: https://stackoverflow.com/a/42969608 -->
  <script></script>
</body>
</html>
