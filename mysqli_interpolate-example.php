<?php
require __DIR__.'/mysqli_interpolate.php';

$db = mysqli_connect('localhost', 'root', '', 'demo');

$s1 = mysqli_interpolate(
  $db,
  'select * from users where username=? limit 1',
  'noodlehaus'
);
$s1->execute();
var_dump($s1->get_result());
$s1->free_result();

$s2 = mysqli_interpolate($db, 'select * from users');
$s2->execute();
var_dump($s2->get_result());
$s2->free_result();
