<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "classes/trade.php";
include_once "util.php";

session_start();
$auth = new Auth();
$cards = new CardStorage();

if (isset($_GET["filter"]) && $_GET["filter"] !== "") {
    $filtered = $cards->get_by_type($_GET["filter"]);
} else {
    $filtered = $cards->all();
}

if ($auth->is_authenticated()) $uid = $auth->get("_id");
$trades = new TradeStorage();
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Főoldal</title>
        <link rel="stylesheet" href="styles/base.css">
    </head>

    <body>
        <header>
            <h1>Főoldal</h1>
            <nav>
                <?php if ($auth->is_authenticated()) { ?>
                    <a href="userinfo.php">Felhasználó részletek</a>
                    <a href="signout.php">Kijelentkezés</a>
                <?php } else { ?>        
                    <a href="signin.php">Bejelentkezés</a>
                    <a href="register.php">Regisztráció</a>
                <?php } ?>
                <?php if ($auth->is_authenticated() && $auth->get("level") === "admin") { ?>
                    <a href="admin.php">Adminisztráció</a>
                <?php } ?>
            </nav>
        </header>
        <main>
            <p>
                Üdövözöllek az illegális Pokémon kereskedelem világában!
                <br>
                Ezen az oldalon tudsz böngészni, illetve kereskedni élőlényekkel, hogy maximalizáld
                a profitodat!
            </p>
            <?php if ($auth->is_authenticated()) { ?>
                <h2>Üdv, <?= $_SESSION["user"] ?>!</h2>
                <p>Egyenleged: <b><?= $auth->get("balance") ?></b>.</p>
            <?php } ?>

            <form id="filter-form" action="" method="get">
                <label for="filter">Szűrés típus szerint:</label>
                <select name="filter" id="filter" onchange="submit();">
                    <option <?= (!isset($_GET["filter"]) || $_GET["filter"] === "") ? "selected" : "" ?> value="">-</option>
                    <?php
                    foreach ($types as $t) {
                    ?>
                        <option
                            <?= (isset($_GET["filter"]) && $_GET["filter"] === $t) ? "selected" : "" ?>
                            value=<?= $t ?>
                        ><?= ucfirst($t); ?></option>
                    <?php } ?>
                </select>
            </form>
            <?php
                $not_admin = $auth->is_authenticated() && $auth->get("level") !== "admin";
                $can_purchase = $not_admin && count($cards->get_by_user($uid)) < 5;
            ?>
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
                        <?php
                        if ($c->owner === null) {
                            if ($can_purchase) { ?>
                                <a class="purchase" href="purchase.php?id=<?= $c->_id ?>">
                                    Vásárlás: <?= $c->price ?>
                                </a>
                            <?php }
                        } elseif ($not_admin && $c->owner !== $uid && !$trades->trade_exists_by_for($uid, $c->_id)) { ?>
                            <a class="trade" href="start_trade.php?id=<?= $c->_id ?>">
                                Csere
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (empty($filtered)) echo "Nincs találat!"; ?>
            </div>
            <?php if ($can_purchase) { ?>
                <div id="random-section">
                    <a href="randpurchase.php">Véletlenszerű kártya 50 egyenlegért</a>
                </div>
            <?php } ?>
        </main>
    </body>

    <script>
        function submit() {
            document.getElementById('#filter-form').submit();
        }
    </script>
</html>