<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    public function getBatoBarangays(): JsonResponse
    {
        // Define the path to your GeoJSON file in the public folder
        $filePath = public_path('geojson/Bato_Brgys.geojson');
        
        // Check if file exists
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Load and return the content of the GeoJSON file
        $geojson = File::get($filePath);

        return response()->json(json_decode($geojson));
    }
}