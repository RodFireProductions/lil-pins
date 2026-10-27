<div class="pin_grid">
<?php

$all_tags = $db->query('SELECT * FROM "Tags"');
$tag_data = array();
while ($res = $all_tags->fetchArray()) {
    array_push($tag_data, $res);
}
$all_tags->finalize();
$db->close();

foreach ($pin_data as $pin) {

    if (strlen($pin["caption"]) > 30) {
        $caption = substr($pin["caption"], 0, 29)."…";
    } else {
        $caption = $pin["caption"];
    };

    if ($pin["tags"] != "") {
        $tags = explode(" ", $pin["tags"]);
    } else {
        $tags = "";
    };

    $tag_names = array();
    if ($tags != "") {
        foreach ($tags as $tag) {
            $index = intval($tag)-1;
            array_push($tag_names, $tag_data[$index]["name"]);
        };
    }

    ?>
    <figure class="pin">
        <button
            type="button" class="pin-btn" command="show-modal" commandfor="main-pin-dialog"
            name="<?php echo "pin-".$pin["id"]; ?>"
            data-id="<?php echo $pin["id"]; ?>"
            data-tags="<?php echo $pin["tags"]; ?>"
            data-names="<?php echo implode("&&", $tag_names); ?>"
            data-src="<?php echo $pin["source"]; ?>"
            data-img="<?php echo $pin["image"]; ?>"
            data-caption="<?php echo $pin["caption"]; ?>"
            data-private="<?php echo $pin["private"]; ?>"
        >
            <img src="<?php echo $pin["image"]; ?>">
        </button>
        <?php if ($caption != "") {
            ?>
                <figcaption><?php echo $caption; ?></figcaption>
            <?php
        } ?>
        <?php if ($pin["tags"] != "") { ?>
            <div class="tags">
                <?php
                for ($i = 0; $i < count($tags); $i++) {
                    ?>
                    <a href="pin.php?p=<?php echo $tags[$i] ?>"><?php echo $tag_names[$i] ?></a>
                    <?php
                };
                ?>
            </div>
        <?php }; ?>
    </figure>
<?php }; ?>
</div>
