<?php

$colors = [
        'black' => "[30m",
        'red' => "[31m",
        'green' => "[32m",
        'yellow' => "[33m",
        'blue' => "[34m",
        'magenta' => "[35m",
        'cyan' => "[36m",
        'white' => "[37m",
];

function console($text, $color="white")
{
    global $colors;
    if (isset($colors[$color]))
    {
        $color = $colors[$color];
    }
    else
    {
        $color = $colors["white"];
    }
    error_log("\n\n\033".$color.$text."\033[0m\n");    
}

function redirect($path)
{
    header("Location:".$path);
}


?>
