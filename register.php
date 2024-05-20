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
    if (is_empty($input, "email")) {
        $errors[] = "Email cím megadása kötelező.";
    } elseif (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Érvénytelen email cím.";
    }
    if (is_empty($input, "password") || is_empty($input, "password_rep")) {
        $errors[] = "Jelszó megadása kötelező.";
    } elseif ($input["password"] !== $input["password_rep"]) {
        $errors[] = "A jelszavaknak egyeznie kell.";
    }
    if (count($errors) == 0) {
        if ($auth->user_exists($input["username"])) {
            $errors[] = "A felhasználó már létezik.";
        }
    }

    return !(bool) $errors;
}

$errors = [];
if (count($_POST) != 0) {
    if (validate($_POST, $errors, $auth)) {
        $filtered = array_filter($_POST, function ($e) {
            return $e !== "password_rep";
        }, ARRAY_FILTER_USE_KEY);
        $filtered["balance"] = 500;
        $filtered["level"] = "user";
        $auth->register($filtered);
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
            <h1>Regisztráció</h1>
            <nav><a href="index.php">Főoldal</a></nav>
        </header>

        <main>
            <?php if ($errors) { ?>
                <div id="errors">
                    <ul>
                        <?php foreach ($errors as $e) { ?>
                            <li><?= $e ?></li>
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
                        <td><input novalidate type="text" id="username" name="username" value=<?= field_value("username", $errors); ?>></td>
                    </tr>
                    <tr>
                        <td><label for="email">Email cím:</label></td>
                        <td><input novalidate type="text" id="email" name="email" value=<?= field_value("email", $errors); ?>></td>
                    </tr>
                    <tr>
                        <td><label for="password">Jelszó:</label></td>
                        <td><input novalidate type="password" id="password" name="password"></td>
                    </tr>
                    <tr>
                        <td><label for="password_rep">Jelszó újra:</label></td>
                        <td><input novalidate type="password" id="password_rep" name="password_rep"></td>
                    </tr>
                </table>
                <input novalidate type="submit" value="Regisztráció">
            </form>
            <a href="signin.php">Bejelentkezés</a>
        </main>
    </body>

</html>