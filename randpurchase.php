<?php
include_once "classes/auth.php";
include_once "classes/card.php";
include_once "util.php";

session_start();
$auth = new Auth();
$cards = new CardStorage();

if (!$auth->is_authenticated()) {
    header("Location: index.php");
    exit();
}

$filtered = array_filter($cards->all(), function ($card) {
    return $card->owner === null;
});

if (empty($filtered)) {
    header("Location: index.php");
    exit();
}

$rand = array_rand($filtered);

if ($auth->get("balance") >= 50 && count($cards->get_by_user($auth->get("_id"))) < 5) {
    $cards->change_owner($rand, $auth->get("_id"));
    $auth->change_balance_by(-50);
}
header("Location: index.php");
exit();
?>