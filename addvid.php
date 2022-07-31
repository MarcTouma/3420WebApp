<?php

session_start();
if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit();
}

  $errors=array();

  $Title =$_POST['Title'] ?? null;
  $Genre = $_POST['Genre'] ?? null;
  $MPAA = $_POST['MPAA'] ?? null;
  $Year = $_POST['Year'] ?? null;
  $RunTimeHours = $_POST['RunTimeHours'] ?? null;
  $RunTimeMinutes = $_POST['RunTimeMinutes'] ?? null;
  $Studio = $_POST['Studio'] ?? null;
  $TheatreRelease = $_POST['TheatreRelease'] ?? null;
  $DVDrelease = $_POST['DVDrelease'] ?? null;
  $Actors = $_POST['Actors'] ?? null;
  $CoverUpload = $_FILES['CoverUpload'] ?? null;
  $CoverLink = $_POST['CoverLink'] ?? null;
  $PlotSummary = $_POST['PlotSummary'] ?? null;
  $DVD=$_POST['DVD'] ?? null;
  $BluRay=$_POST['BluRay'] ?? null;
  $DigitalSD=$_POST['DigitalSD'] ?? null;
  $DigitalHD=$_POST['DigitalHD'] ?? null;

  //Calculate Runtime
  

  //Connect to Database
  include "includes/library.php";
  $pdo = connectDB();

  if(isset($_POST['submit'])){
    var_dump(($_POST));
    var_dump($_FILES['CoverUpload']);
    
    if(strlen($Title) == "0"){
      $errors['Title']=true;
     
    }
    
    if($Genre === "0"){
      $errors['Genre']=true;      
    }else{
      if($Genre==="1"){
        $Genre='Horror';
      }else if($Genre==="2"){
        $Genre='Romance';
      }else if($Genre==="3"){
        $Genre='Comedy';
      }else if ($Genre==="4"){
        $Genre='Documentary';
      }else if ($Genre==="5"){
        $Genre='non-fiction';        
      }else if ($Genre==="6"){
        $Genre='fiction';
      }
    }

    if($MPAA === "0"){
      $errors['MPAA']=true;      
    }else{
      if($MPAA==="1"){
        $MPAA='G';
      }else if($MPAA==="2"){
        $MPAA='PG';
      }else if($MPAA==="3"){
        $MPAA='PG-13';
      }else if ($MPAA==="4"){
        $MPAA='R';
      }else if ($MPAA==="5"){
        $MPAA='NC-17';        
      }
    }
    
    if(strlen($Year) ===0 ||strlen($Year)>4|| !is_numeric($Year)|| intval($Year)<1910){
      $errors['Year']=true;      
    }

    if(strlen($Studio)===0){
      $errors['Studio']=true;
      
    }

    if(strlen($TheatreRelease) ===0){
      $errors['TheatreRelease']=true;
    }
    else{
      if(strlen($TheatreRelease)>4|| !is_numeric($TheatreRelease)){
        $errors['TheatreRelease2']=true;

      }
    }

    if(strlen($DVDrelease) ===0){
      $errors['DVDrelease']=true;
    }else{
      if(strlen($DVDrelease)>4|| !is_numeric($DVDrelease)){
      $errors['DVDrelease2']=true;

      }
    }

    $ActorsArray=false;
    if(strlen($Actors)===0 ){
      $errors['Actors']=true;
    }else{
      $ActorsArray=explode(",", $Actors);
    }

    require_once "includes/upload.php"; 
    $Uploaded=false;
    if(is_uploaded_file($_FILES['CoverUpload']['tmp_name']) && strlen($CoverLink)===0){
      echo"Reached";
      var_dump(is_uploaded_file($_FILES['CoverUpload']['tmp_name']));
      $Uploaded=true;
      /* Retrieve max id from database and increment it to ensure uniqueness*/
      $stmt=$pdo->prepare('SELECT Mid FROM Movies WHERE Mid=( SELECT max(Mid) FROM Movies)');
      $stmt->execute();
      $MaxID=$stmt->fetch();
      if($MaxID){ // if there is at least 1 Movie in the database
      $ID =intval($MaxID['Mid'])+1;
      }else{ // otherwise if there are no movies in the database
      $ID=1;
      } 
      var_dump($ID);
      $path = WEBROOT."www_data/";
      $root="Cover";

      $results = checkErrors('CoverUpload',1024000);
      if(strlen($results)>0){
       
        $errors['Upload']=true;
        
      }else{
        $NewName=createFilename('CoverUpload',$path,$root,$ID);
        if(!move_uploaded_file($_FILES['CoverUpload']['tmp_name'],$NewName)){
          $errors['Upload']=true;              
          }
        else{
          echo "success";
        }
      }
    }else if(!$Uploaded && strlen($CoverLink)===0){ 
        $errors['Upload']=true;
    }else if($Uploaded && strlen($CoverLink)!==0){
      $errors['Upload']=true;
      $errors['CoverLink']=true;

    }

  //Calculate Runtime
  if(is_numeric($RunTimeHours) && is_numeric($RunTimeMinutes) ){
    if(intval($RunTimeMinutes) === 0 && intval($RunTimeHours)=== "0"){
      $errors['RunTimeHours']=true;
      $errors['RunTimeMinutes']=true;
    }
    else{
      $TotalSeconds=(intval($RunTimeHours)*3600)+(intval($RunTimeMinutes)*60);
    }
  }
  else{
     if($RunTimeHours && !is_numeric($RunTimeHours)){
      $errors['RunTimeHours']=true;
    }
    if ($RunTimeMinutes && !is_numeric($RunTimeMinutes)){
    $errors['RunTimeMinutes']=true;
    }
  }

  if(isset($errors['Upload']) && !$errors['Upload'] &&!filter_var($CoverLink, FILTER_VALIDATE_URL)){
    $errors['CoverLink']=true;
    unset($errors['Upload']);
  }

  $availability=array();
  $availability['DVD']=0;
  $availability['BluRay']=0;
  $availability['DigitalHD']=0;
  $availability['DigitalSD']=0;

  if(is_null($DVD) && is_null($BluRay) && is_null($DigitalSD) && is_null($DigitalHD)){
    $errors['VideoType']=true;
  }else{
    if(!is_null($DVD)){
      $availability['DVD']=1;
    }
    if(!is_null($BluRay)){
      $availability['BluRay']=1;
    }
    if(!is_null($DigitalSD)){
      $availability['DigitalSD']=1;
    }
    if(!is_null($DigitalHD)){
      $availability['DigitalHD']=1;
    }
  }
  var_dump($errors);

    /* If there are no errors, do database work */
  if (count($errors) === 0) {
    /********************************************
    * Put the code to write to the database here
    ********************************************/

    /* Connect to DB */
    $pdo = connectDB();   
    
    //check if the user already owns the movie 

    $query="SELECT * FROM `MoviesOwned` JOIN Movies ON MoviesOwned.Mid=Movies.Mid WHERE Movies.Title=? AND Movies.Year=? AND Movies.Studio=?";
    $stmt=$pdo->prepare($query);
    $stmt->execute([$Title, $Year, $Studio]);
    $Movieowned=$stmt->fetch();

    if($Movieowned){
      $errors['MovieOwned']=true;
    
     
    }
    //if the user doesnt own the movie
    else{
    //check if the movie already exists in the database
    $query="SELECT * FROM Movies where Title=? AND Year=? AND Studio=?";
    $stmt=$pdo->prepare($query);
    $stmt->execute([$Title, $Year, $Studio]);
    $MovieExists=$stmt->fetch();
    if($MovieExists){
      //update the Movies owned table so the user owns the movie
    $query="INSERT INTO `MoviesOwned`(`username`, `Mid`) VALUES (?,?)";
    $stmt=$pdo->prepare($query);
    $stmt->execute([$_SESSION['username'],$MovieExists['Mid']]);

    //redirect to home page, where the user can see that they own the movie
    header("Location: Index.php");
    exit();
    }
    //if the movie doesnt exist in the database
    else{
      //Add it to the Movies Table
      if(strlen($CoverLink)===0 && $Uploaded){


        var_dump(str_replace("public_html/","",str_replace("home/","~",$NewName)));
          $link="https://loki.trentu.ca".str_replace("public_html/","",str_replace("home/","~",$NewName));
        

      $query="INSERT INTO `Movies`(`Title`, `Genre`, `MPAA`, `Year`, `TotalSeconds`, `Studio`, `TheatreRelease`, `DVDRelease`, `CoverImage`, `PlotSummary`) VALUES (?,?,?,?,?,?,?,?,?,?)";
      $stmt=$pdo->prepare($query)-> execute([$Title,$Genre,$MPAA,$Year,$TotalSeconds,$Studio,$TheatreRelease,$DVDrelease,$link,$PlotSummary]);
      $MaxID=$pdo->lastInsertId();
      }
      else if(strlen($CoverLink)!==0 && !$Uploaded){
        $query="INSERT INTO `Movies`(`Title`, `Genre`, `MPAA`, `Year`, `TotalSeconds`, `Studio`, `TheatreRelease`, `DVDRelease`, `CoverLink`, `PlotSummary`) VALUES (?,?,?,?,?,?,?,?,?,?)";
      $stmt=$pdo->prepare($query)-> execute([$Title,$Genre,$MPAA,$Year,$TotalSeconds,$Studio,$TheatreRelease,$DVDrelease,$CoverLink,$PlotSummary]);
      $MaxID=$pdo->lastInsertId();
      }


      //Update the MoviesOwned Table
      $query="INSERT INTO `MoviesOwned`(`username`, `Mid`) VALUES (?,?)";
      $stmt=$pdo->prepare($query);
      $stmt->execute([$_SESSION['username'], $MaxID]);

      //Update the MoviesAvailability Table
      $query="INSERT INTO `MovieAvailabilty`(`Mid`, `DVD`, `BluRay`, `DigitalSD`, `DigitalHD`) VALUES (?,?,?,?,?)";
      $stmt=$pdo->prepare($query);
      $stmt->execute([$MaxID, $availability['DVD'], $availability['BluRay'],$availability['DigitalSD'],$availability['DigitalHD']]);

     // var_dump($ActorsArray);
      //Update the Actors and ActedIn tables as necessary
      if($ActorsArray !== false){
        foreach($ActorsArray as $Actor){
          $Actor=trim($Actor);
        //check if the actor exists in the actors table
        $query="SELECT `Name` FROM `Actors` WHERE Name=?";
        $stmt=$pdo->prepare($query);
        $stmt->execute([$Actor]);
        $actor=$stmt->fetch();
        //if so update the ActedIn table
        if($actor){
          $query="INSERT INTO `ActedIn`(`Actor`, `Mid`) VALUES (?,?)";
          $stmt=$pdo->prepare($query);
          $stmt->execute([$actor['Name'],$MaxID]);
        }else{
          //otherwise insert the actor into the Actors table and update the ActedIn table

          $query="INSERT INTO `Actors`(`Name`) VALUES (?)";
          $stmt=$pdo->prepare($query);
          $stmt->execute([$Actor]);

          $query="INSERT INTO `ActedIn`(`Actor`, `Mid`) VALUES (?,?)";
          $stmt=$pdo->prepare($query);
          $stmt->execute([$Actor,$MaxID]);
        }
        }
      }else{
        //check if the actor exists in the actors table
        $query="SELECT `Name` FROM `Actors` WHERE Name=?";
        $stmt=$pdo->prepare($query);
        $stmt->execute([$Actors]);
        $actor=$stmt->fetch();
        //if so update the ActedIn table
        if($actor){
          $query="INSERT INTO `ActedIn`(`Actor`, `Mid`) VALUES (?,?)";
          $stmt=$pdo->prepare($query);
          $stmt->execute([$actor,$MaxID]);
        }else{
          //otherwise insert the actor into the Actors table and update the ActedIn table
          $query="INSERT INTO `Actors`(`Name`) VALUES (?)";
          $stmt=$pdo->prepare($query);
          $stmt->execute([$Actors]);

          $query="INSERT INTO `ActedIn`(`Actor`, `Mid`) VALUES (?,?)";
          $stmt=$pdo->prepare($query);
          $stmt->execute([$Actors,$MaxID]);
        }
      }
    /* Redirect*/
      header('Location: index.php');
      exit();
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
  <script src="scripts/AddVid.js" ></script>
  <title>Add a Video</title>
</head>
<body>
<?php include 'includes/header.php'?>
 <main>
  <div class="flexbox-container">
      
  <div><?php include 'includes/nav.php'?></div>

        <div class="flexbox-item-2">
          <h3 title="Add Video">Add Video</h3>
          <Form id="Add-video-form" method="post"  enctype="multipart/form-data">
          <div class="contain">
            <div class="textinput">
              <label for="Title">Title</label>
              <input id="Title" name="Title" value="<?= $Title ?>" type="text" placeholder="Movie Title"  />
              <span class="error <?= !isset($errors['Title']) ? 'hidden' : "" ?>">Please enter a Title</span>
              <!--<button id="SearchBtn" name="SearchButton" class="centered">Search</button>-->
            </div>
            <div class="rating">
            <label for="Rating">Rating:</label>
            <div class="Star-rating">              
              <input type="radio" id="fifth-star" name="Star-rating" value="5" <?= $rating =5 ?> />
              <label for="fifth-star" title="5 stars">5 stars</label>
              <input type="radio" id="fourth-star" name="Star-rating" value="4" <?= $rating =4 ?> />
              <label for="fourth-star" title="4 stars">4 stars</label>
              <input type="radio" id="third-star" name="Star-rating" value="3" <?= $rating =3 ?> />
              <label for="third-star" title="3 stars">3 stars</label>
              <input type="radio" id="star2" name="Star-rating" value="2" <?= $rating =2 ?> />
              <label for="second-star" title="2 stars">2 stars</label>
              <input type="radio" id="star1" name="Star-rating" value="1"  <?= $rating =1 ?>/>
              <label for="first-star" title="1 star">1 star</label>
            </div>
          </div>
      
          <div class="centered">
            <label for="Genre">Genre:</label>
            <select id="Genre" name="Genre">
              <option value="0" selected>Select an option</option>
              <option value="1" <?= $Genre == 1 ? 'selected' : '' ?>>Horror</option>
              <option value="2" <?= $Genre == 2 ? 'selected' : '' ?>>Romance</option>
              <option value="3" <?= $Genre == 3 ? 'selected' : '' ?>>Comedy</option>
              <option value="4" <?= $Genre == 4 ? 'selected' : '' ?>>Documentary</option>
              <option value="5" <?= $Genre == 5 ? 'selected' : '' ?>>non-fiction</option>
              <option value="6" <?= $Genre == 6 ? 'selected' : '' ?>>fiction</option>
            </select>
            <span class="error <?= !isset($errors['Genre']) ? 'hidden' : "" ?>">Please select a Genre</span>                        
          </div>            
            <div class="info">
              <div class="textinput">
                <label for="MPAA">MPAA Rating</label>
                <select id="MPAA" name="MPAA">
                  <option value="0" selected>Select an option</option>
                  <option value="1" <?= $MPAA == 1 ? 'selected' : '' ?>>G</option>
                  <option value="2" <?= $MPAA == 2 ? 'selected' : '' ?>>PG</option>
                  <option value="3" <?= $MPAA == 3 ? 'selected' : '' ?>>PG-13</option>
                  <option value="4" <?= $MPAA == 4 ? 'selected' : '' ?>>R</option>
                  <option value="5" <?= $MPAA == 5 ? 'selected' : '' ?>>NC-17</option>
                </select>
                <span class="error <?= !isset($errors['MPAA']) ? 'hidden' : "" ?>">Please select an MPAA rating</span>
              </div>
              <div class="textinput">
                <label for="Year">Year</label>
                <input id="Year" name="Year" type="text" value="<?= $Year ?>" placeholder="Year of Release"  />
                <span class="error <?= !isset($errors['Year']) ? 'hidden' : "" ?>">Please Enter a valid year of release</span>
              </div>        
            </div>
            <div class="info">
              <div class="textinput">
                <label for="RunTime">Run-Time</label>
                <div id="RunTime">
                <input id="RunTimeHours" name="RunTimeHours" type="text" value="<?= $RunTimeHours ?>" placeholder="Video duration (hours)"  />
                <input id="RunTimeMinutes" name="RunTimeMinutes" type="text" value="<?= $RunTimeMinutes ?>" placeholder="(minutes, if applicable)"  />
                </div>
                <span id="minerr1" class="error <?= !isset($errors['RunTimeMinutes']) ? 'hidden' : "" ?>">Please enter the minutes (e.x 1hr & 45min, enter 45))</span>
                <span id="minerr2" class="error <?= !isset($errors['RunTimeMinutes2']) ? 'hidden' : "" ?>">invalid Entry, Please enter a number</span>
                <span id="minerr3" class="error <?= !isset($errors['RunTimeMinutes3']) ? 'hidden' : "" ?>">Please enter the runtime</span>
                <span id="minerr4" class="error <?= !isset($errors['RunTimeHours']) ? 'hidden' : "" ?>">Please enter the hours (e.x 1hr & 45min, enter 1))</span>
              </div>
              <div class="textinput">
                <label for="Studio">Studio</label>
                <input id="Studio" name="Studio" type="text" Value="<?= $Studio ?>" placeholder="Name of Studio"  />
                <span class="error <?= !isset($errors['Studio']) ? 'hidden' : "" ?>">Please enter the studio's name</span>

              </div>          
            </div>
            <div class="info">
              <div class="textinput">
                <label for="TheatreRelease">Theatre Release:</label>
                <input id="TheatreRelease" name="TheatreRelease" type="text" value="<?= $TheatreRelease ?>" placeholder="Release Date"  />
                <span class="error <?= !isset($errors['TheatreRelease']) ? 'hidden' : "" ?>">Please enter the year of theatre release</span>
                <span class="error <?= !isset($errors['TheatreRelease2']) ? 'hidden' : "" ?>">Please enter a valid year</span>


              </div>
              <div class="textinput">
                <label for="DVDrelease">DVD / Streaming Release:</label>
                <input id="DVDrelease" name="DVDrelease" type="text" value="<?= $DVDrelease ?>" placeholder="Release Date"  />
                <span class="error <?= !isset($errors['DVDrelease']) ? 'hidden' : "" ?>">Please Enter the Date of release</span>
                <span class="error <?= !isset($errors['DVDrelease2']) ? 'hidden' : "" ?>">Please enter a valid year</span>

              
              </div>          
            </div>
            <div class="textinput">
              <label for="Actors">Actors</label>
              <input id="Actors" name="Actors" type="text" value="<?= $Actors ?>" placeholder="Actor names seperated by commas"/>
              <span class="error <?= !isset($errors['Actors']) ? 'hidden' : "" ?>">Please Enter the Actors</span>
              <span class="error <?= !isset($errors['Actors2']) ? 'hidden' : "" ?>">Remember to seperate the actor names using commas</span>

            </div> 
            <div class="textinput">              
                <input type="hidden" name="MAX_FILE_SIZE" value="1024000" />
                <label for="file">Upload Cover Image:</label>
                <input name="CoverUpload" type="file" id="file"/>
                <span class="error <?= !isset($errors['Upload']) ? 'hidden' : "" ?>">Upload failed</span>

            </div>  
            <div class="textinput">
              <label for="CoverLink">Link Cover</label>
              <input id="CoverLink" name="CoverLink" type="url" pattern="https?://.+" placeholder="Link to Cover"/>
              <span class="error <?= !isset($errors['CoverLink']) ? 'hidden' : "" ?>">Please Link or upload a cover image</span>
            </div>   
            <div class="textinput">
              <label for="PlotSummary">Plot Summary</label>
              <textarea id="PlotSummary" name="PlotSummary" maxlength="2500" ></textarea>
              <div id="PlotSummary-counter">0 / 2500</div>
              <span class="error <?= !isset($errors['PlotSummary']) ? 'hidden' : "" ?>">Please include a brief plot summary</span>
              <span class="error <?= !isset($errors['Plot summary2']) ? 'hidden' : "" ?>">Please enter at least 25 characters</span>


            </div>
            <fieldset>
              <legend>Video Type</legend>
      
              <div>
                <input id="radio-DVD" name="DVD" type="Checkbox" value="DVD" <?= $DVD == "DVD" ? 'checked' : '' ?>/>
                <label for="radio-DVD">DVD</label>
              </div>
      
              <div>
                <input id="radio-BluRay" name="BluRay" type="Checkbox" value="BluRay" <?= $BluRay == "BluRay" ? 'checked' : '' ?>/>
                <label for="radio-BluRay">BluRay</label>
              </div>
      
              <div>
                <input id="radio-DigitalSD" name="DigitalSD" type="Checkbox" value="Digital SD" <?= $DigitalSD == "Digital SD" ? 'checked' : '' ?>/>
                <label for="radio-DigitalSD">Digital SD</label>
              </div>
              <div>
                <input id="radio-DigitalHD" name="DigitalHD" type="Checkbox" value="Didital HD" <?= $DigitalHD == "Digital HD" ? 'checked' : '' ?>/>
                <label for="radio-DigitalHD">Digital HD</label>
              </div>
            </fieldset>
            <span class="error <?= !isset($errors['VideoType']) ? 'hidden' : "" ?>">Please select a video type</span>
            <span class="error <?= !isset($errors['MovieOwned']) ? 'hidden' : "" ?>">You already own this movie</span>

            <button id="submit" name="submit" class="centered">Add Video</button>
          </div>
          
          </Form>
        </div>


     </div>
</main> 
  

<?php include 'includes/footer.php'?>


  <!-- Fix for Chrome bug: https://stackoverflow.com/a/42969608 -->
  <script></script>
</body>
</html>
