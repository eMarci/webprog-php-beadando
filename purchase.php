<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "util.php";

session_start();
$auth = new Auth();
$cards = new CardStorage();

if (is_empty($_GET, "id") || !$auth->is_authenticated()) {
    header("Location: index.php");
    exit();
}

$card = $cards->get_by_id($_GET["id"]);
if ($auth->get("balance") >= $card["price"] && count($cards->get_by_user($auth->get("_id"))) < 5) {
    $cards->change_owner($_GET["id"], $auth->get("_id"));
    $auth->change_balance_by(-1 * $card["price"]);
}
header("Location: index.php");
exit();
?>