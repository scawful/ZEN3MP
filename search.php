<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

require_once('src/config.php');
require_once('src/incl.php');

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

require_once("src/auth.php");
require_once("src/globals.php");
require_once("src/forms/search_form.php");

$twig->addGlobal('search_string', $search_string);
$twig->addGlobal('prompt_string', $prompt_string);

echo $twig->render('social/search.twig');
?>
