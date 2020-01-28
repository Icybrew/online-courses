<?php

namespace App;

use App\Core\Database\Model;

/**
 * Class User
 * @package Application
 */
class User extends Model
{
    protected static $table = "users";

    protected static $primary_key = "id";

}
