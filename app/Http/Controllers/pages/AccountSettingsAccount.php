<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\UserProfile;
use App\Models\User;
use DB;

class AccountSettingsAccount extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get the user's profile data
        $userProfile = DB::table('user_profiles')
            ->select(
                'user_profiles.region_id',
                'user_profiles.province_id',
                'user_profiles.municipality_id',
                'refregion.regDesc as region_name',
                'refprovince.provDesc as province_name',
                'refcitymun.citymunDesc as municipality_name'
            )
            ->leftJoin('refregion', 'user_profiles.region_id', '=', 'refregion.regCode')
            ->leftJoin('refprovince', 'user_profiles.province_id', '=', 'refprovince.provCode')
            ->leftJoin('refcitymun', 'user_profiles.municipality_id', '=', 'refcitymun.citymunCode')
            ->where('user_profiles.user_id', $user->id)
            ->first();
    
        // Add a null check for $userProfile
        if (!$userProfile) {
            $userProfile = (object) [
                'region_id' => null,
                'province_id' => null,
                'municipality_id' => null,
            ];
        }

        // Fetch all regions, provinces, and municipalities for dropdown population
        $regions = DB::table('refregion')->get();
        $provinces = DB::table('refprovince')->get();
        $municipalities = DB::table('refcitymun')->get();
    
        // Set diagnosis type based on role
        if ($user->role === 'Admin') {
            return view('content.pages.admin.admin-profile', [
                'user' => $user,
                'userProfile' => $userProfile,
                'regions' => $regions,
                'provinces' => $provinces,
                'municipalities' => $municipalities
            ]);
        } else {
            return view('content.pages.profile', [
                'user' => $user,
                'userProfile' => $userProfile
            ]);
        }
    }
    
    

  public function updateAccount(Request $request)
  {
      $request->validate([
          'username' => 'required|string|max:255',
          'email' => 'required|email|max:255',
          'status' => 'required|in:Active,Inactive',
      ]);
  
      $user = Auth::user();
      $user->update([
          'username' => $request->username,
          'email' => $request->email,
          'status' => $request->status,
      ]);
  
      // Redirect with SweetAlert success message and session data for active tab
      return redirect()->back()->with([
          'swal' => [
              'title' => 'Success!',
              'text' => 'Profile updated successfully.',
              'icon' => 'success',
          ],
          'activeTab' => 'myAccount',  // Track the active tab
      ]);
  }
  public function changePassword(Request $request)
  {
      $request->validate([
          'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
          'confirm_password' => ['required', 'same:password'],  // Ensure passwords match
      ], [
          'password.required' => 'The password field is required.',
          'password.min' => 'The password must be at least :min characters.',
          'password.regex' => 'Password should contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
          'confirm_password.required' => 'The confirm password field is required.',
          'confirm_password.same' => 'The confirm password must match the new password.',
      ]);
  
      $user = Auth::user();
      $user->update([
          'password' => bcrypt($request->password), // Hash the password before saving
      ]);
  
      // Redirect with SweetAlert success message and session data for active tab
      return redirect()->back()->with([
          'swal' => [
              'title' => 'Success!',
              'text' => 'Password updated successfully.',
              'icon' => 'success',
          ],
          'activeTab' => 'changePassword',  // Track the active tab
      ]);
  }
  


  public function updateSetting(Request $request)
  {
      $request->validate([
          'region' => 'nullable',
          'province' => 'nullable',
          'municipality' => 'nullable',
      ]);
  
      $user = Auth::user();
  
      // Find or create the user profile, then update it
      $userProfile = $user->profile()->firstOrNew(); // Assuming `profile` is the relationship method on the User model
  
      $userProfile->region_id = $request->region;
      $userProfile->province_id = $request->province;
      $userProfile->municipality_id = $request->municipality;
      
      $userProfile->save();
  
      // Redirect with SweetAlert success message and session data for active tab
      return redirect()->back()->with([
          'swal' => [
              'title' => 'Success!',
              'text' => 'Settings updated successfully.',
              'icon' => 'success',
          ],
          'activeTab' => 'settings', 
          'activeTab' => 'settings', 
      ]);
  }
  
  public function updateProfile(Request $request)
  {
      $request->validate([
          'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
          'municipal_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
          'landing_page_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
      ]);

      $user = Auth::user();

      $profile = $user->profile()->firstOrNew();

      // Path where the images will be saved
      $destinationPath = public_path('assets/img/avatars');

      // Ensure the directory exists
      if (!File::exists($destinationPath)) {
          File::makeDirectory($destinationPath, 0755, true);
      }

      // Save Profile Picture
      if ($request->hasFile('profile_picture')) {
          $profilePicture = $request->file('profile_picture');
          $profilePictureName = 'profile_' . $user->id . '.' . $profilePicture->getClientOriginalExtension();
          $profilePicture->move($destinationPath, $profilePictureName);
          $profile->profile_picture = 'assets/img/avatars/' . $profilePictureName;
      }

      // Save Municipal Logo
      if ($request->hasFile('municipal_logo')) {
          $municipalLogo = $request->file('municipal_logo');
          $municipalLogoName = 'municipal_logo_' . $user->id . '.' . $municipalLogo->getClientOriginalExtension();
          $municipalLogo->move($destinationPath, $municipalLogoName);
          $profile->municipal_logo = 'assets/img/avatars/' . $municipalLogoName;
      }

      // Save Landing Page Picture
      if ($request->hasFile('landing_page_picture')) {
          $landingPagePicture = $request->file('landing_page_picture');
          $landingPagePictureName = 'landing_page_' . $user->id . '.' . $landingPagePicture->getClientOriginalExtension();
          $landingPagePicture->move($destinationPath, $landingPagePictureName);
          $profile->landing_page_picture = 'assets/img/avatars/' . $landingPagePictureName;
      }

      // Save profile data to user_profiles table
      $profile->user_id = $user->id;
      $profile->save();

      // Redirect with SweetAlert success message
      return redirect()->back()->with('swal', [
          'title' => 'Success!',
          'text' => 'Profile updated successfully.',
          'icon' => 'success',
      ]);
  }

  public function updateNurse(Request $request)
  {
      $request->validate([
          'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
          'username' => 'required|string|max:255',
          'email' => 'required|email|max:255',
          'status' => 'required|in:Active,Inactive',
      ]);

      $user = Auth::user();

      
    $user->update([
        'username' => $request->username,
        'email' => $request->email,
        'status' => $request->status,
    ]);

    
      $profile = $user->profile()->firstOrNew();

      // Path where the images will be saved
      $destinationPath = public_path('assets/img/avatars');

      // Ensure the directory exists
      if (!File::exists($destinationPath)) {
          File::makeDirectory($destinationPath, 0755, true);
      }

      // Save Profile Picture
      if ($request->hasFile('profile_picture')) {
          $profilePicture = $request->file('profile_picture');
          $profilePictureName = 'profile_' . $user->id . '.' . $profilePicture->getClientOriginalExtension();
          $profilePicture->move($destinationPath, $profilePictureName);
          $profile->profile_picture = 'assets/img/avatars/' . $profilePictureName;
      }
      
      // Save profile data to user_profiles table
      $profile->user_id = $user->id;
      $profile->save();

      // Redirect with SweetAlert success message
      return redirect()->back()->with('swal', [
          'title' => 'Success!',
          'text' => 'Profile updated successfully.',
          'icon' => 'success',
      ]);
  }
}