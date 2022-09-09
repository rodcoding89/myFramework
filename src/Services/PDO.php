<?php

namespace App\Services;

class PDO{
    private $db ;
    public function __construct(string $host,string $db, string $user, string $pw)
    {
        //$db = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $pw);
        //$db = new PDO()
    }
}