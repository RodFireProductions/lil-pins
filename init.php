<?php
if (!file_exists("pins.db") && !(isset($_GET["p"]) && $_GET["p"] == "create")) {
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="src/style.css">
        <link rel="shortcut icon" href="favicon.ico">
        <title>lil-pins initializing</title>
        <style>
            body {
                padding: 1em 5em;
                display: grid;
                grid-auto-flow: row;
                justify-content: center;
            }
        </style>
    </head>
    <body>
        <h1>Create Admin Account</h1>
        <form action="?p=create" method="post">
            <p>
                <label for="username">Username</label>:
                <input type="text" name="username" id="username" pattern="[A-Za-z0-9-]{3,20}" required>
            </p><p>
                <label for="password">Password</label>:
                <input type="password" name="password" id="password" required>
            </p>
            <p><p>
                <label for="magic_word">What's your favorite animal?</label>:
                <input type="text" name="magic_word" id="magic_word" required>
            </p><p>
                <input type="submit" id="submit" value="Create">
            </p>
        </form>
    </body>
</html>
<?php
} else if (isset($_GET["p"]) && $_GET["p"] == "create") {
    /* Creating Database */

    $db = new SQLite3('pins.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

    $db->query('CREATE TABLE IF NOT EXISTS "User" (
        "id" INTEGER PRIMARY KEY NOT NULL,
        "about" TEXT, /* Site Description */
        "username" TEXT,
        "password" TEXT,
        "magic_word" TEXT
    )');

    $db->query('CREATE TABLE IF NOT EXISTS "Boards" (
        "id" INTEGER PRIMARY KEY NOT NULL,
        "title" TEXT,
        "description" TEXT,
        "private" INTEGER,
        "pins" TEXT /* List of pin IDs */
    )');

    $db->query('CREATE TABLE IF NOT EXISTS "Pins" (
        "id" INTEGER PRIMARY KEY NOT NULL,
        "image" TEXT, /* URL to image */
        "source" TEXT,
        "caption" TEXT,
        "private" INTEGER,
        "tags" TEXT /* List of tag IDs */
    )');

    $db->query('CREATE TABLE IF NOT EXISTS "Tags" (
        "id" INTEGER PRIMARY KEY NOT NULL,
        "name" TEXT
    )');

    /* Creating User */
    $magicpassword = trim($_POST['password']) . $_POST["magic_word"];
    $password = password_hash($magicpassword, PASSWORD_DEFAULT);
    $statement = $db->prepare('INSERT INTO "User" ("username", "password", "about", "magic_word")
    VALUES (:1, :2, :3, :4)');
    $statement->bindValue(':1', $_POST["username"]);
    $statement->bindValue(':2', $password);
    $statement->bindValue(':3', "A person worthy of love.");
    $statement->bindValue(':4', $_POST["magic_word"]);
    $statement->execute();

    $db->close();

    echo "Admin created. This file can be deleted.";


} else {
    echo "You can delete this file.";
}
?>
