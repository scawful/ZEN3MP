<?php
namespace zen3mp;

require_once("src/config.php");
require_once("src/incl.php");
require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);
$utils = new Utils();

include("src/auth.php");
include("src/globals.php");
include("src/forms/requests_form.php");

$twig->addGlobal('requests', $requests);
$twig->addGlobal('confirm', $confirm);


echo $twig->render('social/requests.twig');
?>
