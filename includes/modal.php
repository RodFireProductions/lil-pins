<?php
    if (!isset($_SESSION["in"])) { ?>
        <dialog id="login-dialog">
            <button commandfor="login-dialog" command="close">&#10005;</button>
            <form class="" action="index.php?p=login" method="post">
                <legend>Log In</legend>
                <p>
                    <label for="username">Username</label>:
                    <input type="text" name="username" id="username" pattern="[A-Za-z0-9-]{3,20}" required>
                </p><p>
                    <label for="password">Password</label>:
                    <input type="password" name="password" id="password" required>
                </p><p>
                    <input type="submit" id="submit" value="Submit">
                </p>
            </form>
        </dialog>
<?php
    } else { ?>
        <dialog id="pin-dialog">
            <button commandfor="pin-dialog" command="close">&#10005;</button>
            <form class="" action="index.php?p=pin" method="post">
                <legend>Create Pin</legend>
                <div class="two_col">
                    <div>
                        <label id="drop-zone">
                            Drop images here, or click to upload.
                            <input type="file" id="file-input" name="file-input" accept="image/*" class="hidden">
                        </label>
                        <img id="image-preview" class="hidden" src="" alt="Your image preview">
                        <button type="button" name="clear" id="clear">Clear</button>
                    </div>
                    <div>
                        <p>
                            <label for="url">Image URL</label>:
                            <input type="url" name="url" id="url" <?php
                                if (isset($from_ext) && $from_ext) {
                                    if (str_contains($_POST["image"], "http")) {
                                        $img = $_POST["image"];
                                    } else {
                                        $img = $_POST["source"]."/".$_POST["image"];
                                    };
                                    echo 'value="'.$img.'"';
                                };
                            ?> required>
                        </p><p>
                            <label for="url">Image Source</label>:
                            <input type="url" name="source" id="source" <?php
                                if (isset($from_ext) && $from_ext) {
                                    echo "value='".$_POST["source"]."'";
                                };
                            ?>>
                        </p><p>
                            <label for="caption">Caption</label>:<br>
                            <textarea name="caption" id="caption" rows="8" cols="50"></textarea>
                        </p><p>
                            <label>
                                Make private?
                                <input type="checkbox" name="private" id="private" value="yes">
                                <span></span>
                            </label>
                        </p>
                    </div>
                </div>
                <div class="two_col">
                    <div>
                        <p>
                            <label for="add-tag">Add new tag:</label>
                            <br>
                            <input type="text" name="add-tag" id="add-tag" value="">
                            <button type="button" id="add-tag-btn">Add</button>
                        </p>
                        <div class="">
                            <label for="tags">Tags (ctrl+click to select multiple)</label>
                            <br>
                            <select name="tags[]" id="tags" multiple>
                                <?php
                                $db = new SQLite3('pins.db', SQLITE3_OPEN_READONLY);
                                $result = $db->query('SELECT * FROM "Tags"');
                                while ($tag = $result->fetchArray()) {
                                    echo "<option value='" . $tag["id"] . "'>" . $tag["name"] . "</option>";
                                };
                                ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <p>
                            <label for="add-board">Add new board:</label>
                            <br>
                            <input type="text" name="add-board" id="add-board" value="">
                            <button type="button" id="add-board-btn">Add</button>
                        </p>
                        <div class="">
                            <label for="boards">Boards (ctrl+click to select multiple)</label>
                            <br>
                            <select name="boards[]" id="boards" multiple>
                                <?php
                                $result = $db->query('SELECT * FROM "Boards"');
                                while ($tag = $result->fetchArray()) {
                                    echo "<option value='" . $tag["id"] . "'>" . $tag["title"] . "</option>";
                                };
                                $db->close();
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <p>
                    <input type="submit" id="submit" value="Submit">
                </p>
            </form>
        </dialog>

        <dialog id="board-dialog">
            <button commandfor="board-dialog" command="close">&#10005;</button>
            <form class="" action="index.php?p=board" method="post">
                <legend>Create Board</legend>
                <p>
                    <label for="title">Title</label>:
                    <input type="text" name="title" id="title" required>
                </p><p>
                    <label for="description">Description</label>:<br>
                    <textarea name="description" id="description" rows="8" cols="50"></textarea>
                </p><p>
                    <label>
                        Make private?
                        <input type="checkbox" name="private" id="private" value="yes">
                        <span></span>
                    </label>
                </p><p>
                    <input type="submit" id="submit" value="Submit">
                </p>
            </form>
        </dialog>
<?php } ?>

<dialog id="main-pin-dialog">
    <button commandfor="main-pin-dialog" command="close">&#10005;</button>
    <img id="pin-img" src="" alt="">
    <div class="pin-info">
        <div id="pin-caption"></div>
        <ul id="pin-tags"></ul>
        <a id="pin-source" href="#" class="hidden" target="_blank">Source</a>
        <a id="pin-permalink" href="#">Permalink</a>
    </div>
</dialog>

<?php
if ($error) {
    ?>
    <p class="error">
        <?php echo $error_msg; ?>
    </p>
    <?php
}
?>
