<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "util.php";

session_start();
$auth = new Auth();
$cards = new CardStorage();

if (
    is_empty($_GET, "uid")
    || is_empty($_GET, "cid")
    || !$auth->is_authenticated()
    || $auth->get("_id") !== $_GET["uid"]
    || $auth->get("level") === "admin"
) {
    header("Location: index.php");
    exit();
}

$card = $cards->get_by_id($_GET["cid"]);
$cards->change_owner($_GET["cid"], null);
$auth->change_balance_by(ceil($card["price"]*0.9));
header("Location: userinfo.php");
exit();
?>