<?php

namespace App;

use App\Core\Database\Model;

/**
 * Class CourseVideo
 * @package Application
 */
class CourseVideo extends Model
{
    protected static $table = "course_videos";

    protected static $primary_key = "id";
}
