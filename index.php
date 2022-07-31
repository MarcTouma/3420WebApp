<?php
session_start();
if(!isset($_SESSION['username'])){
  header('Location: login.php');
  exit();
}

include "includes/library.php";
$pdo=connectDB();

$query="SELECT Movies.Mid, `Title`, `Genre`, `MPAA`, `Year`, `TotalSeconds`, `Studio`, `TheatreRelease`, `PlotSummary`, COALESCE(CoverLink, CoverImage) as ImageLink FROM `Movies` Join MoviesOwned on Movies.Mid=MoviesOwned.Mid WHERE MoviesOwned.username=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$_SESSION['username']]);
$AllMovies=$stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <link rel="stylesheet" href="./styles/main.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Movie Library</title>
  <script src="scripts/details.js"></script>
</head>
<body>
<?php include 'includes/header.php'?>

 <main>
  <div class="flexbox-container">
      
  <?php include 'includes/nav.php'?>

        <div class="flexbox-item-2">
            <h3> Library</h3>      
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
                   <a id="View" href="index.php?id=<?=$row['Mid']  ?>" class="fa fa-eye"></a>
                   <a href="EditVideo.php?id=<?=$row['Mid']  ?>" class="fa fa-pencil-square"></a>
                   <a href="includes/delete.php?id=<?=$row['Mid']  ?>" class="fa fa-trash"></a>                
                </div>
            </li>         
            <?php endforeach ?> 
                         
          </ul>
            
        </div>
     </div>
     <div id="DetailView"  class="hidden">
              <div>
              </div>
              <?php include "details.php" ?>
            </div>
</main> 
  

<?php include 'includes/footer.php'?>


  <!-- Fix for Chrome bug: https://stackoverflow.com/a/42969608 -->
  <script></script>
</body>
</html>
