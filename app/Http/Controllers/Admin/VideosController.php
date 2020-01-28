<?php

namespace App\Http\Controllers\Admin;


use App\CourseVideo;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class VideosController
 * @package App\Http\Controllers\Admin
 */
class VideosController extends Controller
{
    public function index()
    {
        $videos = CourseVideo::select('*')->join('courses', 'courses.id', '=', 'course_videos.course_id')->all();
        return view('admin/videos/index', ['videos' => $videos]);
    }

    public function show($id)
    {
        return view('admin/videos/video');
    }

    public function edit($id)
    {
        return view('admin/videos/edit');
    }

    public function update(Request $request, $id)
    {
        return "Administration Videos Update - $id";
    }

    public function create()
    {
        return view('admin/videos/create');
    }

    public function store(Request $request)
    {
        return "Administration Videos Store";
    }

    public function delete(Request $request, $id)
    {
        return "Administration Videos Delete - $id";
    }
}
