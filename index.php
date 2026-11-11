<?php
session_start();
$error = False;
$error_msg = "";
$from_ext = False;

if (isset($_GET["p"])) {
    switch ($_GET["p"]) {
        case "login":
            $db = new SQLite3('pins.db', SQLITE3_OPEN_READONLY);
            $statement = $db->prepare('SELECT * FROM "User" WHERE "username" = :1 ');
            $statement->bindValue(':1', $_POST["username"]);
            $user = $statement->execute();
            $user = $user->fetchArray();

            if (count($user) > 0) {
                $password = trim($_POST["password"]) . $user["magic_word"];

                if (password_verify($password, $user["password"])) {
                    header("Location: index.php");
                    $_SESSION["in"] = TRUE;
                } else {
                    $error = True;
                    $error_msg = "Incorrect password.";
                };
            } else {
                $error = True;
                $error_msg = "Username doesn't exist.";
            };
            $db->close();
            break;
        case "logout":
            if (isset($_SESSION["in"])) {
                header("Location: index.php");
                $_SESSION["in"] = FALSE;
                session_destroy();
            };
            break;
        case "pin":
            if (isset($_SESSION["in"])) {
                include "includes/utils.php";
                $db = new SQLite3('pins.db', SQLITE3_OPEN_READWRITE);

                if (isset($_POST["private"])) {
                    $private = 1;
                } else {
                    $private = 0;
                };

                if (isset($_POST["source"])) {
                    $src = trim($_POST["source"]);
                } else {
                    $src = "";
                };

                if (isset($_POST["caption"])) {
                    $caption = trim($_POST["caption"]);
                } else {
                    $caption = "";
                };

                if (isset($_POST["tags"])) {
                    $tags = handleTags($_POST["tags"]);
                } else {
                    $tags = "";
                };

                $image_url = uploadImage($_POST["url"]);

                $statement = $db->prepare('INSERT INTO "Pins" ("image", "source", "caption", "private", "tags")
                VALUES (:1, :2, :3, :4, :5)');
                $statement->bindValue(':1', $image_url);
                $statement->bindValue(':2', $src);
                $statement->bindValue(':3', $caption);
                $statement->bindValue(':4', $private);
                $statement->bindValue(':5', $tags);
                $statement->execute();

                if (isset($_POST["boards"])) {
                    $id = $db->query('SELECT "id" FROM "Pins" ORDER BY "id" DESC LIMIT 1');
                    $id = $id->fetchArray()[0];
                    handleBoards($_POST["boards"], $id);
                };
                $db->close();
            } else {
                $error = True;
                $error_msg = "You have to be logged in to do that.";
            };
            break;
        case "board":
            if (isset($_SESSION["in"])) {
                $db = new SQLite3('pins.db', SQLITE3_OPEN_READWRITE);

                if (isset($_POST["private"])) {
                    $private = 1;
                } else {
                    $private = 0;
                };

                if (isset($_POST["description"])) {
                    $desc = trim($_POST["description"]);
                } else {
                    $desc = "";
                };

                $statement = $db->prepare('INSERT INTO "Boards" ("title", "description", "private", "pins")
                VALUES (:1, :2, :3, :4)');
                $statement->bindValue(':1', trim($_POST["title"]));
                $statement->bindValue(':2', $desc);
                $statement->bindValue(':3', $private);
                $statement->bindValue(':4', "");
                $statement->execute();
                $db->close();
            } else {
                $error = True;
                $error_msg = "You have to be logged in to do that.";
            };
            break;
        case "extension":
            if (isset($_SESSION["in"])) {
                if (isset($_POST["image"]) && isset($_POST["source"])) {
                    $from_ext = True;
                };
            };
            break;
    };
};
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="src/style.css">
        <link rel="shortcut icon" href="favicon.ico">
        <title>lil-pins</title>
    </head>
    <body>
        <?php include "includes/header.php"; ?>
        <?php include "includes/modal.php"; ?>
        <main>
            <?php
                $db = new SQLite3('pins.db', SQLITE3_OPEN_READONLY);
                $all_pins = $db->query('SELECT * FROM "Pins"');
                if (isset($_SESSION["in"])) {
                    $all_pins = $db->query('SELECT * FROM "Pins"');
                } else {
                    $all_pins = $db->query('SELECT * FROM "Pins" WHERE "private" = "0"');
                }
                $pin_data = array();
                while ($res = $all_pins->fetchArray()) {
                    array_push($pin_data, $res);
                }
                $all_pins->finalize();
                include "includes/pin_grid.php";
            ?>
        </main>
        <footer><p>End :)</p></footer>
    </body>
    <script src="src/modal.js" charset="utf-8"></script>
    <?php if (isset($from_ext) && $from_ext) {
        ?>
        <script type="text/javascript">
            document.getElementById("pin-dialog").showModal();
            displayImage(null, imgURL.value);
        </script>
        <?php
    } ?>
</html>
