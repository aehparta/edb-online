<?php

$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

for ($i = 0; $i < strlen($chars); $i++) {
	$c1 = $chars[$i];
	mkdir("./$c1");
	for ($j = 0; $j < strlen($chars); $j++) {
		$c2 = $chars[$j];
                echo "$c1/$c2\n";
		mkdir("./$c1/$c2");
        }
}
