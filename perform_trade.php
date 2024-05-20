<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "classes/trade.php";
include_once "util.php";

if (is_empty($_GET, "id") || is_empty($_GET, "action")) {
    header("Location: index.php");
    exit();
}

session_start();
$auth = new Auth();
$cards = new CardStorage();
$trades = new TradeStorage();

if ($_GET["action"] === "accept") {
    $trade = $trades->get_by_id($_GET["id"]);
    if ($auth->get("_id") !== $trade["target_id"]) {
        header("Location: index.php");
        exit();
    }
    $cards->change_owner($trade["sender_card"], $trade["target_id"]);
    $cards->change_owner($trade["target_card"], $trade["sender_id"]);
    $trades->delete($_GET["id"]);
    $c1 = $trade["sender_card"];
    $c2 = $trade["target_card"];
    $trades->delete_where(function ($t) use ($c1, $c2) {
        return $t->sender_card === $c1
            || $t->target_card === $c1
            || $t->sender_card === $c2
            || $t->target_card === $c2;
    });

} elseif ($_GET["action"] === "cancel") {
    $_id = $auth->get("_id");
    $trade = $trades->get_by_id($_GET["id"]);
    if ($_id !== $trade["sender_id"] && $_id !== $trade["target_id"]) {
        header("Location: index.php");
        exit();
    }
    $trades->delete($_GET["id"]);
}
header("Location: userinfo.php");
exit();
?>