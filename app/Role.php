<?php

namespace App;

use App\Core\Database\Model;

/**
 * Class Role
 * @package Application
 */
class Role extends Model
{
    protected static $table = "roles";

    protected static $primary_key = "id";
}
