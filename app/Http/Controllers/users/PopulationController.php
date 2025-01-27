<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\Population;
use App\Models\Log;
use Carbon\Carbon;

class PopulationController extends Controller
{
    public function index()
    {
        return view('content.admin.population.population');
    }
    
    public function fetchPopulation(Request $request)
    {
        // Set the default page size (number of records per page)
        $pageSize = $request->input('pageSize', 10); 
    
        // Get the date input from the request (the date from date picker)
        $selectedDate = $request->input('getDate'); 
    
        // Fetch population records for the patient
        $query = Population::select(
            'populations.id', 
            'populations.total_population',
            'populations.created_at'
        )
        ->orderBy('populations.created_at', 'asc');
    
        if ($selectedDate) {
            $query->whereDate('populations.created_at', $selectedDate);
        }
    
        // Apply pagination to the query
        $populationRecords = $query->paginate($pageSize);
    
        // Process populations data
        foreach ($populationRecords as $populationRecord) {
            // Format the created_at date
            $populationRecord->formatted_date = Carbon::parse($populationRecord->created_at)->format('F j, Y');
            // Encrypt the ID
            $populationRecord->encrypted_id = Crypt::encrypt($populationRecord->id);
        }
    
        // Return the paginated result as JSON response
        return response()->json([
            'populationRecords' => $populationRecords
        ]);
    }
    

    public function storePopulation(Request $request)
    {
        $validateData = $request->validate([
            'population' => 'required|integer'
        ]);    

        try
        {
            $populations = [
                'total_population' => $validateData['population'],
            ];

            $total_population = Population::create($populations);

            $role = Auth::user()->role;

            Log::create([
                'role' => $role,
                'action' => 'Added',
                'description' => "You Added total population: {$total_population->total_population}.",
            ]);

            return response()->json(['status' => 200, 'message' => 'Population created successfully']);
        }
        catch (Exception $e)
        {
            return response()->json(['status' => 500, 'message' => 'Failed to create population.']);
        }
    }
}