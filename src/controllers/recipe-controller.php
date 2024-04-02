<?php

require __DIR__ . '/../models/recipe-model.php';

function browseRecipes(): void
{
    $recipes = getAllRecipes();

    require __DIR__ . '/../views/indexRecipe.php';
}

function addRecipe(): void
{
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === 'POST') {
        $recipe = array_map('trim', $_POST);

        // Validate data
        if (empty($recipe['title'])) {
            $errors[] = 'The title is required';
        }
        if (empty($recipe['description'])) {
            $errors[] = 'The description is required';
        }
        if (!empty($recipe['title']) && strlen($recipe['title']) > 255) {
            $errors[] = 'The title should be less than 255 characters';
        }

        // Save the recipe
        if (empty($errors)) {
            saveRecipe($recipe);
            header('Location: /');
        }
    }
    // Generate the web page
    require __DIR__ . '/../views/form.php';
}

function showRecipe(int $id): void
{
    $id = $_GET['id'];
    if (empty($id)) {
        die("Wrong input parameter");
    }

    // Fetching a recipe
    $recipe = getRecipeById($id);

    // Database result check
    if (!isset($recipe['title']) || !isset($recipe['description'])) {
        header("HTTP/1.1 404 Not Found");
        die("Recipe not found");
    }

    // Generate the web page
    require __DIR__ . '/../views/showRecipe.php';
}
