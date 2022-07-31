<?php
session_start();
if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit();
}
//connect to database
include "includes/library.php";
$pdo=connectDB();
$query="SELECT Email FROM `Users` WHERE username=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$_SESSION['username']]);
$Uinfo=$stmt->fetch();

$errors=array();
$username=$_POST['username'] ?? $_SESSION['username'];
$Password=$_POST['Password'] ?? null;
$OPassword=$_POST['OPassword'] ?? null;
$Email=$_POST['Email'] ?? null;
$OEmail=$_POST['OEmail'] ?? $Uinfo['Email'];
$RePassword=$_POST['RePassword'] ?? null;
$ReEmail=$_POST['ReEmail'] ?? null;

if(isset($_POST['Registerbutton'])){
  //validate
  if(strlen($username)===0){
    $errors['username']=true;
  }


  if(strlen($OPassword)!==0 && strlen($Password)===0){
    $errors['NewPass']=true;
  } 
  
  if(strlen($OPassword)!==0 && strlen($RePassword)===0){
    $errors['RePassword']=true;
  } 
  
  if(strlen($OPassword)!==0 && strlen($Password)<6 && strlen($Password)!==0){$errors['ShortPass']=true;}

  if(filter_var($OEmail, FILTER_VALIDATE_EMAIL) === false || $OEmail!=$Uinfo['Email']){
    $errors['OEmail']=true;
  }

  if(strlen($Email)!==0){
    if(filter_var($Email, FILTER_VALIDATE_EMAIL) === false||$Email===$OEmail){
      $errors['Email']=true;
    }
  }
  if(strlen($ReEmail)!==0){
  if(!filter_var($ReEmail, FILTER_VALIDATE_EMAIL)){
    $errors['ReEmail']=true;      
  }}
  if(strlen($Email)!==0 && $Email!==$ReEmail){
    if(!isset($errors['ReEmail'])){
      $errors['ReEmailInvalid']=true;
    }
  }
  if(strlen($OPassword)!==0 && $Password!==$RePassword){
    if(!isset($errors['RePassword'])){
    $errors['RePasswordInvalid']=true;
    }
  }

  if(strlen($OPassword)!==0 && (strlen($Password)!==0 || strlen($RePassword)!==0)){
    //Check password
  $query="SELECT `Password` FROM `Users` WHERE username = ?" ;
  $stmt=$pdo->prepare($query);
  $stmt->execute([$username]);
  $UPass=$stmt->fetch();
  if(!password_verify($OPassword, $UPass['Password'])){
    $errors['NotPass']=true;
  }
  }
  if(strlen($OEmail)!==0 && (strlen($Email)!==0 || strlen($ReEmail)!==0)){
      //check if email already exists
    $query="SELECT `email` FROM `Users` WHERE email = ?";
     $stmt=$pdo->prepare($query);
     $stmt->execute([$Email]);
     $FETCHED1=$stmt->fetch();
     if($FETCHED1){
     $errors['EmailExists']=true;
     }
  }
  
  if(count($errors)===0){
    //handle Email update
    if(strlen($Email)!==0){      
      
        $query="UPDATE `Users` SET `Email`=? WHERE username=?" ;
        $stmt=$pdo->prepare($query);
        $stmt->execute([$Email, $_SESSION['username']]);
  
    }

    if(strlen($Password)!==0){
      $hash = password_hash($Password, PASSWORD_DEFAULT);

      $query="UPDATE `Users` SET `Password`=? WHERE username=?" ;
      $stmt=$pdo->prepare($query);
      $stmt->execute([$hash, $_SESSION['username']]);
    }

    //handle username update
    if($username!==$_SESSION['username']){
      //Check if username already exists
    $query="SELECT `username` FROM `Users` WHERE username = ?" ;
    $stmt=$pdo->prepare($query);
    $stmt->execute([$username]);
    $FETCHED=$stmt->fetch();

      if($FETCHED){
        $errors['UserExists']=true;
      }
      else{
        $query="UPDATE `Users` SET `username`=? WHERE username=?" ;
        $stmt=$pdo->prepare($query);
        $stmt->execute([$username, $_SESSION['username']]);

        $query="UPDATE `MoviesOwned` SET `username`=? WHERE username=?" ;
        $stmt=$pdo->prepare($query);
        $stmt->execute([$username, $_SESSION['username']]);
      }    
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
  <title>Edit Account</title>
  
</head>
<body>
<?php include 'includes/header.php'?>

<main>
 <div class="flexbox-container">
     
 <?php include 'includes/nav.php'?>
        <div class="flexbox-item-2">
          <h3 title="Edit Account">Edit Account</h3>
          <Form id="Edit-Account-form" method="post">
          <div class="contain">
            <div class="textinput">
              <label for="username">Username</label>
              <input id="username" name="username" type="text" value="<?=$username ?>"/>
              <span class="error <?= !isset($errors['UserExists']) ? 'hidden' : "" ?>">This username already exists</span>
              <span class="error <?= !isset($errors['username']) ? 'hidden' : "" ?>">Please enter a username</span>
            </div>                  
            <div class="info">
              <div class="textinput">
                <label for="Password">Original Password</label>
                <input id="Password" name="OPassword"type="password" placeholder="********"/>
                <span class="error <?= !isset($errors['NotPass']) ? 'hidden' : "" ?>">Incorrect Password</span>

              </div>
              <div class="textinput">
                <label for="Email">Original Email</label>
                <input id="Email" name ='OEmail' type="text" value="<?=$OEmail ?>" placeholder="John123@somemail.com"/>
                <span class="error <?= !isset($errors['Email']) ? 'hidden' : "" ?>">Please enter your current Email</span>
                <span class="error <?= !isset($errors['NotEmail']) ? 'hidden' : "" ?>">Please enter your current a valid Email</span>

              </div>        
            </div>
            <div class="info">
              <div class="textinput">
                <label for="Password">New Password</label>
                <input id="Password" name="Password"type="password" placeholder="********"/>
                <span class="error <?= !isset($errors['NewPass']) ? 'hidden' : "" ?>">Please enter a new password</span>
                <span class="error <?= !isset($errors['ShortPass']) ? 'hidden' : "" ?>">Please enter a longer password</span>

              </div>
              <div class="textinput">
                <label for="Email">New Email</label>
                <input id="Email" name ='Email' type="text" placeholder="John123@somemail.com"/>
                <span class="error <?= !isset($errors['EmailExists']) ? 'hidden' : "" ?>">This Email already belongs to an account</span>
                <span class="error <?= !isset($errors['Email']) ? 'hidden' : "" ?>">Please enter a valid Email</span>
              </div>        
            </div>
            <div class="info">
              <div class="textinput">
                <label for="Re-Password">Re-enter Password</label>
                <input id="Re-Password" name='RePassword' type="password" placeholder="********"/>
                <span class="error <?= !isset($errors['RePassword']) ? 'hidden' : "" ?>">please re-enter the new Password </span>
                <span class="error <?= !isset($errors['RePasswordInvalid']) ? 'hidden' : "" ?>">Re-entered password did not match original password</span>

              </div>
              <div class="textinput">
                <label for="Re-Email">Re-enter Email</label>
                <input id="Re-Email" name='ReEmail' type="text" placeholder="John123@somemail.com"/>
                <span class="error <?= !isset($errors['ReEmail']) ? 'hidden' : "" ?>">please enter a valid Email</span>
                <span class="error <?= !isset($errors['ReEmailInvalid']) ? 'hidden' : "" ?>">Re-entered Email did not match the new Email</span>

              </div>          
            </div>            
            <button id="RegisterBtn" name="Registerbutton">Save changes</button>
          </div> 
          </Form> 
     </div>
</main>
 
 
<?php include 'includes/footer.php'?>
</body>
</html>