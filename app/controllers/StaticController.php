<?php
namespace App\Controllers;

use App\Core\View;

class StaticController
{
    public function mentionsLegales(): void
    {
        View::render('static/mentionsView');
    }

    public function confidentialite(): void
    {
        View::render('static/confidentialiteView');
    }
}
