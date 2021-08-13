<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use EmizorIpx\PrepagoBags\Http\Requests\StorePostpagoPlanRequest;
use EmizorIpx\PrepagoBags\Http\Resources\PostpagoPlanResource;
use EmizorIpx\PrepagoBags\Models\PostpagoPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PostpagoPlanController extends Controller
{
    public function index( Request $request ){

        \Log::debug("Get Index");

        $postpago_plans = PostpagoPlan::paginate(30);

        return view('prepagobags::ListPlanes', compact('postpago_plans'));

    }

    public function store(StorePostpagoPlanRequest $request){

        $data = $request->all();

        \Log::debug("Store Postpago Plans");
        \Log::debug($data);

        try {
            
            $plan = PostpagoPlan::create($data);

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'data' => $ex->getMessage()
            ]);
        }

    }
}
