<?php
function is_empty(array $input, string $key): bool
{
    return !(isset($input[$key]) && trim($input[$key]) !== "");
}

function any(array $arr, callable $pred): bool
{
    foreach ($arr as $value) {
        if ($pred($value)) {
            return true;
        }
    }
    return false;
}

$types = ["normal", "fire", "water", "electric", "grass", "ice", "fighting",
    "poison", "ground", "flying", "psychic", "bug", "rock", "ghost",
    "dragon", "dark", "steel", "fairy", "stellar"];

$colors = [
    "normal" => "rgb(168, 168, 120)",
    "fire" => "rgb(240, 128, 48)",
    "water" => "rgb(104, 144, 240)",
    "electric" => "rgb(248, 208, 48)",
    "grass" => "rgb(120, 200, 80)",
    "ice" => "rgb(152, 216, 216)",
    "fighting" => "rgb(192, 48, 40)",
    "poison" => "rgb(160, 64, 160)",
    "ground" => "rgb(224, 192, 104)",
    "flying" => "rgb(168, 144, 240)",
    "psychic" => "rgb(248, 88, 136)",
    "bug" => "rgb(168, 184, 32)",
    "rock" => "rgb(184, 160, 56)",
    "ghost" => "rgb(112, 88, 152)",
    "dragon" => "rgb(112, 56, 248)",
    "dark" => "rgb(112, 88, 72)",
    "steel" => "rgb(184, 184, 208)",
    "fairy" => "rgb(240, 182, 188)",
    "stellar" => "rgb(53, 172, 231)"
];
?>