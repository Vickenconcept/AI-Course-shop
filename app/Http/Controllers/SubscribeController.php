<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subscribe;
use App\Models\User;
use App\Services\MailChimp;
use App\Services\MailChimpService;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public $mailchimpService;

    public function __construct(MailChimpService $mailchimpService)
    {

        $this->mailchimpService = $mailchimpService;
    }


    public function index()
    {
        // return view('pages.courses.subscribe');
        return null;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $courseId = $request->input('courseId');
        $email = $request->input('email');
        $list_id = $request->input('list_id');
        $user = User::where('email', $email)->first();
        $courseModel = new Course;

        $course = $courseModel->newQueryWithoutScopes()->find($courseId);
        
        // dd($user);
        if ($user && $course) {
            
            $courseCreator = $course->user;
            
            if ($courseCreator->first()->setting) {
                $response = $this->mailchimpService
                ->subscribe($email, $courseCreator
                ->first()->setting->mailchimp_api_key, $courseCreator
                ->first()->setting->mailchimp_prefix_key, $list_id);
                
                $course->user()->sync([$user->id], false);
                // dd($courseCreator->first()->setting);

                return redirect()->route('courses.share', ['course_slug' => $course->slug]);
            } else {
                $course->user()->attach($user->id);
            }
        } else {
            response('course not found');
        }
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $intent = auth()->user()->createSetupIntent();
        $courseModel = new Course;
        $course = $courseModel->newQueryWithoutScopes()
            ->find($id);
        if (!$course) {
            abort(404);
        }
        $list_id = $course->list_id;

        // dd( $course->list_id);

        return view('pages.courses.subscribe', compact('course' ,'list_id', 'intent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscribe $subscribe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscribe $subscribe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscribe $subscribe)
    {
        //
    }
}