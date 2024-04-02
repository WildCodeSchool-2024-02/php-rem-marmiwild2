<?php

require __DIR__ . '/controllers/recipe-controller.php';

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ('/' === $urlPath) {
    browseRecipes();
} elseif ('/add' === $urlPath) {
    addRecipe();
} elseif ('/show' === $urlPath && isset($_GET['id'])) {
    showRecipe($_GET['id']);
} else {
    header('HTTP/1.1 404 Not Found');
}
