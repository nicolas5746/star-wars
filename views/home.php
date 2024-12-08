<?php
// Use connection
include(__DIR__ . "/../conn/conn.php");

$no_results = false;
$searching = false;
$search_results = 0; // Number of search results

if (isset($_POST['search'])) {
  $search_query = $_POST['search'];
  // Select all columns that have name, alias or title alike or match search input
  $sql = "SELECT * FROM characters WHERE CONCAT_WS(display_name, full_name, alias, title) LIKE '%$search_query%' LIMIT 0, 12;";
  $query = mysqli_query($conn, $sql); // Perform query in database
  $search_results = mysqli_num_rows($query); // Number of rows
  // Check if form has been submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If 0 results, no results were found message will display
    ($search_results > 0) ? $searching = true : $no_results = true;
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include(__DIR__ . "/../ui/meta.php");  ?>
  <link rel="stylesheet" href="styles/home.css" type="text/css" />
</head>

<body>
  <?php include(__DIR__ . "/../ui/header.php");  ?>
  <form class="form" method="POST">
    <input class="search-box" name="search" placeholder="Enter character name" type="text" required />
    <input class="search-button" name="submit" type="submit" value="Search" />
  </form>
  <main class="main">
    <?php if ($no_results): ?>
      <div class="no-results">
        <p>No results were found</p>
      </div>
    <?php else: ?>
      <div id="cards" class="cards">
        <?php
        if ($searching) {
          while ($row = mysqli_fetch_assoc($query)) {
            $id = $row['id'];
            $name = $row['display_name'];
            $no_picture = "https://res.cloudinary.com/dizcz3fgi/image/upload/v1733415277/no_picture_ix5lmr.png";
            $image = ($row['image_1'] !== "") ? $row['image_1'] : $no_picture; // If image_1 is emtpy, display a default image
            echo '<div class="card">
              <!-- Testing in XAMPP <a href="/swapi/character/id/..."> -->
              <a href="/character/id/' . $id . '">
                <img
                  class="card-image"
                  alt="' . ucwords($name) . '"
                  src="' . $image . '"
                  title="' . ucwords($name) . '"
                />
              </a>
              <p>' . mb_strtoupper($name) . '</p>
            </div>';
          }
        }
        ?>
      </div>
    <?php endif; ?>
  </main>
  <?php include(__DIR__ . "/../ui/footer.php");  ?>
  <script
    src="https://code.jquery.com/jquery-3.7.1.js"
    integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
  <script type="text/javascript">
    let index = 0; // First item showed
    let limit = 12; // Last item showed
    let maxReached = false; // Set if maximum items are reached
    let searchResults = <?= $search_results; ?>; // Use php variable of search results

    const getData = () => {
      if (maxReached) return; // If maximum is reached, function does nothing
      // Perform the ajax request
      $.ajax({
        method: 'POST',
        url: './data/data.php',
        data: {
          getData: 1,
          index: index,
          limit: limit
        },
        dataType: 'text',
        success: (response) => {
          if (response === 'Maximum reached') {
            maxReached = true; // data.php sends the message that maximum has been reached
          } else {
            index += limit; // New index
            $('#cards').append(response); // Append response to id
          }
        }
      });
    }
    // Execute only if we are not searching
    if (<?= json_encode(!$searching && !$no_results); ?>) {
      $(document).ready(() => {
        getData();
      });
      // Check if bottom of page is reached whether is desktop or mobile
      $(window).on('scroll', () => {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const bottomIsReached = ($(window).height() + $(window).scrollTop()) - $(document).height() === 0;
        const mobileBottomIsReached = Math.ceil($(window).scrollTop()) + window.innerHeight === Math.ceil($(document).height());

        if (isMobile && mobileBottomIsReached || bottomIsReached) getData();
      });
    }
    // Style changes according to window dimensions
    if (searchResults === 1) $('#cards').css({ 'grid-template-columns': 'auto' });
    if (searchResults === 2 && $(window).width() > 768) $('#cards').css({ 'grid-template-columns': 'repeat(2, 1fr)' });
    if (searchResults === 3 && $(window).width() > 1600) $('#cards').css({ 'grid-template-columns': 'repeat(3, 1fr)' });
  </script>
</body>

</html>