<?php
require('Trello.Class.php');
require('Chatwork.Class.php');
require('InsertTaskToTrello.Class.php');

$T = new InsertTaskToTrello();
$T->execute();
