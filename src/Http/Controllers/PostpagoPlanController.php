<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\TypeDocuments;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use EmizorIpx\PrepagoBags\Http\Requests\StorePostpagoPlanRequest;
use EmizorIpx\PrepagoBags\Http\Resources\PostpagoPlanResource;
use EmizorIpx\PrepagoBags\Models\PostpagoPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PostpagoPlanController extends Controller
{
    use MakesHash;

    public function index( Request $request ){


        $search = request('search');

        $postpago_plans = PostpagoPlan::when($search, function ($query, $search) {
                            return $query->where('name','like', "%".$search."%");
                        })
                        ->paginate(30);
        $document_sectors = TypeDocumentSector::ARRAY_NAMES;

        return view('prepagobags::ListPlanes', compact('postpago_plans', 'search','document_sectors'));

    }

    public function store(StorePostpagoPlanRequest $request){

        $data = $request->all();

        $data['all_sector_docs'] = isset($data['all_sector_docs']) ? isset($data['all_sector_docs']) : false;
        $data['enable_overflow'] = isset($data['enable_overflow']) ? isset($data['enable_overflow']) : false;


        try {
            
            $plan = PostpagoPlan::create($data);

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }

    }

    public function formCreate(){

        return view('prepagobags::components.createFormModal',['document_sectors' => TypeDocumentSector::ARRAY_NAMES]);
    }

    public function formEdit($plan_id){
        $data = PostpagoPlan::where('id', $plan_id)->first();
        \Log::debug($data->id);

        return view('prepagobags::components.editFormModal',["plan" => $data, 'document_sectors' => TypeDocumentSector::ARRAY_NAMES]);
    }

    public function update( StorePostpagoPlanRequest $request, $id ){

        $data = $request->all();

        $data['all_sector_docs'] = isset($data['all_sector_docs']) ? isset($data['all_sector_docs']) : false;
        $data['enable_overflow'] = isset($data['enable_overflow']) ? isset($data['enable_overflow']) : false;

        try{
            $plan = PostpagoPlan::whereId($id)->first();

            if(!$plan){
                throw new Exception('Plan No encontrado');
            }

            $plan->update($data);

            return response()->json([
                'success' => true,
                'data' => new PostpagoPlanResource($plan)
            ]);
        } catch(Exception $ex){
            \Log::debug($ex->getMessage());
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }

    }


    public function show(Request $request, $id ){

        // $idPlanDecode = $this->decodePrimaryKey($id);

        $postpago_plans = PostpagoPlan::find($id);

        return new PostpagoPlanResource($postpago_plans);

    }

    public function publishPlan($id){
        \Log::debug("Publish Plan ");
        \Log::debug($id);
        $plan = PostpagoPlan::whereId($id)->first();

        try{
            if(!$plan){
                throw new Exception('Plan no encontrado');
            }
            $plan->update([
                'public' => $plan->public == true ? false : true
            ]);

            return response()->json([
                'success' => true,
                'data' => new PostpagoPlanResource($plan)
            ]);
        } catch(Exception $ex){

            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);

        }


    }

    public function getPostpagoPlans( Request $request ){

        try{
            $postpago_plans = PostpagoPlan::pluck('name', 'id');
    
            return response()->json([
                'success' => true,
                'data' => $postpago_plans
            ]);
        } catch( Exception $ex ){
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
            
        }


    }


}
