<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecommendationService;

class RecommendationController extends Controller
{
    public function __construct(private RecommendationService $service) {}

    public function index(Request $request)
    {
        return response()->json(
            $this->service->recommend($request->user()->id)
        );
    }
}

