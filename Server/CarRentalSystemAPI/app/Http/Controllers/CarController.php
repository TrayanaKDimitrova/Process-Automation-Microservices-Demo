<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cars;

class CarController extends Controller
{
    public function create(Request $request)
    {
        $car = new Cars();
        $car->derivative = $request->input('derivative');
        $car->model = $request->input('model');
        $car->transmission = $request->input('transmission');
        $car->fuel_type = $request->input('fuel_type');
        $car->price = $request->input('price');
        $car->save();

        return $car;
    }
}
