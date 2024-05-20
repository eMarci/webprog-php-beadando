<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "util.php";

session_start();
$auth = new Auth();
$cards = new CardStorage();

if (is_empty($_GET, "id")) {
    if ($auth->get("level") === "admin") {
        header("Location: admin.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

$card = $cards->get_by_id($_GET["id"]);

$fields = ["name", "type", "health", "attack", "defense", "price", "description", "image"];

function validate(array $input, array &$errors): bool
{
    global $fields;
    if (any($fields, function ($f) use ($input) {
        return is_empty($input, $f);
    })) {
        $errors[] = "Minden mező megadása kötelező!";
    }

    return !(bool) $errors;
}

$errors = [];
if (count($_POST) !== 0) {
    if (validate($_POST, $errors)) {
        $cards->update($_GET["id"], $_POST);
        header("Location: admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Adminisztráció: Kártya módosítása</title>
        <link rel="stylesheet" href="styles/base.css">
    </head>

    <body>
        <header>
            <h1>Adminisztráció: Kártya módosítása</h1>
            <nav><a href="admin.php">Adminisztráció</a></nav>
        </header>

        <main>
            <h2>Kártya módosítása</h2>
            <?php if ($errors) { ?>
                <ul>
                    <?php foreach ($errors as $e) { ?>
                        <li>
                            <?= $e ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
            <form action="" method="post">
                <table>
                    <tr>
                        <td><label for="name">Név:</label></td>
                        <td><input novalidate type="text" id="name" name="name" value=<?= $card["name"] ?>></td>
                    </tr>
                    <tr>
                        <td><label for="type">Típus:</label></td>
                        <td>
                            <select id="type" name="type">
                                <?php foreach ($types as $t) { ?>
                                    <option
                                        <?= $card["type"] === $t ? "selected" : "" ?>    
                                        value=<?= $t ?>><?= ucfirst($t); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="health">Életpontok:</label></td>
                        <td><input type="number" id="health" name="health" value=<?= $card["health"] ?>></td>
                    </tr>
                    <tr>
                        <td><label for="attack">Támadó erő:</label></td>
                        <td><input type="number" id="attack" name="attack" value=<?= $card["attack"] ?>></td>
                    </tr>
                    <tr>
                        <td><label for="defense">Védekezés:</label></td>
                        <td><input type="number" id="defense" name="defense" value=<?= $card["defense"] ?>></td>
                    </tr>
                    <tr>
                        <td><label for="price">Ár:</label></td>
                        <td><input type="number" id="price" name="price" value=<?= $card["price"] ?>></td>
                    </tr>
                    <tr>
                        <td><label for="description">Leírás:</label></td>
                        <td><textarea novalidate id="description" name="description"><?= $card["description"] ?></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="image">Kép URL:</label></td>
                        <td><input novalidate type="text" id="image" name="image" value=<?= $card["image"] ?>></td>
                    </tr>
                </table>
                <button type="submit">Mentés</button>
            </form>
        </main>
    </body>

</html>