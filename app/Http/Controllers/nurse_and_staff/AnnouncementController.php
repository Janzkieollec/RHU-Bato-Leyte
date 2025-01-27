<?php

namespace App\Http\Controllers\nurse_and_staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('content.nurse_and_staff.announcement.announcement');
    }

     // Fetch Announcement
     public function fetchAnnouncement(Request $request)
     {
        $pageSize = $request->input('pageSize', 10); // Default to 10 if not provided
        $searchTitle = $request->input('searchTitle'); // get the searchFamilyNumber in data fetchPatients
    
         // Fetch Family Planning with their related data
        $query = Announcement::select(
            'announcements.title',
            'announcements.content',
            'announcements.location',
            'announcements.date',
        )->orderBy('announcements.date', 'asc');;
       
        // If search parameter is provided, add a where condition
        if ($searchTitle) {
            $query->where('announcements.title', 'like', '%' . $searchTitle . '%');
        }
    
        // Apply pagination after the search filter
        $announcement = $query->paginate($pageSize);
 
         // Process patient data
         foreach ($announcement as $announcements) {
             $announcements->encrypted_id = Crypt::encrypt($announcements->id);
             $announcements->date = Carbon::parse($announcements->date)->format('F j, Y'); 
         }
 
         // Return JSON response
         return response()->json([
             'announcement' => $announcement,
         ]);
     }

    public function addAnnouncement(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'location' => 'string',
            'date' => 'required|date',
        ]);
        

        // Save the announcement in the database
        $announcement = [
            'title' => $validateData['title'],
            'content' => $validateData['content'],
            'location' => $validateData['location'],
            'date' => $validateData['date'],
        ];
        
        $announcement = Announcement::create($announcement);

        $role = Auth::user()->role;

        Log::create([
            'role' => $role,
            'action' => 'Added',
            'description' => "You added announcement {$announcement->title}.",
        ]);
    
        return response()->json(['status' => 200, 'message' => 'Announcement created successfully']);
    }
}