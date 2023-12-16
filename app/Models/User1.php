<?php
namespace App\Models;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class User1 extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'users';

    // Define your model attributes and relationships here
}
