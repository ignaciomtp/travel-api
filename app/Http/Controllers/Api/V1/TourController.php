<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Travel;
use App\Models\Tour;
use App\Http\Resources\TourResource;

class TourController extends Controller
{
    public function index(Travel $travel) {
        $tours = $travel->tours()
            ->orderBy('starting_date')
            ->paginate();

        return TourResource::collection($tours);
    }
}
