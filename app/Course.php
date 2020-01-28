<?php

namespace App;

use App\Core\Database\Model;

/**
 * Class Course
 * @package Application
 */
class Course extends Model
{
    protected static $table = "courses";

    protected static $primary_key = "id";
}
