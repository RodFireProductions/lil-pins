<?php
session_start();
$error = False;
$error_msg = "";
// TODO: Add editing
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="src/style.css">
        <link rel="shortcut icon" href="favicon.ico">
        <title>Pin | lil-pin </title>
    </head>
    <body>
        <?php include "includes/header.php"; ?>
        <?php include "includes/modal.php"; ?>
        <main>
            <?php
            if (isset($_GET["p"]) && $_GET["p"] != "") {
                $db = new SQLite3('pins.db', SQLITE3_OPEN_READONLY);

                if (isset($_SESSION["in"])) {
                    $get = $db->prepare('SELECT * FROM "Pins" WHERE "id" = :1');
                } else {
                    $get = $db->prepare('SELECT * FROM "Pins" WHERE "id" = :1 and "private" = "0"');
                };
                $get->bindValue(":1", $_GET["p"]);
                $pin = $get->execute();
                $pin = $pin->fetchArray();

                if ($pin) {
                    if ($pin["tags"] != "") {
                        $tags = array();
                        foreach (explode(" ",$pin["tags"]) as $id) {
                            $tag = $db->prepare('SELECT * FROM "Tags" WHERE "id" = :1');
                            $tag->bindValue(":1", $id);
                            $tag = $tag->execute();
                            $tag = $tag->fetchArray();
                            array_push($tags, $tag);
                        };
                    };

                    ?>
                    <article class="big-pin two_col">
                        <div>
                            <img src="<?php echo $pin["image"]; ?>" alt="Pin #<?php echo $pin["id"] ?>">
                        </div>
                        <div class="info">
                            <p class="caption"><?php echo $pin["caption"]; ?></p>
                            <ul><?php
                                foreach ($tags as $t) {
                                    echo "<li><a href='pin.php?p=".$t["id"]."'>".$t["name"]."</a></li>";
                                };
                            ?></ul>
                            <?php
                            if ($pin["source"] != "") {
                                echo "<a class='source' href='".$pin["source"]."'>Source</a>";
                            }
                            ?>
                        </div>
                    </article>
                    <?php
                } else {
                    echo "<p class='error'>Pin doesn't exist or is private.<p>";
                };

            } else {
                echo "<p class='error'>No pin selected.</p>";
            };

            ?>
        </main>
        <footer><p>End :)</p></footer>
    </body>
    <script src="src/modal.js" charset="utf-8"></script>
</html>
