<?php

if (file_exists("pins.db")) {

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="src/style.css">
        <link rel="shortcut icon" href="favicon.ico">
        <title>Forgot login | lil-pins</title>
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
        <h1 style="text-align:center;">Forgot Login</h1>
        <p>
            If you haven't forgotten your login info and don't need to update it,
            <b>delete this file</b>.
        </p>
        <form enctype="multipart/form-data" action="?p=reset" method="post">
            <p style="text-align:center;">Reset Login:</p>
            <p>
                <label for="username">Username</label>:
                <input type="text" name="username" id="username" patter="[A-Za-z0-9-]{3,20}" required>
            </p><p>
                <label for="password">Password</label>:
                <input type="password" name="password" id="password" required>
            </p><p>
                <label for="magic_word">What's your favorite animal again?</label>:
                <input type="text" name="magic_word" id="magic_word" required>
            </p><p>
                <input type="submit" id="submit" value="Reset">
            </p>
        </form>
     </body>
 </html>

<?php } else if (isset($_GET["p"]) && $_GET["p"] == "reset") {

    $db = new SQLite3('pins.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

    // Encrpyting Password
    $magicpassword = $_POST['password'] . $_POST["magic_word"];
    $password = trim(password_hash($magicpassword, PASSWORD_DEFAULT));

    // Updating Login
    $statement = $db->prepare('UPDATE "User" SET "username" = :1, "password" = :2, "magic_word" = :3, WHERE "id" = 1');
    $statement->bindValue(':1', $_POST["username"]);
    $statement->bindValue(':2', $password);
    $statement->bindValue(':3', $magic_word);
    $statement->execute();

    $db->close();

    echo "Login changed. <b>Delete this file</b>.";

} else {
    echo "<b>Delete this file</b>.";
}
?>
