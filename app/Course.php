<?php

namespace App;

use App\Core\Database\Model;

/**
 * Class Course
 * @package Application
 */
class Course extends Model
{
    protected $table = "courses";

    protected $primary_key = "id";
}
