<?php

$name = "21430597_1799315760102900_7989716472317131798_n.jpg";
$ext = end((explode(".", $name))); # extra () to prevent notice

echo $ext;

?>