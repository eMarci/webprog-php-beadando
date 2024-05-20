<?php
include_once "classes/card.php";
include_once "classes/auth.php";
include_once "util.php";

session_start();
$auth = new Auth();
$cards = new CardStorage();

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$card = $cards->get_by_id($_GET["id"]);
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
        <header style='background-color: <?= $colors[$card["type"]] ?>'>
            <h1>Részletek</h1>
            <nav><a href="index.php">Főoldal</a></nav>
        </header>
        <main>
            <h2><?= $card["name"] ?></h2>
            <div class="row">
                <table>
                    <tr>
                        <td>Típus</td>
                        <td><?= $card["type"] ?></td>
                    </tr>
                    <tr>
                        <td>Életpontok</td>
                        <td><?= $card["health"] ?></td>
                    </tr>
                    <tr>
                        <td>Támadó erő</td>
                        <td><?= $card["attack"] ?></td>
                    </tr>
                    <tr>
                        <td>Védekezés</td>
                        <td><?= $card["defense"] ?></td>
                    </tr>
                    <tr>
                        <td>Ár</td>
                        <td><?= $card["price"] ?></td>
                    </tr>
                    <?php if ($auth->get("level") === "admin") { ?>
                        <tr>
                            <td>ID</td>
                            <td><?= $card["_id"] ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <div class="col">
                    <div><img class=<?= "c-" . $card["type"] ?> src=<?= $card["image"] ?> alt="Pokemon képe."></div>
                    <p><?= $card["description"] ?></p>
                </div>
            </div>
        </main>
    </body>

</html>