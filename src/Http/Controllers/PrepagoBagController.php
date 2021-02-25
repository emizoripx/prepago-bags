<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use EmizorIpx\PrepagoBags\Http\Requests\StorePrepagoBagResquest as RequestsStorePrepagoBagResquest;
use EmizorIpx\PrepagoBags\Http\Resources\PrepagoBagResource;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use Exception;
use Illuminate\Support\Facades\Log;

class PrepagoBagController extends Controller
{
    public function store(RequestsStorePrepagoBagResquest $request){

        $data = $request->all();

        Log::debug($data);

        try {
            $prepagoBag = PrepagoBag::create($data);

            return response()->json([
                'success' => true,
                'data' => $prepagoBag
            ]);


        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function index (){
        $prepagoBags = PrepagoBag::all();

        return response()->json([
            "data" => PrepagoBagResource::collection($prepagoBags)
        ]);
    }

    public function update(RequestsStorePrepagoBagResquest $request, $id_bag){

        $data = $request->all();

        try {
            $prepagoBag = PrepagoBag::where('id', $id_bag)->first();

            if(!$prepagoBag){
                return response()->json([
                    "success" => false,
                    "msg" => "Prepago Bag not found"
                ]);
            }

            $prepagoBag->update($data);

            return response()->json([
                "success" =>true
            ]);

        } catch (Exception $ex) {
            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage()
            ]);
        }

    }

    public function delete($id_bag){

        try {
            $prepagoBag = PrepagoBag::find($id_bag);

            if(!$prepagoBag){
                return response()->json([
                    "success" => false,
                    "msg" => "Prepago Bag not found"
                ]);
            }

            $prepagoBag->delete();

            return response()->json([
                "success" =>true
            ]);

        } catch (Exception $ex) {
            return response()->json([
                "success" => false,
                "msg" => $ex->getMessage()
            ]);
        }

    }

    public function show($id_bag){
        $prepagoBag = PrepagoBag::find($id_bag);

        if(!$prepagoBag){
            return response()->json([
                "success" => false,
                "msg" => "Prepago Bag not found"
            ]);
        }

        return response()->json([
            "data" => new PrepagoBagResource($prepagoBag)
        ]);
    }
}
