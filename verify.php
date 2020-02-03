<?php
namespace zen3mp;
use zen3mp\Utils as Utils;
require('src/config.php');
require_once('src/incl.php');

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

include("src/auth.php");
include("src/globals.php");
include("src/forms/verify_form.php");

echo $twig->render('social/verify.twig');

?>
