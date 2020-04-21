<?php
include("includes/init.php");
$title = "Music Catalog";

$db = open_sqlite_db("secure/catalog.sqlite");

$messages = array();

function print_record($record)
{
    ?>
  <div class="inRecord" id = "rec">
    <div class="id" id= "rec"><?php echo htmlspecialchars($record["id"]); ?></div>
    <div class="singer" id = "rec">
    <?php $singer = $record["singer"];

    if ($singer == "Maroon 5") {
      ?><img class = "singer-img" src="images/maroon5.jpg" alt="flowers">
    <?php }  ?>

    <?php
    if ($singer == "Zedd and Alessia Cara") {
      ?><img class = "singer-img" src="images/zedd.jpg" alt="flowers">
    <?php }  ?>

    <?php
    if ($singer == "Kehlani") {
      ?><img class = "singer-img" src="images/kehlani.jpg" alt="flowers">
    <?php }  ?>

    <?php
    if ($singer == "Sam Smith") {
      ?><img class = "singer-img" src="images/sam.jpg" alt="flowers">
    <?php }  ?>

    <?php
    if ($singer == "Allan Walker") {
      ?><img class = "singer-img" src="images/alan.jpg" alt="flowers">
    <?php }  ?>

    <?php
    if ($singer == "Panic! at the disco") {
      ?><img class = "singer-img" src="images/panic.jpg" alt="flowers">
    <?php }  ?>

    <?php
    if ($singer == "Adam Levine") {
      ?><img class = "singer-img" src="images/adam.jpg" alt="flowers">
    <?php }  ?>

    <?php
    if ($singer == "Troye Sivan") {
      ?><img class = "singer-img" src="images/troye.jpg" alt="flowers">
    <?php }  ?>

    <?php
    if ($singer == "BTS") {
      ?><img class = "singer-img" src="images/bts.jpg" alt="flowers">
    <?php }  ?>


    <?php
    if ($singer == "Clean Bandit feat Zara Larsson") {
      ?><img class = "singer-img" src="images/clean.jpg" alt="flowers">
    <?php }  ?>

    </div>
    <div class="name1" id = "rec">
      <?php echo htmlspecialchars($record["name"]); ?>
      ||
      <?php echo htmlspecialchars($record["genre"]); ?>
    </div>
    <div class="ranking" id = "rec"><?php
      $arrow = $record["rankings"];
        if ($arrow == "up") {
          echo "🢁";
        } else {
          echo "🢃";
        }
      ?></div>
  </div>
  <span></span>
<?php
}

const SEARCH_FIELDS = [
  "all" => "Search Everything",
  "name" => "Search Song Titles",
  "singer" => "Search Singers",
  "rankings" => "Search Rankings -- Up/Down",
  "genre" => "Search Genre"
];

if (isset($_GET['search'])) {
  $do_search = TRUE;

  $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);
  if (in_array($category, array_keys(SEARCH_FIELDS))) {
    $search_field = $category;
  } else {
    array_push($messages, "Invalid category for search.");
    $do_search = FALSE;
  }

  $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
  $search = trim($search);
} else {
  $do_search = FALSE;
  $category = NULL;
  $search = NULL;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $valid_data = TRUE;

  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $singer = filter_input(INPUT_POST, 'singer', FILTER_SANITIZE_STRING);
  $rankings = filter_input(INPUT_POST, 'rankings', FILTER_SANITIZE_STRING);
  $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_STRING);

  // rankings
  if ($rankings == "up" || $rankings == "down") {
      $valid_data = true;
  }
  else{
      $valid_data = FALSE;
  }

  if ($valid_data) {
    $params = array(
      ':name' => $name,
      ':singer' => $singer,
      ':rankings' => $rankings,
      ':genre' => $genre,
    );
    $sql="INSERT INTO catalog (name, singer, rankings, genre)
     VALUES (:name, :singer, :rankings, :genre) ";

    if (exec_sql_query($db, $sql, $params)) {
      array_push($messages, "Your review has been recorded. Thank you!");
    } else {
      array_push($messages, "Failed to add review.");
    }
  } else {
    array_push($messages, "Failed to add review. Invalid product or rating.");
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- font awesome cdn -->
  <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="style.css">
  <title>Music catalog</title>
</head>

<body>
    <?php
    foreach ($messages as $message) {
      echo "<p><strong>" . htmlspecialchars($message) . "</strong></p>\n";
    }
    ?>

    <div class="sContainer">
    <form id="searchForm" action="index.php" method="get" novalidate>
      <select name="category">
        <?php foreach (SEARCH_FIELDS as $field_name => $label) { ?>
          <option value="<?php echo $field_name; ?>"><?php echo $label; ?></option>
        <?php } ?>
      </select>
      <input type="text" id = "search" name="search" placeholder= "Find your next soulmate" required />
      <button type="submit" class="searchButton">
        <i class="fa fa-search"></i>
     </button>
    </form>
    </div>

    <?php
    if ($do_search) { ?>

      <?php
      if ($search_field == "all") {
        $sql = "SELECT * FROM catalog WHERE (name LIKE '%' || :search || '%' ) OR (singer LIKE '%' || :search || '%') OR (genre LIKE '%' || :search || '%') OR (rankings LIKE '%' || :search || '%')";
        $params=array(':search'=>$search);
      } else {
        $sql = "SELECT * FROM catalog WHERE ($search_field LIKE '%' || :search || '%' )";
        $params=array(':search'=>$search);

      }
    } else {
      ?>
      <?php

      $sql = "SELECT * FROM catalog";
      $params = array();
    }

    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
      $records = $result->fetchAll();

      if (count($records) > 0) {
      ?>
          <?php
          foreach ($records as $record) {
            print_record($record);
          }
          ?>
    <?php
      } else {
        echo "<p>Sorry, it's unavailable at the moment. Stay tuned! </p>";
      }
    }
    ?>

    <!--form-->
    <section id="container">
    <div class="form-img">
        <img src="./images/add.jpg" alt="black feathers">
    </div>
    <div class="form-text">
      <span></span>
        <h3> Put in your favorite song right here!</h3><br>

        <form id="input-form" method="post" action="index.php" type = "submit" >

        <div class="input_fields">
          <input name="name" placeholder = "Song title" id="name" type="text" />
        </div>

        <div class="input_fields">
          <input name="singer" placeholder = "Singer" id="singer" type="text"/>
        </div>

        <div class="input_fields">
          <input name="genre" placeholder = "Genre" id="genre" type="text"/>
        </div>

        <div class="input_fields">
          <input name="rankings" placeholder = "Up or Down" id="rankings" type="text" />
        </div>

        <div class="input_fields">
          <span></span>
            <input type="submit" id="submit" value="Submit"/>
        </div>

      </form>

      </div>
      </section>

      <!--citations for all the images used on this page -->
      <div class="citation">
      <!-- Source: https://www.google.com/url?sa=i&url=https%3A%2F%2Fhype.my%2F2014%2F26359%2Fhypes-album-review-maroon-5-v%2F&psig=AOvVaw2HUf8S36skE2T5zI76IZXR&ust=1586374268172000&source=images&cd=vfe&ved=0CAMQjB1qFwoTCLixifCG1-gCFQAAAAAdAAAAABAD
 -->
      Source: <cite><a href="https://www.google.com/url?sa=i&url=https%3A%2F%2Fhype.my%2F2014%2F26359%2Fhypes-album-review-maroon-5-v%2F&psig=AOvVaw2HUf8S36skE2T5zI76IZXR&ust=1586374268172000&source=images&cd=vfe&ved=0CAMQjB1qFwoTCLixifCG1-gCFQAAAAAdAAAAABAD
"     >Maroon 5 icon</a></cite>

      <!-- Source: https://www.google.com/url?sa=i&url=https%3A%2F%2Flyricsery.com%2Flyrics%2Fzedd-alessia-cara-stay%2F&psig=AOvVaw3C72JXxWf449SnlFulDQ9v&ust=1586374760526000&source=images&cd=vfe&ved=0CAMQjB1qFwoTCMibk9aI1-gCFQAAAAAdAAAAABAP -->
      Source: <cite><a href="https://www.google.com/url?sa=i&url=https%3A%2F%2Flyricsery.com%2Flyrics%2Fzedd-alessia-cara-stay%2F&psig=AOvVaw3C72JXxWf449SnlFulDQ9v&ust=1586374760526000&source=images&cd=vfe&ved=0CAMQjB1qFwoTCMibk9aI1-gCFQAAAAAdAAAAABAP">Zedd icon</a></cite>

      <!-- Source: https://wallpaperaccess.com/troye-sivan-iphone -->
      Source: <cite><a href="https://wallpaperaccess.com/troye-sivan-iphone">Troye Sivan icon</a></cite>

      <!-- Source: https://www.google.com/imgres?imgurl=https%3A%2F%2Fupload.wikimedia.org%2Fwikipedia%2Fen%2F5%2F52%2FGangstaKehlani.jpeg&imgrefurl=https%3A%2F%2Fen.wikipedia.org%2Fwiki%2FGangsta_(Kehlani_song)&tbnid=0D7wUkQ2BxZiTM&vet=12ahUKEwjkm_maidfoAhWL1XMBHc8TAUcQMygAegUIARD7AQ..i&docid=mEwoD4F6q2CbWM&w=300&h=300&q=gangsta%20kehlani%20album%20cover&hl=en&ved=2ahUKEwjkm_maidfoAhWL1XMBHc8TAUcQMygAegUIARD7AQ -->
      Source: <cite><a href="https://www.google.com/imgres?imgurl=https%3A%2F%2Fupload.wikimedia.org%2Fwikipedia%2Fen%2F5%2F52%2FGangstaKehlani.jpeg&imgrefurl=https%3A%2F%2Fen.wikipedia.org%2Fwiki%2FGangsta_(Kehlani_song)&tbnid=0D7wUkQ2BxZiTM&vet=12ahUKEwjkm_maidfoAhWL1XMBHc8TAUcQMygAegUIARD7AQ..i&docid=mEwoD4F6q2CbWM&w=300&h=300&q=gangsta%20kehlani%20album%20cover&hl=en&ved=2ahUKEwjkm_maidfoAhWL1XMBHc8TAUcQMygAegUIARD7AQ">Kehlani icon</a></cite>


      <!-- Source: https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.udiscovermusic.com%2Fstories%2Frediscover-in-the-lonely-hour%2F&psig=AOvVaw3YoXMDyf-S6KKE3vZyKdTk&ust=1586375015188000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCIiDs9KJ1-gCFQAAAAAdAAAAABAJ -->
      Source: <cite><a href="https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.udiscovermusic.com%2Fstories%2Frediscover-in-the-lonely-hour%2F&psig=AOvVaw3YoXMDyf-S6KKE3vZyKdTk&ust=1586375015188000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCIiDs9KJ1-gCFQAAAAAdAAAAABAJ">Sam Smith icon</a></cite>


      <!-- Source: https://www.google.com/url?sa=i&url=https%3A%2F%2Fopen.spotify.com%2Fplaylist%2F37i9dQZF1DX4npDJDFDYLg&psig=AOvVaw0i6mbqGXTd7Zd0b4Ep1uAc&ust=1586375387539000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCMCZr4iL1-gCFQAAAAAdAAAAABAf -->
      Source: <cite><a href="https://www.google.com/url?sa=i&url=https%3A%2F%2Fopen.spotify.com%2Fplaylist%2F37i9dQZF1DX4npDJDFDYLg&psig=AOvVaw0i6mbqGXTd7Zd0b4Ep1uAc&ust=1586375387539000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCMCZr4iL1-gCFQAAAAAdAAAAABAf">Alan Walker icon</a></cite>


      <!-- Source: https://www.google.com/url?sa=i&url=https%3A%2F%2Fnaijaforbe.com%2Fhip-hop%2Fmp3-panic-disco-high-hopes%2F&psig=AOvVaw3mzLgNzwB1YVfkrfwOfpNQ&ust=1586375707665000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCLDFsq6M1-gCFQAAAAAdAAAAABAZ -->
      Source: <cite><a href="https://www.google.com/url?sa=i&url=https%3A%2F%2Fnaijaforbe.com%2Fhip-hop%2Fmp3-panic-disco-high-hopes%2F&psig=AOvVaw3mzLgNzwB1YVfkrfwOfpNQ&ust=1586375707665000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCLDFsq6M1-gCFQAAAAAdAAAAABAZ">Panic! at the Disco icon</a></cite>


      <!-- Source: https://www.google.com/url?sa=i&url=http%3A%2F%2Fsongsonlyric.blogspot.com%2F2016%2F10%2Fadam-levine-lost-stars-lyrics.html&psig=AOvVaw1xovwYSZiGjkM88QsdZpkE&ust=1586375934363000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCKjou4SN1-gCFQAAAAAdAAAAABAD -->
      Source: <cite><a href="https://www.google.com/url?sa=i&url=http%3A%2F%2Fsongsonlyric.blogspot.com%2F2016%2F10%2Fadam-levine-lost-stars-lyrics.html&psig=AOvVaw1xovwYSZiGjkM88QsdZpkE&ust=1586375934363000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCKjou4SN1-gCFQAAAAAdAAAAABAD">Adam Levine icon</a></cite>


      <!-- Source: https://www.google.com/url?sa=i&url=https%3A%2F%2Fmusiczone.ie%2Fproduct%2Fclean-bandit-what-is-love-limited-edition-deluxe-red-vinyl%2F&psig=AOvVaw3Gkq-tgLzSDQJ8A1b43V1s&ust=1586477322037000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCIiNo-CG2ugCFQAAAAAdAAAAABAX -->
      Source: <cite><a href="https://www.google.com/url?sa=i&url=https%3A%2F%2Fmusiczone.ie%2Fproduct%2Fclean-bandit-what-is-love-limited-edition-deluxe-red-vinyl%2F&psig=AOvVaw3Gkq-tgLzSDQJ8A1b43V1s&ust=1586477322037000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCIiNo-CG2ugCFQAAAAAdAAAAABAX">Clean Bandit icon</a></cite>

      <!-- Source: https://images.pexels.com/photos/4004374/pexels-photo-4004374.jpeg?cs=srgb&dl=white-feather-on-black-background-4004374.jpg&fm=jpg-->
      Source: <cite><a href="https://images.pexels.com/photos/4004374/pexels-photo-4004374.jpeg?cs=srgb&dl=white-feather-on-black-background-4004374.jpg&fm=jpg">Black feather image</a></cite>

      <!-- Source: https://images.pexels.com/photos/21148/pexels-photo.jpg?cs=srgb&dl=vinyl-music-play-spinning-21148.jpg&fm=jpg -->
      Source: <cite><a href="https://images.pexels.com/photos/21148/pexels-photo.jpg?cs=srgb&dl=vinyl-music-play-spinning-21148.jpg&fm=jpg">Vinyl Record image</a></cite>

      </div>

</body>

</html>
