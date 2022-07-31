<?php


//Get Movie Details from DataBase
$pdo=connectDB();

$query="SELECT Movies.Mid, `Title`, `Genre`, `MPAA`, `Year`, `TotalSeconds`, `Studio`, `TheatreRelease`, `DVDRelease`, `PlotSummary`, COALESCE(CoverLink, CoverImage) as ImageLink FROM `Movies`WHERE Mid=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$_GET['id']]);
$Movie=$stmt->fetch();
//Get Movie availability details from database
$query="SELECT DVD, BluRay, DigitalSD, DigitalHD FROM `MovieAvailabilty` WHERE Mid=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$_GET['id']]);
$MovieAvail=$stmt->fetch();

//Get Actors details from database
$query="SELECT Actor FROM `ActedIn` WHERE Mid=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$_GET['id']]);
$MovieActors=$stmt->fetchAll(); //this array is multidimensional
$Mactors=array(); // so I used this foreach loop to put the values I need in a 1D array
foreach ($MovieActors as $MA){
array_push($Mactors, $MA['Actor']);
}

//Calculate Runtime
if(($Movie['TotalSeconds']/3600)>=1.0){
  $Hours=floor($Movie['TotalSeconds']/3600);
  }else{$Hours=0;}
$Minutes=Round(($Movie['TotalSeconds']-(3600*$Hours))/60);
$Actorslist=implode(", ",$Mactors);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <link rel="stylesheet" href="./styles/main.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Movie Details</title>
  <script src="scripts/details.js"></script>
</head>
<body>


 <main>
<div class="flexbox-container"> 
    <div id="outer-box">
    <span class="close">&times;</span>
      <div id="NavButtons">
        <a id="PreviousMovie" href=""> < Previous</a>
        <a id="NextMovie" href="">Next ></a>
      </div>
      <div id="inner-box-1">
        <div class="poster"><img src="<?=$Movie['ImageLink'] ?>" alt="" title="Movie Poster"></div>
        <div class="Movie-Info">
          <h4><?=$Movie['Title'] ?></h4>
          <div class="score">              
              <p>5 stars</p>
            </div>
          <div class="Availability">
            <div class="availability <?= !$MovieAvail['DVD']==1 ? 'hidden' : "" ?>">              
              <i class="fa fa-check-circle"></i>
              <label for="DVD">DVD</label>
            </div>
            <div class="availability <?= !$MovieAvail['BluRay']==1 ? 'hidden' : "" ?>">              
              <i class="fa fa-check-circle"></i>
              <label for="BluRay">BluRay</label>
            </div>
            <div class="availability <?= !$MovieAvail['DigitalSD']==1 ? 'hidden' : "" ?>">              
              <i class="fa fa-check-circle"></i>
              <label for="digital-DVD">digital-DVD</label>
            </div>
            <div class="availability <?= !$MovieAvail['DigitalHD']==1 ? 'hidden' : "" ?>">              
              <i class="fa fa-check-circle"></i>
              <label for="digital-HD">Digital HD</label>
            </div>
          </div>
          <div class="more-info-1">
            <div id="left">
              <div>
              <label for="MPAA">MPAA Rating:</label>
              <p>R</p>
              </div>
              <div>
                <label for="Year">Year:</label>
                <p><?=$Movie['Year'] ?></p>
              </div>            
            </div>          
            <div id="right">
              <div>
                <label for="Theatre-Release">Theatre Release</label>
                <p><?=$Movie['TheatreRelease'] ?></p>
              </div>
              <div>
                <label for="DVD/streaming-Release">DVD/streaming Release</label>
                <p><?=$Movie['DVDRelease'] ?></p>
              </div>
            </div>
          </div>        
          <div class="more-info-2">
            <div>
              <label for="Studio">Studio:</label>
              <p><?=$Movie['Studio'] ?></p>
              </div>
              <div>
                <label for="Actors">Actors:</label>
                <p><?=$Actorslist ?></p>
              </div>
              <div>
                <label for="Genres">Genres:</label>
                <p><?=$Movie['Genre'] ?></p>
              </div>
              <div>
                <label for="RunTime">RunTime:</label>
                <p><?=$Hours?>h <?=$Minutes?>m</p>
              </div>
          </div>
        </div>
      </div>      
      <div id="inner-box-2">
        <p><?=$Movie['PlotSummary'] ?></p>
      </div>
      </div>
    </div>
  
</div>  
</main>  
</body>
</html>
