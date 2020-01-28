<?php

namespace App;

use App\Core\Database\Model;

/**
 * Class User
 * @package Application
 */
class User extends Model
{
    protected $table = "users";

    protected $primary_key = "id";

}
