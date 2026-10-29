<header>
    <h1><a href="index.php">lil-pins</a></h1>
    <nav class="middle">
        <?php
            if (isset($_SESSION["in"])) {
                ?>
                <div class="dropdown">
                    Create
                    <div class="dropdown-content">
                        <button type="button" name="create-pin" command="show-modal" commandfor="pin-dialog">Pin</button>
                        <button type="button" name="create-board" command="show-modal" commandfor="board-dialog">Board</button>
                    </div>
                </div>
                <a href="boards.php">Boards</a>
                <a href="#">Get Extension</a>
        <?php
        } else {
            echo '<a href="boards.php">Boards</a>';
        }
        ?>
    </nav>
    <?php
        if (!isset($_SESSION["in"])) { ?>
            <button type="button" name="login" command="show-modal" commandfor="login-dialog">Log In</button>
            <?php
        } else {
            ?>
            <a href="index.php?p=logout">Log Out</a>
            <?php
        }
    ?>
</header>
<noscript>
    This website needs Javascript enabled to run properly, sorry.
</noscript>
