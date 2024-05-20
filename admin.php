<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "util.php";

session_start();
$auth = new Auth();
$cards = new CardStorage();

if (!$auth->is_authenticated() || $auth->get("level") !== "admin") {
    header("Location: index.php");
    exit();
}

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
        $cards->add(new Card(
            $_POST["name"],
            $_POST["type"],
            $_POST["health"],
            $_POST["attack"],
            $_POST["defense"],
            $_POST["price"],
            $_POST["description"],
            $_POST["image"]
        ));
        $_POST = array();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Adminisztráció</title>
        <link rel="stylesheet" href="styles/base.css">
    </head>

    <body>
        <header>
            <h1>Adminisztráció</h1>
            <nav><a href="index.php">Főoldal</a></nav>
        </header>

        <main>
            <h2>Kártya létrehozása</h2>
            <?php if ($errors) { ?>
                <ul>
                    <?php foreach ($errors as $e) { ?>
                        <li><?= $e ?></li>    
                    <?php } ?>
                </ul>
            <?php } elseif (!empty($_POST)) { ?>
                <p>Kártya "<?= $_POST["name"] ?>" sikeresen mentve.</p>
            <?php } ?>
            <form action="" method="post">
                <table>
                    <tr>
                        <td><label for="name">Név:</label></td>
                        <td><input novalidate type="text" id="name" name="name"></td>
                    </tr>
                    <tr>
                        <td><label for="type">Típus:</label></td>
                        <td>
                            <select id="type" name="type">
                                <?php foreach ($types as $t) { ?>
                                    <option value=<?= $t ?>><?= ucfirst($t); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="health">Életpontok:</label></td>
                        <td><input type="number" id="health" name="health"></td>
                    </tr>
                    <tr>
                        <td><label for="attack">Támadó erő:</label></td>
                        <td><input type="number" id="attack" name="attack"></td>
                    </tr>
                    <tr>
                        <td><label for="defense">Védekezés:</label></td>
                        <td><input type="number" id="defense" name="defense"></td>
                    </tr>
                    <tr>
                        <td><label for="price">Ár:</label></td>
                        <td><input type="number" id="price" name="price"></td>
                    </tr>
                    <tr>
                        <td><label for="description">Leírás:</label></td>
                        <td><input novalidate type="text" id="description" name="description"></td>
                    </tr>
                    <tr>
                        <td><label for="image">Kép URL:</label></td>
                        <td><input novalidate type="text" id="image" name="image"></td>
                    </tr>
                </table>
                <button type="submit">Mentés</button>
                <button type="reset">Törlés</button>
            </form>
            <h2>Kártya módosítása</h2>
            <div class="cards-container">
                <?php
                $filtered = $cards->get_by_user(null);
                foreach ($filtered as $c) { ?>
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
                        <a class="modify" href='modify.php?id=<?= $c->_id ?>'>Módosítás</a>
                    </div>
                <?php } ?>
                <?php if (empty($filtered)) echo "Nincs módosítható kártya!"; ?>
            </div>
        </main>
    </body>

</html>