<?php

include 'Locale.php';
use MartijnOud\Locale;
$Translate = new Locale('en');

echo $Translate->__('GREETING :name HAVE NICE :day', array(':name' => 'Martijn', ':day' => date('l')));
echo '<br>';
echo $Translate->__('TRANSLATE_ME_PLX1');