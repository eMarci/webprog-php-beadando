<?php
include_once "classes/auth.php";
include_once "util.php";

session_start();
$auth = new Auth();

function validate(array $input, array &$errors, Auth $auth): bool
{
    if (is_empty($input, "username")) {
        $errors[] = "Felhasználónév megadása kötelező.";
    }
    if (is_empty($input, "password")) {
        $errors[] = "Jelszó megadása kötelező.";
    }
    if (count($errors) == 0) {
        if (!$auth->check_credentials($_POST["username"], $_POST["password"])) {
            $errors[] = "Érvénytelen felhasználónév vagy jelszó.";
        }
    }

    return !(bool) $errors;
}

$errors = [];
if (count($_POST) != 0) {
    if (validate($_POST, $errors, $auth)) {
        $auth->login($_POST);
        header("Location: index.php");
        exit();
    }
}
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
            <h1>Bejelentkezés</h1>
            <nav><a href="index.php">Főoldal</a></nav>
        </header>

        <main>
            <?php if ($errors) { ?>
                <div id="errors">
                    <ul>
                        <?php foreach ($errors as $e) { ?>
                            <li>
                                <?= $e ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php
            function field_value(string $key, &$errors): string
            {
                return ($errors && isset($_POST[$key]) ? $_POST[$key] : "");
            }
            ?>
            <form action="" method="post">
                <table>
                    <tr>
                        <td><label for="username">Felhasználónév:</label></td>
                        <td><input novalidate type="text" id="username" name="username" value=<?= field_value("username", $errors); ?>>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="password">Jelszó:</label></td>
                        <td><input novalidate type="password" id="password" name="password"></td>
                    </tr>
                </table>
                <input novalidate type="submit" value="Regisztráció">
            </form>
            <a href="register.php">Regisztráció</a>
        </main>
    </body>

</html>