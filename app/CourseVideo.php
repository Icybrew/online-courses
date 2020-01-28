<?php

namespace App;

use App\Core\Database\Model;

/**
 * Class CourseVideo
 * @package Application
 */
class CourseVideo extends Model
{
    protected $table = "course_videos";

    protected $primary_key = "id";
}
