<?php

$sig = file_get_contents('https://composer.github.io/installer.sig');
$sig = trim($sig);

copy('https://getcomposer.org/installer', 'composer-setup.php');

if (hash_file('sha384', 'composer-setup.php') === $sig) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;