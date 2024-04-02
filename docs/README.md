# Marmiwild - épisode 2

Bravo ! Grâce à ton travail, le site Marmiwild a pris forme.  
Le bouche à oreille fait son œuvre, et tu passes vite le seuil des 3 followers sur Instawild.  
Ivre de succès, tu te demandes comment améliorer ton site pour satisfaire ta communauté.  
Rajouter un menu jambon-beurre ? Un carrousel ?  
Après avoir posté un sondage sur les réseaux sociaux, les résultats sont sans appel : "Tes followers trouvent moche de voir des `.php` dans les URLs du site..."  

![](images/justine-top.gif)
{: .text-center }

## Pure coquetterie ou vrai besoin&nbsp;?

La question mérite d'être posée.  

En soi, ça peut paraître un détail anodin. Mais pour les non-devs qui visitent ton site, l'extension `.php` dans l'url est pour le moins inutile. Peu leur importe que tu aies codé le site en PHP, ou en autre chose.  

De plus, si tu décides de changer de techno, tu rendras ces URL obsolètes.
Si tu abandonnes PHP (idée folle il est vrai 🙄, mais imaginons), est-ce que ça veut dire que toutes tes URLs vont changer ? 
Est-ce que mes liens favoris vont devenir des liens morts ?  


Côté dev, cela pose une autre question : pour l'instant, chaque fois que tu veux rajouter une page, tu dois créer un nouveau fichier. 
Ces fichiers ont par ailleurs des répétitions entre eux, ne serait-ce que d'inclure le modèle qui leur fournit des données. 
Il y a sûrement moyen d'avancer encore un peu plus sur le chemin de la modularité et d'obtenir des "*clean URLs*", c'est à dire "plus propre". 

Clone ce dépôt grâce au lien donné au début de cette page ⬆ à&nbsp;la&nbsp;section&nbsp;<a href="#input-clone"><i class="bi bi-code-slash"></i>&nbsp;Code</a>. 
{: .alert-info } 

## Objectifs

* Mettre en place un contrôleur frontal
* Rendre les URLs "propres"
* Construire les pages grâce à des actions


## 1. Un fichier pour les gouverner tous

Crée un dossier nommé `public` à la racine de ton projet dans lequel tu vas ajouter un nouveau fichier `index.php` avec ce contenu :

```php
<?php

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo $urlPath;
```

Lance maintenant un serveur depuis la racine du projet :

```bash
php -S localhost:8000 -t public
```

Tu noteras l'utilisation de l'option `-t` pour définir le dossier racine de ton serveur.
Il va considérer que l'adresse localhost:8000 correspond au dossier `public`.

Ouvre maintenant l'URL <http://localhost:8000/hello>, ou l'URL <http://localhost:8000/wilder>.  
Quelle est cette sorcellerie 🧙‍♂️ ?  

La fonction `parse_url()` avec l'option **PHP_URL_PATH** analyse la chaîne passée en premier paramètre. Ici, il s'agit de l'URL de la page en cours dont le nom est stocké dans la superglobale `$_SERVER` à l'index **REQUEST_URI**.  
`$urlPath` contient ainsi uniquement ce qui se trouve après le "**http://localhost:8000**".

Modifie maintenant l'`index.php` du répertoire `public` comme suit :

```php
<?php

require __DIR__ . '/../config.php';

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ('/' === $urlPath) {
    require __DIR__ . '/../index.php';
} else {
    header('HTTP/1.1 404 Not Found');
}
```

Cet unique fichier `index.php` dans le dossier `public` va être ton seul **point d'entrée** sur ton site. C'est ce qu'on appelle un **contrôleur frontal**. C'est ce fichier qui va gérer toutes les requêtes vers tes pages web.

Ici, le bloc `if/else` va analyser l'url demandée (`$urlPath`) et va appeler le fichier `.php` que tu souhaites associer.
Dans le cas présent, si la partie finale de l'URL correspond à la chaîne de caractère "**/**", ton script charge l'autre fichier `index.php` qui se trouve actuellement à la racine du projet. Note que le slash est ajouté automatiquement au bout de **localhost:8000** si tu ne l'ajoutes pas toi même dans la barre d'adresse du navigateur.


Si tu saisis une autre URL non gérée, tu rentres alors dans le `else`. Dans ce dernier cas, la fonction `header()` utilisée ici permet de renvoyer un code d'erreur HTTP **404** qui signifie que la page demandée n'existe pas.

Note que c'est **toi qui décides** du nom des URL correspondant à chaque page.  
Tu peux par exemple choisir une autre URL pour le script `index.php` en changeant la condition `'/' === $urlPath` en `'/home' === $urlPath`.
Mais si tu fais ça, tu dois prendre conscience que tu n'as plus de page d'accueil par défaut. Ce qui est la norme, car assez pratique.

## 2. Nouvelles URLs, nouveau rangement

Maintenant, tu n'es plus obligé de garder tous ces fichiers *.php* à la racine du site.
En fait, tu n'es même plus obligé de garder un fichier par page.
En partant de la racine du projet, crée un fichier `src/controllers/recipe-controller.php`.
Remplis-le avec le contenu suivant :

```php
<?php

require __DIR__ . '/../models/recipe-model.php';

function browseRecipes(): void
{
    $recipes = getAllRecipes();

    require __DIR__ . '/../views/indexRecipe.php';
}
```

Modifie maintenant le contrôleur frontal de manière à :

- Faire appel au fichier `recipe-controller.php`;
- Ne plus faire directement un `require` de `/../index.php`, mais faire appel à la fonction `browseRecipes()` dans ton `if` pour l'url de base.

Cela te donne quelque chose comme ceci :

```php
<?php

require __DIR__ . '/../config.php';
require __DIR__ . '/../src/controllers/recipe-controller.php';

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ('/' === $urlPath) {
    browseRecipes();
} else {
    header('HTTP/1.1 404 Not Found');
}
```

Tu as ici transformé l'appel à un fichier en un appel à une fonction. La fonction représente l'action de parcourir les recettes, d'où le nom **browseRecipes()**.  
Encore une étape de franchie ! Tu as séparé "l'action" de son "URL".

## 3. Sur la route

Plutôt que des URLs, nous allons maintenant parler de routes : un "path" associé à une action.
Pour marquer le coup, modifie `public/index.php` pour qu'il ne reste plus que les 2 lignes ci-dessous :

```php
<?php

require __DIR__ . '/../config.php';
require __DIR__ . '/../src/routing.php';
```

Et crée un fichier `src/routing.php` ou tu déplaceras le code précédent, comme ceci :

```php
<?php

require __DIR__ . '/controllers/recipe-controller.php';

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ('/' === $urlPath) {
    browseRecipes();
} else {
    header('HTTP/1.1 404 Not Found');
}
```

Ton contrôleur frontal devient plus net : il charge uniquement la configuration du projet, via le fichier `config.php`, ainsi que les routes depuis le fichier `routing.php`. On ne peux faire plus explicite !

## 4. Dernier challenge pour la route

C'est bien, ta page d'accueil fonctionne parfaitement comme avant mais tu as perdu des pages dans la bataille. Déclare les routes manquantes de manière à rendre fonctionnelles les pages qui se cachaient auparavant derrière `http://localhost:8000/show.php?id=1` et `http://localhost:8000/add.php`.

Tente de le faire par toi même, mais au cas où, voici la solution pour le routing:

```php
<?php

require __DIR__.'/controllers/recipe-controller.php';

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ('/' === $urlPath) {
    browseRecipes();
} elseif ('/show' === $urlPath && isset($_GET['id'])) {
    showRecipe($_GET['id']);
} elseif ('/add' === $urlPath) {
    addRecipe();
} else {
    header('HTTP/1.1 404 Not Found');
}
```

À toi de compléter `src/controllers/recipe-controller.php` avec les actions `showRecipe` et `addRecipe`.
Tu pourras à terme supprimer les fichiers `index.php`, `show.php` et `add.php` qui trainent encore à la racine du projet.

**Important :** pense à changer les attributs `href` dans tes vues avec les nouvelles URLs 😉.
{: .alert-warning }