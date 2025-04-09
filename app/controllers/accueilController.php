<?php
namespace App\Controllers;

use App\Core\View;

class AccueilController
{
    public function index(): void
    {
        View::render('accueil/indexView');
    }
}
