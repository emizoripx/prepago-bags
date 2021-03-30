<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Utils\Traits\MakesHash;
use EmizorIpx\PaymentQrBcp\Services\BCPService;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPayment;
use Illuminate\Support\Facades\Request;


class PurchasePrepagoBagController extends Controller
{
    use MakesHash;
    protected $bcpService;
    public function __construct(BCPService $bcpService)
    {
        $this->bcpService = $bcpService;
    }

    public function get(Request $request, $prepago_bag_id)
    {

        $companyId = auth()->user()->company()->id;

        $idBagDecode = $this->decodePrimaryKey($prepago_bag_id);

        $prepagoBag = PrepagoBag::find($idBagDecode);
        
        // create always a register
        $generatedId = PrepagoBagsPayment::generateId($companyId,$idBagDecode,$prepagoBag->amount);

        $bag_prepago_name = $prepagoBag->name . " " . $prepagoBag->number_invoices . " facturas";

        $response = $this->bcpService->generate_qr($generatedId, 'BOB', $prepagoBag->amount, $bag_prepago_name );

        if ($response->state != '00') {
            return response()->json([
                'status' => 'Failed',
                'Msg' => $response->message,
            ]);
        }
        return response()->json([
            'status' => 'Success',
            'qr' => $response->data->qrImage,
            'expiration' => $response->data->expirationDate,
        ]);

    }

}
