<?php
session_start();
if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit();
}
$errors=array();
include "includes/library.php";
$pdo=connectDB();
$AllMovies=array();
$Title=$_POST['Title'] ?? null;

if(isset($_COOKIE['MovieList'])){
  if(is_array($_COOKIE['MovieList'])){
    $inc=0;
    foreach($_COOKIE['MovieList'] as $cookie){
      $AllMovies[$inc]=json_decode($cookie, true);
      $inc=$inc+1;
    }
  }else{
    $AllMovies=json_decode($_COOKIE['MovieList'], true);
  }

}

if (isset($_POST['Tsearch'])){
  $AllMovies=array();
  if(isset($_COOKIE['MovieList'])){

    if(is_array($_COOKIE['MovieList'])){
      $increment=0;
      foreach($_COOKIE['MovieList'] as $cookie){
        setcookie("MovieList[".strval($increment)."]", "",time() - 3600);
        $increment=$increment+1;
        }
      unset($_COOKIE['MovieList']);
    }else{
    setcookie("MovieList", "", Time()-3600);
    unset($_COOKIE['MovieList']);
  }
  }
  
  if(!strlen($Title)=="0"){
    
  $query="SELECT Movies.Mid, `Title`, `Genre`, `MPAA`, `Year`, `TotalSeconds`, `Studio`, `TheatreRelease`, `PlotSummary`, COALESCE(CoverLink, CoverImage) as ImageLink FROM `Movies` Join MoviesOwned on Movies.Mid=MoviesOwned.Mid WHERE MoviesOwned.username= ? AND Movies.Title LIKE ? ";
  $stmt=$pdo->prepare($query);
  $stmt->execute([$_SESSION['username'],$Title]); 
  $AllMovies=$stmt->fetchAll();
  if(!$AllMovies){
    $AllMovies=array();
    $errors['NoTitle']=true;
  }
  if($AllMovies){
    $errors['Found']=true;
  setcookie("MovieList", json_encode($AllMovies));
  }
}else{
  $errors['Title']=true;
}
}

if (isset($_POST['Asearch'])){
  $AllMovies=array();

  if(isset($_COOKIE['MovieList'])){
  if(is_array($_COOKIE['MovieList'])){
    $increment=0;
    foreach($_COOKIE['MovieList'] as $cookie){
      setcookie("MovieList[".strval($increment)."]", "",time() - 3600);
      $increment=$increment+1;
      }
    unset($_COOKIE['MovieList']);
  }else{
  setcookie("MovieList", "", Time()-3600);
  unset($_COOKIE['MovieList']);
}
  }

  if(!strlen($Title)=="0"){
   
  $query="SELECT Movies.Mid, `Title`, `Genre`, `MPAA`, `Year`, `TotalSeconds`, `Studio`, `TheatreRelease`, `PlotSummary`, COALESCE(CoverLink, CoverImage) as ImageLink FROM `Movies` Join MoviesOwned on Movies.Mid=MoviesOwned.Mid JOIN ActedIn ON Movies.Mid=ActedIn.Mid WHERE  MoviesOwned.username=? AND ActedIn.Actor Like ?";
  $stmt=$pdo->prepare($query);
  $stmt->execute([$_SESSION['username'],$Title]); 
  $AllMovies=$stmt->fetchAll();
  if(!$AllMovies){
    $AllMovies=array();
    $errors['NoActor']=true;
  }
  if($AllMovies){
    $errors['Found']=true;
    $increment=0;
    foreach($AllMovies as $movie){
    setcookie("MovieList[".strval($increment)."]", json_encode($movie));
    $increment=$increment+1;
    }
  }
}else{
  $errors['Title']=true;
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
  <title>Search for a Video</title>
  <script src="scripts/details.js"></script>

</head>
<body>
<?php include 'includes/header.php'?>

 <main>
  <div class="flexbox-container">
      
  <?php include 'includes/nav.php'?>

    </nav>
        <div class="flexbox-item-2">
          <h3 title="Video Search">Search for a video</h3>
          <Form id="Search-form" method="post">
          <div class="contain">
            <div class="textinput">
              <label for="TitleSearch">Search by Movie Title or Actor</label>
              <input id="TitleSearch" name="Title" type="text" placeholder="Enter a Movie Title or Actor"/>
              <span class="error <?= !isset($errors['Title']) ? 'hidden' : "" ?>">Please enter a title</span>
                        <span class="error <?= !isset($errors['NoTitle']) ? 'hidden' : "" ?>">You do not own that title</span>
                        <span class="error <?= !isset($errors['NoActor']) ? 'hidden' : "" ?>"><?=$Title ?> has not acted in any of the titles you own </span>
              <div class="SearchButtons">          
              <div><button id="SearchBtn" name='Tsearch' name="Search Button">Search by Title</button></div>
              <div><button id="SearchBtn" name='Asearch' name="Search Button">Search by Actor</button></div>
            </div>
            </div>
            <ul class="Library">
            <?php  foreach($AllMovies as $row): ?>          
            <li><?php 
              if(strpos($row['ImageLink'], "/home")===0){
                $row['ImageLink']=str_replace("home/","~",$row['ImageLink']);
                $row['ImageLink']=str_replace("public_html/","",$row['ImageLink']);
                $row['ImageLink']="https://loki.trentu.ca".$row['ImageLink'];
              }
              ?><img src="<?php echo $row['ImageLink'] ?>">
                <h4 title="Movie Title"><?= $row['Title']?></h4>
                <div>
                  <!--<input type="hidden" name="MovieID" value="<?= $row['Mid'] ?>"/>-->
                   <a href="search.php?id=<?=$row['Mid']  ?>" class="fa fa-eye"></a>
                   <a href="EditVideo.php?id=<?=$row['Mid']  ?>" class="fa fa-pencil-square"></a>
                   <a href="includes/delete.php?id=<?=$row['Mid']  ?>" class="fa fa-trash"></a>
                </div>
            </li>         
            <?php endforeach ?>                       
          </ul>
          </div>
          </Form>
        </div>
        </div>

        <div id="DetailView"  class="hidden">
              <div>
              </div>
              <?php include "details.php" ?>
            </div>
</main> 
  

  <footer>
    <div>&copy; 2022 Marc Touma.</div>
  </footer>
</body>
</html>
