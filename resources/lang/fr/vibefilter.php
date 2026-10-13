```php
<?php

return [

    'number' => [
        'decimal' => ',',
        'thousands' => ' ',
    ],

    'filter' => [
        'label' => 'Ambiance',
        'statement' => 'Afficher les lignes pour lesquelles…',
        'placeholder' => 'Le client est en colère',
        'indicator' => 'Ambiance : :statement',
    ],

    'column' => [
        'label' => 'Score',
    ],

    'limit' => [
        'title' => ':count ligne nécessite un nouveau score|:count lignes nécessitent un nouveau score',
        'body' => 'Le tableau contient :total lignes après application des autres filtres et de la recherche, dont :unscored sans score en cache pour cette affirmation. La limite est de :limit.',
        'hint' => 'Affinez les résultats à l’aide d’autres filtres ou d’une recherche pour respecter cette limite, ou lancez le calcul pour toutes les lignes dès maintenant. Le tableau n’est pas filtré avant cela.',
        'run_anyway' => 'Exécuter quand même',
    ],

    'progress' => [
        'scoring' => 'Calcul du score pour :count ligne|Calcul du score pour :count lignes',
        'requests' => ':done / :total requête terminée|:done / :total requêtes terminées',
        'retried' => ':count nouvelle tentative',
        'label' => 'Progression de Vibefilter',
    ],

    'report' => [
        'title' => ':matching lignes sur :total correspondent à l’ambiance',
        'scored' => 'Score calculé pour :count ligne|Score calculé pour :count lignes',
        'scored_in' => 'Score calculé pour :count ligne en :requests requêtes|Score calculé pour :count lignes en :requests requêtes',
        'requests' => ':count requête|:count requêtes',
        'retried' => ':count nouvelle tentative après l’absence de réponse de l’API',
        'cost' => "Coût :\u{00A0}\$:amount",
        'seconds' => "Durée :\u{00A0}:seconds\u{00A0}s",
        'cached' => ':count depuis le cache',
    ],

    'failure' => [
        'title' => 'Vibefilter n’a pas pu s’exécuter',
        'unfiltered' => 'Le tableau n’est pas filtré.',
        'partial_title' => ':scored lignes sur :total ont obtenu un score',
        'partial_body' => 'L’API n’a pas répondu pour :count ligne. Le tableau affiche donc uniquement les correspondances parmi les lignes évaluées. Une nouvelle tentative ne traitera que la ligne manquante :count.|L’API n’a pas répondu pour :count lignes. Le tableau affiche donc uniquement les correspondances parmi les lignes évaluées. Une nouvelle tentative ne traitera que les :count lignes manquantes.',
        'try_again' => 'Réessayer',
    ],

];
```
