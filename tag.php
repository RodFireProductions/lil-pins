<?php
session_start();
$error = False;
$error_msg = "";
$config = include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="src/style.css">
        <link rel="shortcut icon" href="favicon.ico">
        <title>Tags | lil-pins</title>
    </head>
    <body>
        <?php include "includes/header.php"; ?>
        <?php include "includes/modal.php"; ?>
        <main>
            <?php

            $db = new SQLite3($config->database, SQLITE3_OPEN_READONLY);
            $all_tags = $db->query('SELECT * FROM "Tags"');
            $tag_data = array();
            while ($res = $all_tags->fetchArray()) {
                array_push($tag_data, $res);
            };
            $all_tags->finalize();

            echo "<div class='tags'><h2>Tags</h2><ul>";
            foreach ($tag_data as $tag) {
                ?>
                <li>
                    <a
                        href="tag.php?p=<?php echo $tag["id"]; ?>"
                        class="<?php if (isset($_GET["p"]) && $tag["id"] == $_GET["p"]) {
                            echo "selected";
                        } ?>";
                    >
                        <?php echo $tag["name"]; ?>
                    </a>
                </li>
                <?php
            };
            echo "</ul></div>";

            $exists = null;
            if (isset($_GET["p"])) {
                foreach ($tag_data as $tag) {
                    if ($tag["id"] == $_GET["p"]) {
                        $exists = True;
                    };
                };
            };

            if ($exists) {
                $all_pins = $db->query('SELECT * FROM "Pins"');
                if (isset($_SESSION["in"])) {
                    $all_pins = $db->query('SELECT * FROM "Pins"');
                } else {
                    $all_pins = $db->query('SELECT * FROM "Pins" WHERE "private" = "0"');
                }
                $pins = array();
                while ($res = $all_pins->fetchArray()) {
                    array_push($pins, $res);
                }
                $all_pins->finalize();

                $pin_data = array();
                foreach ($pins as $pin) {
                    if ($pin["tags"] != "") {
                        $pin_tags = explode(" ", $pin["tags"]);
                        if (in_array($_GET["p"], $pin_tags)) {
                            array_push($pin_data, $pin);
                        }
                    }
                }

                include "includes/pin_grid.php";

            } else {
                echo "<p>No pins here.</p>";
            };

            $db->close();
            ?>
        </main>
        <footer><p>End :)</p></footer>
    </body>
    <script src="src/modal.js" charset="utf-8"></script>
    <?php
        if (isset($_SESSION["in"])) {
            echo "<script src='src/form.js' charset='utf-8'></script>";
        };
    ?>
</html>
