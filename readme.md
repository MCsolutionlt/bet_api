# Bet api
Olybet testinė užduotis

Aplikacija sukurta naudojant Lumen framework.
Aplikacija pasiekiama darant post metodą localhost:8000/bet/api jei paleisite pvz.: php -S localhost:8000 -t public
Post metodas daromas perduodant json data.
Duomenų bazių lentelės sukuriamos per: php artisan migrate

Nauji failai:
- app\Services kataloge sukurtas failas: BetService.php
BetService.php failę aprašytas perduodamos informacijos tikrinimas
- app\Http\Controllers pridėtas naujas controller'is BetController.php
- app kataloge sukurti: Bet.php, BetSelections.php, Player.php, BalanceTransactions.php

Pakoreguoti failai:
- routes kataloge web.php
