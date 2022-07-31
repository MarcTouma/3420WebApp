<?php

$errors=array();
$username=$_POST['username'] ?? null;
$Password=$_POST['Password'] ?? null;
$Email=$_POST['Email'] ?? null;
$RePassword=$_POST['RePassword'] ?? null;
$ReEmail=$_POST['ReEmail'] ?? null;
include "includes/library.php";
if(isset($_POST['Registerbutton'])){
  //validate
  if(strlen($username)==0){
    $errors['username']=true;
  }
  if(strlen($Password)==0){
    $errors['Password']=true;
  }
  if(filter_var($Email, FILTER_VALIDATE_EMAIL) === false){
    $errors['Email']=true;
  }
  if(strlen($RePassword)==0){
    $errors['RePassword']=true;
  }
  if(!filter_var($ReEmail, FILTER_VALIDATE_EMAIL)){
    $errors['ReEmail']=true;
  }
  if($Email!==$ReEmail){
    if(!isset($errors['ReEmail'])){
      $errors['ReEmailInvalid']=true;
    }
  }
  if($Password!==$RePassword){
    if(!isset($errors['RePassword'])){
    $errors['RePasswordInvalid']=true;
    }
}
   //connect to database
   
   $pdo=connectDB();
  
   //Check if username already exists
   $query="SELECT `username` FROM `Users` WHERE username = ?" ;
   $stmt=$pdo->prepare($query);
   $stmt->execute([$username]);
   $FETCHED=$stmt->fetch();

   if($FETCHED){

     $errors['UserExists']=true;
   }
   //check if email already exists
   $query="SELECT `email` FROM `Users` WHERE email = ?" ;
   $stmt=$pdo->prepare($query);
   $stmt->execute([$Email]);
   $FETCHED1=$stmt->fetch();
   if($FETCHED1){
     $errors['EmailExists']=true;
   }
   //if no errors were found, insert into database
  if(count($errors)==0){
    $hash = password_hash($Password, PASSWORD_DEFAULT);
    //insert into the database
    $query="INSERT INTO `Users`(`username`, `Password`, `Email`) VALUES (?,?,?)" ;
    $stmt=$pdo->prepare($query);
    $stmt->execute([$username, $hash, $Email]);
    
    header('Location: login.php');
    exit();
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
  <title>Register Account</title>
  <script src="scripts/Register.js"></script>

</head>
<body>
<?php include 'includes/header.php'?>

<main>
 <div class="flexbox-container">
     
 <?php include 'includes/nav.php'?>
        <div class="flexbox-item-2">
          <h3 title="Register">Register</h3>
          <Form id="Register-Account-form" method="post">
          <div class="contain">
            <div class="textinput">
              <label for="username">Username</label>
              <input id="username" name="username" type="text" placeholder="John123"/>
              <span  class="error <?= !isset($errors['UserExists']) ? 'hidden' : "" ?>">This username is taken or is invalid</span>
              <span class="error <?= !isset($errors['username']) ? 'hidden' : "" ?>">Please enter a username</span>
            </div>                  
            <div class="info">
              <div class="textinput">
                <label for="Password">Password</label>
                <input id="Password" name="Password"type="password" placeholder="********"/>
                <span id="PassMess4" class="error hidden" ?>Please enter a Password</span>

                <div id="PassMeter" style="display:none;">
                  <meter min="0" low="50" high="80" max="100" id="meter" class="hidden"></meter>
                  <div>
                    <span id="PassMess" class="error hidden" ?>Password is weak, Enter a better password</span>
                    <span  id="PassMess1" class="error hidden" ?>Password ok</span>
                    <span id="PassMess2" class="error hidden" ?>Strong Password</span>
                  </div>
                </div>

              </div>
              <div class="textinput">
                <label for="Email">Email</label>
                <input id="Email" name ='Email' type="text" placeholder="John123@somemail.com"/>
                <span class="error <?= !isset($errors['EmailExists']) ? 'hidden' : "" ?>">This Email already belongs to an account</span>
                <span class="error <?= !isset($errors['Email']) ? 'hidden' : "" ?>">Please enter an Email</span>
              </div>        
            </div>
            <div class="info">
              <div class="textinput">
                <label for="Re-Password">Re-enter Password</label>
                <input id="Re-Password" name='RePassword' type="password" placeholder="********"/>
                <span class="error <?= !isset($errors['RePassword']) ? 'hidden' : "" ?>">please re-enter the original Password </span>
                <span class="error <?= !isset($errors['RePasswordInvalid']) ? 'hidden' : "" ?>">Re-entered password does not match original password</span>

              </div>
              <div class="textinput">
                <label for="Re-Email">Re-enter Email</label>
                <input id="Re-Email" name='ReEmail' type="text" placeholder="John123@somemail.com"/>
                <span class="error <?= !isset($errors['ReEmail']) ? 'hidden' : "" ?>">please enter a valid Email</span>
                <span class="error <?= !isset($errors['ReEmailInvalid']) ? 'hidden' : "" ?>">Re-entered Email did not match original Email</span>

              </div>          
            </div>            
            <button id="RegisterBtn" name="Registerbutton">Register</button>
          </div>
         
          </Form>
        </div>
 
 
     </div>
</main>
 
 
<?php include 'includes/footer.php'?>
</body>
</html>