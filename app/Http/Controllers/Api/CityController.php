<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Get cities by prefecture ID.
     */
    public function index(Request $request)
    {
        $prefectureId = $request->query('prefecture_id');
        
        if (!$prefectureId) {
            return response()->json([]);
        }

        $cities = City::where('prefecture_id', $prefectureId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }
}

