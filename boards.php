<?php
session_start();
$error = False;
$error_msg = "";
$config = include('includes/config.php');

if (isset($_GET["p"]) && $_GET["p"] != "" && isset($_GET["from"]) && isset($_SESSION["in"])) {
    $db = new SQLite3($config->database, SQLITE3_OPEN_READWRITE);
    if ($_GET["from"] == "edit") {
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

        $statement = $db->prepare('UPDATE "Boards" SET "title" = :1, "description" = :2, "private" = :3 WHERE "id" = :4');
        $statement->bindValue(':1', trim($_POST["title"]));
        $statement->bindValue(':2', $desc);
        $statement->bindValue(':3', $private);
        $statement->bindValue(':4', $_GET["p"]);
        $statement->execute();
        $db->close();

    } else if ($_GET["from"] == "delete") {
        $statement = $db->prepare('DELETE FROM "Boards" WHERE "id" = :1');
        $statement->bindValue(":1", $_GET["p"]);
        $statement->execute();
    };
    $db->close();
};

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="src/style.css">
        <link rel="shortcut icon" href="favicon.ico">
        <title>Boards | lil-pins</title>
    </head>
    <body>
        <?php include "includes/header.php"; ?>
        <?php include "includes/modal.php"; ?>
        <main>
            <?php
                $db = new SQLite3($config->database, SQLITE3_OPEN_READONLY);

                if (!isset($_GET["p"]) || $_GET["p"] == "") {
                    if (isset($_SESSION["in"])) {
                        $all_boards = $db->query('SELECT * FROM "Boards"');
                    } else {
                        $all_boards = $db->query('SELECT * FROM "Boards" WHERE "private" = "0"');
                    };
                    $board_data = array();
                    while ($res = $all_boards->fetchArray()) {
                        array_push($board_data, $res);
                    };
                    $all_boards->finalize();

                    echo "<div class='boards'>";
                    foreach ($board_data as $board) {
                        if ($board["pins"] != "") {
                            $pin_num = count(explode(" ", $board["pins"]));
                        } else {
                            $pin_num = 0;
                        }

                        if ($board["description"] && strlen($board["description"]) > 30) {
                            $description = substr($board["description"], 0, 29)."…";
                        } else {
                            $description = $board["description"];
                        };
                        ?>
                        <div class="board">
                            <h2>
                                <a href="boards.php?p=<?php echo $board["id"] ?>"><?php echo $board["title"]; ?></a>
                                (<?php echo $pin_num; ?>)
                            </h2>
                            <?php if ($board["description"]) {
                                echo "<p>".$description."</p>";
                            }; ?>
                        </div>
                        <?php
                    };
                    echo "</div>";

                } else {
                    $get = $db->prepare('SELECT * FROM "Boards" WHERE "id" = :1');
                    $get->bindValue(":1", $_GET["p"]);
                    $board = $get->execute();
                    $board = $board->fetchArray();

                    if ($board) {
                        ?>
                        <section class="board-info">
                            <div id="info">
                                <h2><?php echo $board["title"]; ?></h2>
                                <p><?php echo $board["description"] ?></p>
                            </div>
                            <div class="hidden" id="edit-form">
                                <form class="" action="?p=<?php echo $_GET["p"]; ?>&from=edit" method="post">
                                    <legend>Edit Board</legend>
                                    <p>
                                        <label for="title">Title</label>:
                                        <input type="text" name="title" id="title" value="<?php echo $board["title"]; ?>" required>
                                    </p><p>
                                        <label for="description">Description</label>:<br>
                                        <textarea name="description" id="description" rows="8" cols="50"><?php $board["description"]; ?></textarea>
                                    </p><p>
                                        <label>
                                            Make private?
                                            <input type="checkbox" name="private" id="private" value="yes"
                                            <?php if ($board["private"] == 1) {
                                                echo "checked";
                                            }; ?>
                                            >
                                            <span></span>
                                        </label>
                                    </p><p>
                                        <input type="submit" id="submit" value="Submit">
                                    </p>
                                </form>
                            </div>
                            <?php
                            if (isset($_SESSION["in"])) {
                                echo "<div class='edit'>";
                                echo "<a href='?p=".$_GET["p"]."&from=delete' class='button'>Delete</a>";
                                echo "<button type='button' id='edit' name='edit' data-which='board'>Edit</button>";
                                echo "</div>";
                            };
                            ?>
                        </section>
                        <?php
                        $pin_ids = explode(" ", $board["pins"]);
                        $pin_data = array();
                        foreach ($pin_ids as $id) {
                            if (isset($_SESSION["in"])) {
                                $res = $db->prepare('SELECT * FROM "Pins" WHERE "id" = :1');
                            } else {
                                $res = $db->prepare('SELECT * FROM "Pins" WHERE "id" = :1 AND "private" = "0"');
                            };
                            $res->bindValue(":1", $id);
                            $res = $res->execute();
                            $res = $res->fetchArray();
                            if ($res) {
                                array_push($pin_data, $res);
                            };
                        };
                        include "includes/pin_grid.php";

                    } else {
                    echo "<p class='error'>This board is either private or doesn't exist.</p>";
                    };
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
            echo "<script src='src/edit.js' charset='utf-8'></script>";
        };
    ?>
</html>
