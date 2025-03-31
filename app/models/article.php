<?php
// app/models/article.php

// Nécessaire pour l'autoload PSR-4 avec Composer
namespace App\Models; 


class Article 
{
    private $conn;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    
}