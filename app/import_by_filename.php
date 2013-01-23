#!/usr/bin/php
<?php


if (count($_SERVER['argv']) < 3) {
    echo "not enough options\n";
    exit;
}

$categoryID = intval($_SERVER['argv'][1]);
$uploads = '/home/aehparta/code/edb/web/uploads';
$path = $_SERVER['argv'][2];

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
    if (is_dir($path.'/'.$file))
        continue;
    $name = pathinfo($file, PATHINFO_FILENAME);
    $name = strtoupper($name);
    $articles[$name]['files'][] = $path.'/'.$file;
}

foreach ($articles as $name => $article) {
    $cmd = "./app/console edb:import $categoryID $name $uploads --force";
    foreach ($article['files'] as $file) {
        $cmd .= " --file='$file'";
    }
    //echo " - $name\n$cmd\n";
    system($cmd);
}

