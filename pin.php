<?php
session_start();
$error = False;
$error_msg = "";
$config = include('includes/config.php');

if (isset($_GET["p"]) && $_GET["p"] != "" && isset($_GET["from"]) && isset($_SESSION["in"])) {
    $db = new SQLite3($config->database, SQLITE3_OPEN_READWRITE);
    if ($_GET["from"] == "edit") {
        include "includes/utils.php";

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

        $statement = $db->prepare('UPDATE "Pins" SET "source" = :1, "caption" = :2, "private" = :3, "tags" = :4 WHERE "id" = :5');
        $statement->bindValue(":1", $src);
        $statement->bindValue(":2", $caption);
        $statement->bindValue(":3", $private);
        $statement->bindValue(":4", $tags);
        $statement->bindValue(":5", $_GET["p"]);
        $statement->execute();

        if (isset($_POST["boards"])) {
            $new_b = $_POST["boards"];
        } else {
            $new_b = array();
        };

        if (isset($_POST["current_b"])) {
            $current = explode(" ", $_POST["current_b"]);
            if (count($current) > 0) {
                foreach ($current as $b) {
                    if (!in_array($b, $new_b)) {
                        removeFromBoard($b, $_GET["p"]);
                    };
                };
            };
        };

        if (isset($_POST["boards"])) {
            handleBoards($_POST["boards"], $_GET["p"]);
        };
    } else if ($_GET["from"] == "delete") {
        $file = $db->prepare('SELECT "image" FROM "Pins" WHERE "id" = :1');
        $file->bindValue(":1", $_GET["p"]);
        $image = $file->execute();
        $image = $image->fetchArray()[0];
        unlink($image);

        $statement = $db->prepare('DELETE FROM "Pins" WHERE "id" = :1');
        $statement->bindValue(":1", $_GET["p"]);
        $statement->execute();
    };
    $db->close();
}

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
                $db = new SQLite3($config->database, SQLITE3_OPEN_READONLY);

                if (isset($_SESSION["in"])) {
                    $get = $db->prepare('SELECT * FROM "Pins" WHERE "id" = :1');
                } else {
                    $get = $db->prepare('SELECT * FROM "Pins" WHERE "id" = :1 and "private" = "0"');
                };
                $get->bindValue(":1", $_GET["p"]);
                $pin = $get->execute();
                $pin = $pin->fetchArray();

                if ($pin) {
                    $tags = array();
                    if ($pin["tags"] != "") {
                        foreach (explode(" ", $pin["tags"]) as $id) {
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
                            <div id="info">
                                <p class="caption"><?php echo $pin["caption"]; ?></p>
                                <ul><?php
                                    foreach ($tags as $t) {
                                        echo "<li><a href='tag.php?p=".$t["id"]."'>".$t["name"]."</a></li>";
                                    };
                                ?></ul>
                                <?php
                                if ($pin["source"] != "") {
                                    echo "<a class='source' href='".$pin["source"]."'>Source</a>";
                                };
                                ?>
                            </div>
                            <div class="hidden" id="edit-form">
                                <form class="" action="pin.php?p=<?php echo $pin["id"]; ?>&from=edit" method="post">
                                    <legend>Edit Pin</legend>
                                    <p>
                                        <label for="url">Image Source</label>:
                                        <input type="url" name="source" id="source" value="<?php echo $pin["source"]; ?>">
                                    </p><p>
                                        <label for="caption">Caption</label>:<br>
                                        <textarea name="caption" id="caption" rows="8" cols="50"><?php echo $pin["caption"]; ?></textarea>
                                    </p><p>
                                        <label>
                                            Make private?
                                            <input type="checkbox" name="private" id="private" value="yes"
                                            <?php if ($pin["private"] == 1) {
                                                echo "checked";
                                            }; ?>
                                            >
                                            <span></span>
                                        </label>
                                    </p>
                                    <div>
                                        <p>
                                            <label for="add-tag">Add new tag:</label>
                                            <br>
                                            <input type="text" name="add-tag" id="add-tag2" value="">
                                            <button type="button" id="add-tag-btn2">Add</button>
                                        </p>
                                        <div class="">
                                            <label for="tags">Tags (ctrl+click to select multiple)</label>
                                            <br>
                                            <select name="tags[]" id="tags2" multiple>
                                                <?php
                                                $result = $db->query('SELECT * FROM "Tags"');
                                                while ($tag = $result->fetchArray()) {
                                                    if (in_array($tag, $tags)) {
                                                        echo "<option selected value='" . $tag["id"] . "'>" . $tag["name"] . "</option>";
                                                    } else {
                                                        echo "<option value='" . $tag["id"] . "'>" . $tag["name"] . "</option>";
                                                    };
                                                };
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <p>
                                            <label for="add-board">Add new board:</label>
                                            <br>
                                            <input type="text" name="add-board" id="add-board2" value="">
                                            <button type="button" id="add-board-btn2">Add</button>
                                        </p>
                                        <div class="">
                                            <label for="boards">Boards (ctrl+click to select multiple)</label>
                                            <br>
                                            <select name="boards[]" id="boards2" multiple>
                                                <?php
                                                $result = $db->query('SELECT * FROM "Boards"');
                                                $current_b = array();
                                                while ($b = $result->fetchArray()) {
                                                    $b_pins = explode(" ", $b["pins"]);

                                                    if (in_array($pin["id"], $b_pins)) {
                                                        echo "<option selected value='" . $b["id"] . "'>" . $b["title"] . "</option>";
                                                        array_push($current_b, $b["id"]);
                                                    } else {
                                                        echo "<option value='" . $b["id"] . "'>" . $b["title"] . "</option>";
                                                    };
                                                };
                                                ?>
                                            </select>
                                            <?php
                                                if (count($current_b) > 0) {
                                                    $current_b = implode(" ", $current_b);
                                                    echo "<input name='current_b' type='text' class='hidden' value='".$current_b."'>";
                                                };
                                            ?>
                                        </div>
                                    </div>
                                    <p>
                                        <input type="submit" id="submit" value="Submit">
                                    </p>
                                </form>
                            </div>

                            <?php
                            if (isset($_SESSION["in"])) {
                                echo "<div class='edit'>";
                                echo "<a href='?p=".$_GET["p"]."&from=delete' class='button'>Delete</a>";
                                echo "<button type='button' id='edit' name='edit' data-which='pin'>Edit</button>";
                                echo "</div>";
                            };
                            $db->close();
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
    <?php
        if (isset($_SESSION["in"])) {
            echo "<script src='src/form.js' charset='utf-8'></script>";
            echo "<script src='src/edit.js' charset='utf-8'></script>";
        };
    ?>
</html>
