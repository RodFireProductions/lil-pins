<?php

function uploadImage($url) {
    if (str_contains($url, "base64")) {
        $file_type = explode('/', mime_content_type($url))[1];
        $b64 = explode(',', $url)[1];
        $b64 = base64_decode($b64);
        $url = $b64;
    } else {
        $file_type = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        $url = file_get_contents($url);
    };

    if (!file_exists("media/")) {
        mkdir("media/", 0775, true);
    };

    $hex = bin2hex(random_bytes(10));
    $image = "media/".$hex.".".$file_type;

    file_put_contents($image, $url);

    return $image;
};

function createTag($tag) {
    global $config;
    $db = new SQLite3($config->database, SQLITE3_OPEN_READWRITE);
    $statement = $db->prepare('INSERT INTO "Tags" ("name") VALUES (:1)');
    $statement->bindValue(":1", $tag);
    $statement->execute();

    $id = $db->query('SELECT "id" FROM "Tags" ORDER BY "id" DESC LIMIT 1');
    $id = $id->fetchArray()[0];
    $db->close();

    return $id;
};

function handleTags($tags) {
    $array = array();

    foreach ($tags as $t) {
        if (str_contains($t, "#")) {
            $id = createTag(explode("#", $t)[1]);
            array_push($array, $id);
        } else {
            array_push($array, $t);
        }
    };

    $array = implode(" ", $array);

    return $array;
};

function createBoard($board) {
    global $config;
    $db = new SQLite3($config->database, SQLITE3_OPEN_READWRITE);
    $statement = $db->prepare('INSERT INTO "Boards" ("title", "private") VALUES (:1, "0")');
    $statement->bindValue(":1", $board);
    $statement->execute();

    $id = $db->query('SELECT "id" FROM "Boards" ORDER BY "id" DESC LIMIT 1');
    $id = $id->fetchArray()[0];
    $db->close();

    return $id;
};

function addToBoard($id, $pin) {
    global $config;
    $db = new SQLite3($config->database, SQLITE3_OPEN_READWRITE);

    $pins = $db->prepare('SELECT "pins" FROM "Boards" WHERE "id" = :1');
    $pins->bindValue(":1", $id);
    $pins = $pins->execute();
    $pins = $pins->fetchArray()[0];

    if ($pins == "") {
        $pins = $pin;
    } else {
        $e_pins = explode(" ", $pins);
        if (!in_array($pin, $e_pins)) {
            $pins = $pins." ".$pin;
        };
    };

    $statement = $db->prepare('UPDATE "Boards" SET "pins" = :1 WHERE "id" = :2');
    $statement->bindValue(":1", $pins);
    $statement->bindValue(":2", $id);
    $statement->execute();

    $db->close();
};

function removeFromBoard($id, $pin) {
    global $config;
    $db = new SQLite3($config->database, SQLITE3_OPEN_READWRITE);

    $pins = $db->prepare('SELECT "pins" FROM "Boards" WHERE "id" = :1');
    $pins->bindValue(":1", $id);
    $pins = $pins->execute();
    $pins = $pins->fetchArray()[0];

    if ($pins != "") {
        for ($i = 0; $i < count($e_pins); $i++) {
            echo $e_pins[$i]."<br>";
            if ($pin == $e_pins[$i]) {
                unset($e_pins[$i]);
            };
        };
    };

    $pins = implode(" ", $e_pins);

    $statement = $db->prepare('UPDATE "Boards" SET "pins" =:1 WHERE "id" = :2');
    $statement->bindValue(":1", $pins);
    $statement->bindValue(":2", $id);
    $statement->execute();
}

function handleBoards($boards, $pin) {
    foreach ($boards as $b) {
        if (str_contains($b, "#")) {
            $id = createBoard(explode("#", $b)[1]);
        } else {
            $id = $b;
        }
        addToBoard($id, $pin);
    };
};

?>
