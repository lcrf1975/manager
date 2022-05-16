#!/usr/bin/php -q

<?php

$Pass = $argv[1];

echo "******************************\n";
echo "* Manager Password Generator *\n";
echo "******************************\n";
echo "Password: " . $Pass . "\n";
echo "MD5.....: " . md5 ($Pass) . "\n\n";

?>
