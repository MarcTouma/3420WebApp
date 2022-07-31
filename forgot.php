<?php
$errors=array();
$Email=$_POST['Email'] ?? null;
include "includes/library.php";
$pdo=connectDB();

if(isset($_POST['button'])){
  if(strlen($Email)===0){
    $errors['NoEmail']=true;
  }else{
    if(filter_var($Email, FILTER_VALIDATE_EMAIL) === false){
    $errors['Email']=true;
    }
    else{
      $query="SELECT `username`, `Email` FROM `Users` WHERE Email=?";
      $stmt=$pdo->prepare($query);
      $stmt->execute([$Email]);
      $FETCHED1=$stmt->fetch();
      if(!$FETCHED1){
      $errors['NoEmailExists']=true;
      }else{

        $guid=uniqid($FETCHED1['username']);
        $query="UPDATE `Users` SET `Token`=?,`TokenExp`=? WHERE Email=?";
        $stmt=$pdo->prepare($query);
        $stmt->execute([$guid, (time() + 86400), $FETCHED1['Email']]);
        $link="https://loki.trentu.ca/~marctouma/3420/assignments/assn2/ResetPass.php?id=".$guid;

        include ('Mail.php');  //this includes the pear SMTP mail library
        $from = "Password System Reset <noreply@loki.trentu.ca>";
        $to = $FETCHED1['Email'];  //put user's email here
        $subject = "Password Reset";
        $body = "Click the following link to reset your password. ".$link;
        $host = "smtp.trentu.ca";
        $headers = array ('From' => $from,
        'To' => $to,
        'Subject' => $subject);
        $smtp = Mail::factory('smtp',array ('host' => $host));
  
        $mail = $smtp->send($to, $headers, $body);
        if (PEAR::isError($mail)) {
        echo("<p>" . $mail->getMessage() . "</p>");
        } else {
      $errors['Sent']=true;
        }

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
  <title>Forgot Password</title>
</head>
<body>
<?php include 'includes/header.php'?>

 <main>
  <div class="flexbox-container">
      
  <?php include 'includes/nav.php'?>

        <div class="flexbox-item-2">
          <h3 title="Reset your password">Reset your Password</h3>
          <Form id="forgot-form" method="post">
          <Fieldset>
            <div class="userinput">
              <label for="Username_email">Email:</label>
              <input id="Username_email" type="text" placeholder="Enter email" name="Email"/>
              <span class="error <?= !isset($errors['NoEmail']) ? 'hidden' : "" ?>">Please enter an Email</span>
              <span class="error <?= !isset($errors['Email']) ? 'hidden' : "" ?>">Please enter a valid Email</span>
              <span class="error <?= !isset($errors['NoEmailExists']) ? 'hidden' : "" ?>">This Email does not belong to any account</span>
              <span class="error <?= !isset($errors['Sent']) ? 'hidden' : "" ?>">Email sent successfully</span>

            </div>
            <div class="centered">
               <a>Enter you email. you will recieve an email to reset your password</a>.
              </label>
            </div>
            <button id="Btn" name="button" class="centered">Send email</button>
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
