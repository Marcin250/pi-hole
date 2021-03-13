<?php

$urls = explode(PHP_EOL, file_get_contents('white_list.txt'));

array_walk($urls, function($url) {
    echo "Adding: {$url}";

    exec("pihole -w {$url}");
});