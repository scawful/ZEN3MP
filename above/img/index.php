<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../src/config.php');
require_once("../../src/incl.php");

require '../../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);

require_once("../../src/auth.php");
require_once("../../src/globals.php");
require_once("../func.php");

echo $twig->render('above/image.twig');

?>