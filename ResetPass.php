<?php
$errors=array();

$Pass=$_POST['Pass'] ?? null;
$RePass=$_POST['RePass'] ?? null;


include "includes/library.php";
$pdo=connectDB();
$query="SELECT Token, TokenExp FROM `Users` WHERE Token = ?";
$stmt=$pdo->prepare($query);
$stmt->execute([$_GET['id']]);
$Exists=$stmt->fetch();
if($Exists){
    if(time()>$Exists['TokenExp']){
        $errors['InvalidToken']=true;
    }
}else{
    $errors['InvalidToken']=true;
}

if(isset($_POST['button'])){
    if(strlen($Pass)===0){
        $errors['Pass']=true;

    }else{
        if(strlen($Pass)<6){
        $errors['ShortPass']=true;
        }
    }

    if($Pass !== $RePass){
    $errors['RePass']=true;
    }
    if(count($errors)===0){
    $hash=password_hash($Pass, PASSWORD_DEFAULT);
    //insert into the database
    $query="UPDATE `Users` SET `Password`=? WHERE Token=?" ;
    $stmt=$pdo->prepare($query);
    $stmt->execute([$hash, $_GET['id']]);
    
    header('Location: login.php');
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
  <title>Forgot Password</title>
</head>
<body>
<?php include 'includes/header.php'?>

 <main>
  <div class="flexbox-container">
      
  <?php include 'includes/nav.php'?>

        <div class="flexbox-item-2">
        <span class="<?= isset($errors['InvalidToken']) ? 'hidden' : "" ?>">
          <h3 title="Reset your password">Reset your Password</h3>
          <Form id="forgot-form" method="post">
          <Fieldset>
            <div class="userinput">
              <label for="password">New Password:</label>
              <input id="password" type="password" placeholder="*******" name="Pass"/>
              <span class="error <?= !isset($errors['Pass']) ? 'hidden' : "" ?>">Please enter a Password</span>
              <span class="error <?= !isset($errors['ShortPass']) ? 'hidden' : "" ?>">Please enter a longer password</span>

              <label for="repassword">Re-enter Password:</label>
              <input id="repassword" type="password" placeholder="*******" name="RePass"/>
              <span class="error <?= !isset($errors['RePass']) ? 'hidden' : "" ?>">The re-entered password does not match the new password</span>
            </div>
            <div class="centered">
               <a>Enter you email. you will recieve an email to reset your password</a>.
              </label>
            </div>
            <button id="Btn" name="button" class="centered">Save</button>
          </Fieldset>
          </Form>
          </span>
          <span class="<?= !isset($errors['InvalidToken']) ? 'hidden' : "" ?>"><a>Reset Link is Invalid</a></span>
        </div>


     </div>
</main> 
  

<?php include 'includes/footer.php'?>


  <!-- Fix for Chrome bug: https://stackoverflow.com/a/42969608 -->
  <script></script>
</body>
</html>
