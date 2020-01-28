<?php

namespace App;

use App\Core\Database\Model;

/**
 * Class Role
 * @package Application
 */
class Role extends Model
{
    protected $table = "roles";

    protected $primary_key = "id";
}
