<?php

namespace App\Http\Controllers\Admin;

use App\CourseVideo;
use App\Http\Controllers\Controller;
use App\Services\Youtube;
use Symfony\Component\HttpFoundation\Request;

use App\Course;


/**
 * Class CoursesController
 * @package App\Http\Controllers\Admin
 */
class CoursesController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('admin/courses/index', ['courses' => $courses]);
    }

    public function show($id)
    {
        $course = Course::find($id);

        if (!empty($course)) {
            return view('admin/courses/course', ['course' => $course]);
        } else {
            return view('errors/error404');
        }
    }

    public function edit($id)
    {
        $course = Course::find($id);

        if (!empty($course)) {
            $course->videos = CourseVideo::where('course_id', '=', $course->id)->getAll();
            return view('admin/courses/edit', ['course' => $course]);
        } else {
            return view('errors/error404');
        }
    }

    public function update(Request $request, $id)
    {
        if (!empty($course)) {
            return redirect()->route('admin.courses.index')->withErrors(['error' => 'Tokio kurso nėra']);
        }

        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $cover_image = $request->files->get('cover_image');
        $price = $request->request->get('price');
        $price_discounted = $request->request->get('price_discounted');
        $price_discounted_expires = $request->request->get('price_discounted_expires');
        $isPublic = $request->request->get('isPublic');
        $videos = $request->request->get('videos');

        $allowed_file_types = [
            'image/png',
            'image/jpeg',
            'image/gif',
            'application/octet-stream'
        ];

        $errors = [];

        // Validation
        if (!isset($name) || strlen($name) == 0) {
            $errors['title'] = 'Neivestas pavadinimas';
        } else if (strlen($name) < 3) {
            $errors['title'] = 'Pavadinimas negali būti trumpesnis negu 3 simboliai';
        }

        if (!isset($description) || strlen($description) == 0) {
            $errors['description'] = 'Neivestas aprašymas';
        } else if (strlen($description) < 3) {
            $errors['description'] = 'Aprašymas negali būti trumpesnis negu 3 simboliai';
        }

        if (isset($cover_image) && !in_array($cover_image->getClientMimeType(), $allowed_file_types)) {
            $errors['cover_image'] = 'Paveikslėlis turi būti PNG/JPEG/GIF formato';
        }

        if (!isset($price) || strlen($price) == 0) {
            $errors['price'] = 'Neivesta kaina';
        } else if (doubleval($price) == 0) {
            $errors['price'] = 'Kaina turi būti iš skaičių';
        }

        if ((!isset($price_discounted) || strlen($price_discounted) == 0) && (isset($price_discounted_expires) && strlen($price_discounted_expires) > 0)) {
            $errors['price_discounted'] = 'Neivesta nuolaidos kaina';
        } else if ((isset($price_discounted) && strlen($price_discounted) > 0) && (!isset($price_discounted_expires) || strlen($price_discounted_expires) == 0)) {
            $errors['price_discounted_expires'] = 'Neivesta nuolaidos galiojimo data';
        }

        if (count($errors) > 0) {
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $course = Course::find($id);

        $file_destination = __DIR__ . '/../../../../public/images/courses/';
        $file_name = date('Y-m-d_H-i-s') . '.' . pathinfo($cover_image->getClientOriginalName(), PATHINFO_EXTENSION);

        if ($cover_image) {
            $upload_result = move_uploaded_file($cover_image->getPathName(), $file_destination . $file_name);

            if ($upload_result != true) {
                return redirect()->back()->withErrors(['error' => 'Nepavyko įkelti paveikslėlio']);
            }
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'price' => doubleval($price),
            'price_discount' => doubleval($price_discounted) != 0 ? doubleval($price_discounted) : null,
            'price_discount_expires' => strlen($price_discounted_expires) > 0 ? date('Y-m-d H:i:s', strtotime($price_discounted_expires)) : null,
            'public' => isset($isPublic) ? 1 : 0
        ];

        if ($cover_image) {
            $data['cover_image'] = $file_name;
        }

        $result = Course::update($course->id, $data);

        if ($result) {
            return redirect()->route('admin.courses.index')->with(['success' => 'Kurso duomenys atnaujinti']);
        } else {
            return redirect()->route('admin.courses.index')->withErrors(['error' => $result]);
        }

    }

    public function create()
    {

        $youtube = new Youtube('s');

        dd($youtube->get_MY_ChannelVideos());
        //dd($youtube->getVideoInfo('rie-hPVJ7Sw'));

        return view('admin/courses/create');
    }

    public function store(Request $request)
    {
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $cover_image = $request->files->get('cover_image');
        $price = $request->request->get('price');
        $price_discounted = $request->request->get('price_discounted');
        $price_discounted_expires = $request->request->get('price_discounted_expires');
        $isPublic = $request->request->get('isPublic');
        $videos = $request->request->get('videos');

        $allowed_file_types = [
            'image/png',
            'image/jpeg',
            'image/gif',
            'application/octet-stream'
        ];

        $errors = [];

        // Validation
        if (!isset($title) || strlen($title) == 0) {
            $errors['title'] = 'Neivestas pavadinimas';
        } else if (strlen($title) < 3) {
            $errors['title'] = 'Pavadinimas negali būti trumpesnis negu 3 simboliai';
        }

        if (!isset($description) || strlen($description) == 0) {
            $errors['description'] = 'Neivestas aprašymas';
        } else if (strlen($description) < 3) {
            $errors['description'] = 'Aprašymas negali būti trumpesnis negu 3 simboliai';
        }

        if (!isset($cover_image)) {
            $errors['cover_image'] = 'Nepasirinktas cover paveiksliukas';
        } else if (!in_array($cover_image->getClientMimeType(), $allowed_file_types)) {
            $errors['cover_image'] = 'Paveikslėlis turi būti PNG/JPEG/GIF formato';
        }

        if (!isset($price) || strlen($price) == 0) {
            $errors['price'] = 'Neivesta kaina';
        } else if (doubleval($price) == 0) {
            $errors['price'] = 'Kaina turi būti iš skaičių';
        }

        if ((!isset($price_discounted) || strlen($price_discounted) == 0) && (isset($price_discounted_expires) && strlen($price_discounted_expires) > 0)) {
            $errors['price_discounted'] = 'Neivesta nuolaidos kaina';
        } else if ((isset($price_discounted) && strlen($price_discounted) > 0) && (!isset($price_discounted_expires) || strlen($price_discounted_expires) == 0)) {
            $errors['price_discounted_expires'] = 'Neivesta nuolaidos galiojimo data';
        }

        if (count($errors) > 0) {
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $user = $request->getSession()->get('user');

        $file_destination = __DIR__ . '/../../../../public/images/courses/';
        $file_name = date('Y-m-d_H-i-s') . '.' . pathinfo($cover_image->getClientOriginalName(), PATHINFO_EXTENSION);
        $upload_result = move_uploaded_file($cover_image->getPathName(), $file_destination . $file_name);

        if ($upload_result != true) {
            return redirect()->back()->withErrors(['error' => 'Nepavyko įkelti paveikslėlio']);
        }

        $result = Course::insert([
            'owner_id' => $user->id,
            'name' => $title,
            'description' => $description,
            'cover_image' => $file_name,
            'price' => doubleval($price),
            'price_discount' => doubleval($price_discounted) != 0 ? doubleval($price_discounted) : null,
            'price_discount_expires' => strlen($price_discounted_expires) > 0 ? date('Y-m-d H:i:s', strtotime($price_discounted_expires)) : null,
            'public' => isset($isPublic) ? 1 : 0
        ]);

        return redirect()->route('admin.courses.show', ['course' => $result])->with(['success' => 'Kursas sukurtas']);
    }

    public function delete(Request $request, $id)
    {
        return "Administration Courses Delete - $id";
    }
}
