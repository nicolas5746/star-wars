<?php
// Use connection
include(__DIR__ . "/../conn/conn.php");
// Session id from index.php
$id = $_SESSION['id'];
$slide = false;
// Select all columns with matching id
$sql = "SELECT * FROM characters WHERE id = '$id';";
$query = mysqli_query($conn, $sql);
// Check number of rows y assign variables
if (mysqli_num_rows($query) > 0) {
    foreach ($query as $row) {
        $name = $row['display_name'];
        $full_name = $row['full_name'];
        $alias = $row['alias'];
        $title = $row['title'];
        $gender = $row['gender'];
        $species = $row['species'];
        $homeworld = $row['homeworld'];
        $born = $row['birth_year'];
        $died = $row['death_year'];

        $image_1 = $row['image_1'];
        $image_2 = $row['image_2'];
        $image_3 = $row['image_3'];

        $portrayed_1 = $row['portrayed_image_1'];
        $portrayed_2 = $row['portrayed_image_2'];
        $portrayed_3 = $row['portrayed_image_3'];
        // Slide images only if there are 3 of them
        ($row['image_2'] !== "" || $row['image_3'] !== "") ? $slide = true : $slide = false;
        // Separate name and full name into arrays
        $separated_name = explode(" ", $name);
        $separated_full_name = explode(" ", $full_name);
        // Check if name and full name have some duplicates
        $some_name_matches = array_intersect($separated_name, $separated_full_name);
    }
}

function getImages($query)
{
    if (mysqli_num_rows($query) > 0) {
        foreach ($query as $row) {
            $images = array();
            $no_picture = "https://res.cloudinary.com/dizcz3fgi/image/upload/v1733415277/no_picture_ix5lmr.png";

            $image_1 = ($row['image_1'] !== "") ? $row['image_1'] : $no_picture;
            array_push($images, $image_1); // add an image to images array

            if ($row['image_2'] !== "") {
                $image_2 = $row['image_2'];
                array_push($images, $image_2);
            }

            if ($row['image_3'] !== "") {
                $image_3 = $row['image_3'];
                array_push($images, $image_3);
            }

            $images_length = count($images); // Get length of images array
        }
        // Loop throughout images
        for ($i = 1; $i <= $images_length; $i++) {
            $image_number = "image_" . $i;
            $image = ($row[$image_number] !== "") ? $row[$image_number] : $no_picture;
            $portrayed_number = "portrayed_image_" . $i;
            $portrayed = ($row[$portrayed_number] !== "") ? htmlspecialchars($row[$portrayed_number], ENT_QUOTES) : "";
            echo '<div class="slide">
                <img
                    class="slide-image"
                    src="' . $image . '"
                    alt="' . $row['display_name'] . '"
                    title="' . $portrayed . '"
                />
            </div>';
        }
    }
}

$conn->close(); // Close connection

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include(__DIR__ . "/../ui/meta.php");  ?>
    <link rel="stylesheet" href="styles/character.css" type="text/css">
</head>

<body>
    <?php include(__DIR__ . "/../ui/header.php");  ?>
    <div class="slider-container">
        <?php if ($slide): ?>
        <div class="slider">
        <?php else: ?>
        <div class="no-slider">
        <?php
            endif;
            getImages($query);
        ?>
        </div>
    </div>
    <div class="character-details">
        <?php if ($name === $full_name || !$some_name_matches) : ?><p><span>Name:</span>&nbsp;&nbsp;<?= $name ?></p><?php endif; ?>
        <?php if ($name !== $full_name) : ?><p><span>Full Name:</span>&nbsp;&nbsp;<?= $full_name ?></p><?php endif; ?>
        <?php if ($alias !== "") : ?><p><span>Other Names:</span>&nbsp;&nbsp;<?= ucwords($alias) ?></p><?php endif; ?>
        <?php if ($title !== "") : ?><p class="titles"><span>Titles:</span>&nbsp;&nbsp;<?= $title ?></p><?php endif; ?>
        <p><span>Gender:</span>&nbsp;&nbsp;<?= ($gender === "n/a" ? strtoupper($gender) : ucfirst($gender)) ?></p>
        <p><span>Species:</span>&nbsp;&nbsp;<?= ucfirst($species) ?></p>
        <p><span>Homeworld:</span>&nbsp;&nbsp;<?= ($homeworld === "unknown" ? ucfirst($homeworld) : ucwords($homeworld)) ?></p>
        <p><span>Birth Year:</span>&nbsp;&nbsp;<?= ($born === "unknown" ? ucfirst($born) : strtoupper($born)) ?></p>
        <?php if ($died !== "") : ?><p><span>Death Year:</span>&nbsp;&nbsp;<?= ($died === "unknown") ? ucfirst($died) : strtoupper($died) ?></p><?php endif; ?>
    </div>
    <?php include(__DIR__ . "/../ui/footer.php");  ?>
</body>

</html>