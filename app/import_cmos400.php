#!/usr/bin/php
<?php

$categoryID = 48;
$uploads = '/home/aehparta/code/edb/web/uploads';
$path = '/data/pub/electronics/Components/Sorted/LOGIC/CMOS4000/';

$files = array();

$dh = opendir($path);
if (!$dh)
    exit(1);

while (($file = readdir($dh)) !== false) {
    if (is_dir($file))
        continue;
    $files[] = $file;
}

closedir($dh);

$articles = array();
foreach ($files as $file) {
    preg_match('/([^0-9]*)([0-9]+)/', $file, $matches);
    $name = $matches[2];

    if (strlen($name) < 4) {
        echo " ! $name is too short, skip..";
        continue;
    }

    if (!array_key_exists($name, $articles)) {
        $articles[$name] = array();
        $articles[$name]['files'] = array();
    } else {
        echo " - article exists, just add new file\n";
    }

    $articles[$name]['files'][] = $path.$file;
}

foreach ($articles as $name => $article) {
    $cmd = "./app/console edb:import $categoryID $name $uploads --force --file=".implode(' --file=', $article['files']);
    echo " - $name\n$cmd\n";
    //system($cmd);
}

