<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!$request->user()->is_admin) {
        //     return to_route('course-validation.index');
        // }

        // return $next($request);

        if ($request->user()->is_admin === 'super_admin') {
            // Admin access
            return $next($request);
        } elseif ($request->user()->is_admin === 'admin') {
            // Content creator access
            return to_route('course-validation.index');
        } elseif ($request->user()->is_admin === 'user') {
            // Regular user access
            // return response('Access Denied', 403);

            $pendingCourseSlug = $request->session()->get('pending_course_slug');

            if ($pendingCourseSlug) {
                // Clear the stored course slug from session
                $request->session()->forget('pending_course_slug');

                // Redirect the user to the intended course share page
                return redirect()->route('courses.share', ['course_slug' => $pendingCourseSlug]);
            } else {
                // Handle the case where there's no pending_course_slug
                // For example, redirect to a default page
                return redirect('/'); // Or any other default page
            }
        }
    }
}
