<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "classes/trade.php";
include_once "util.php";

session_start();
$auth = new Auth();
$cards = new CardStorage();

if (is_empty($_GET, "id") || !$auth->is_authenticated()) {
    header("Location: index.php");
    exit();
}

if (!is_empty($_POST, "cid")) {
    $_id = $auth->get("_id");
    $trades = new TradeStorage();
    
    $trades->request(new Trade(
        $_id,                                       // jelenleg aktív felhasználó ID, 
        $_POST["cid"],                              // általa kiválasztott saját kártya ID,
        $cards->get_by_id($_GET["id"])["owner"],    // csere cél-kártya tulajdonosa
        $_GET["id"]));                              // csere cél-kártya
    header("Location: index.php");
    exit();
}

$filtered = $cards->get_by_user($auth->get("_id"));
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Csere</title>
        <link rel="stylesheet" href="styles/base.css">
    </head>

    <body>
        <header>
            <h1>Csere</h1>
            <nav><a href="index.php">Főoldal</a></nav>
        </header>
        <main>
            <?php $can_purchase = $auth->is_authenticated() && $auth->get("level") !== "admin" && count($cards->get_by_user($auth->get("_id"))) < 5; ?>
            <div class="cards-container">
                <?php foreach ($filtered as $c) { ?>
                    <div class="card">
                        <img class=<?= "c-" . $c->type ?> src=<?= $c->image ?> alt="Kép">
                        <div class="card-info">
                            <h3><a href=<?= "details.php?id=" . $c->_id ?>><?= $c->name ?></a></h3>
                            <p><i><?= $c->type ?></i></p>
                            <div class="stats">
                                <span><span class="icon">❤</span><?= $c->health ?></span>
                                <span><span class="icon">⚔</span><?= $c->attack ?></span>
                                <span><span class="icon">🛡</span><?= $c->defense ?></span>
                            </div>
                        </div>
                        <form action="" method="post">
                            <input type="text" name="cid" id="cid" value=<?= $c->_id ?> hidden>
                            <button class="trade" type="submit">Csere</button>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </main>
    </body>

</html>