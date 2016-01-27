<?php
namespace Edu\Cnm\Zleyba\Clinstrument;
//require_once ("Item.php");

// secure PDO connection
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
$pdo = connectToEncryptedMySQL("/etc/apache2/data-design/zleyba.ini");
require_once("User.php");

//$item = new Item(null,1,"title2","dec","copy");
//$item->insert($pdo);


$user = new User(null,"john@email.com");
$user->insert($pdo);