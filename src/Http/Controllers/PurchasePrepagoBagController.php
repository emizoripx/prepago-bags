<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use App\Http\Controllers\Controller;
use EmizorIpx\PaymentQrBcp\Services\BCPService;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use Illuminate\Support\Facades\Request;


class PurchasePrepagoBagController extends Controller
{

    protected $bcpService;
    public function __construct(BCPService $bcpService)
    {
        $this->bcpService = $bcpService;
    }

    public function get(Request $request, $prepago_bag_id)
    {

        $idBagDecode = $this->decodePrimaryKey($prepago_bag_id);

        $prepagoBag = PrepagoBag::find($idBagDecode);

        $response = $this->bcpService->generate_qr(100, 'BOB', $prepagoBag->amount, 'Compra bolsa prepago');

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

    public function store(Request $request)
    {
        // get notification from payment service done
        // save payment in hitorical_payments
    }
}
