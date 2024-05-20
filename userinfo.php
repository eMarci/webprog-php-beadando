<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "classes/trade.php";

session_start();
$auth = new Auth();
if (!$auth->is_authenticated()) {
    header("Location: index.php");
    exit();
}

$cards = new CardStorage();
if ($auth->get("level") === "admin") {
    $user_cards = $cards->get_by_user(null);
} else {
    $user_cards = $cards->get_by_user($auth->get("_id"));
}

$uid = $auth->get("_id");
$trades = new TradeStorage();
$sent = $trades->get_sent($uid);
$received = $trades->get_received($uid);
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="styles/base.css">
    </head>

    <body>
        <header>
            <h1>Felhasználó részletek</h1>
            <nav><a href="index.php">Főoldal</a></nav>
        </header>
        <main>
            <h2>Felhasználó:
                <?= $_SESSION["user"] ?>
            </h2>
            <p>Email cím:
                <?= $auth->get("email"); ?>
            </p>
            <p>Egyenleg:
                <?= $auth->get("balance"); ?>
            </p>
            <div class="cards-container">
                <?php foreach ($user_cards as $c) { ?>
                    <div class="card">
                        <img class=<?= "c-" . $c->type ?> src=<?= $c->image ?> alt="Kép">
                        <div class="card-info">
                            <h3><a href=<?= "details.php?id=" . $c->_id ?>><?= $c->name ?></a></h3>
                            <p><i>
                                    <?= $c->type ?>
                                </i></p>
                            <div class="stats">
                                <span><span class="icon">❤</span>
                                    <?= $c->health ?>
                                </span>
                                <span><span class="icon">⚔</span>
                                    <?= $c->attack ?>
                                </span>
                                <span><span class="icon">🛡</span>
                                    <?= $c->defense ?>
                                </span>
                            </div>
                        </div>
                        <?php $can_sell = $auth->is_authenticated() && $auth->get("level") !== "admin" ?>
                        <?php if ($can_sell) { ?>
                            <a class="sell" href='sell.php?uid=<?= $auth->get("_id") ?>&cid=<?= $c->_id ?>'>
                                Eladás:
                                <?= ceil($c->price * 0.9) ?>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php if (!empty($sent) || !empty($received)) { ?>
                <h2>Felhasználó cseréi</h2>
                <?php if (!empty($sent)) { ?>
                    <h3>Elküldött csere kérelmek</h3>
                    <div class="trades-col">
                        <?php foreach ($sent as $s) { ?>
                            <div class="trade-entry">
                                <div class="cards-container shrunk">
                                    <?php echo Card::basic($cards->get_by_id($s->sender_card)); ?>
                                </div>
                                <p>kártyádat szeretnéd elcserélni egy</p>
                                <div class="cards-container shrunk">
                                    <?php echo Card::basic($cards->get_by_id($s->target_card)); ?>
                                </div>
                                <p>kártyára.</p>
                                <a href="perform_trade.php?id=<?= $s->_id ?>&action=cancel">Visszavonás</a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($received)) { ?>
                    <h3>Beérkezett csere kérelmek</h3>
                    <div class="trades-col">
                        <?php foreach ($received as $r) { ?>
                            <div class="trade-entry">
                                <div class="cards-container shrunk">
                                    <?php echo Card::basic($cards->get_by_id($r->sender_card)); ?>
                                </div>
                                <p>kártyáját szeretné elcserélni veled valaki egy</p>
                                <div class="cards-container shrunk">
                                    <?php echo Card::basic($cards->get_by_id($r->target_card)); ?>
                                </div>
                                <p>kártyádra.</p>
                                <a href="perform_trade.php?id=<?= $r->_id ?>&action=accept">Elfogadás</a>
                                <a href="perform_trade.php?id=<?= $r->_id ?>&action=cancel">Elutasítás</a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </main>
    </body>

</html>