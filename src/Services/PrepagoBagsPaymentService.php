<?php

namespace EmizorIpx\PrepagoBags\Services;

use App\Events\Invoice\InvoiceWasCreated;
use App\Factory\InvoiceFactory;
use App\Factory\ProductFactory;
use App\Models\Company;
use App\Models\Product;
use App\Repositories\InvoiceRepository;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Repository\FelInvoiceRequestRepository;
use EmizorIpx\ClientFel\Repository\FelProductRepository;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPayment;
use Exception;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class PrepagoBagsPaymentService
{


    protected $prepago_payment;

    public function __construct(PrepagoBagsPayment $prepago_payment)
    {
        $this->prepago_payment = $prepago_payment;
    }

    public function generateInvoiceToPurchase()
    {

        try {

            
            $company = Company::where('id', config('prepagobag.company_admin_id'))->first();
            
            $company_client = Company::where('id', $this->prepago_payment->company_id)->first(); //Company_client
            
            $bag = PrepagoBag::find($this->prepago_payment->prepago_bag_id);
            // $bag = PrepagoBag::find($bag_id);
            
            $product = Product::where('product_key', 'bag-purchase')->first();
            
            if (!$product) {
                $product_repo = new ProductRepository();
                $fel_product_repo = new FelProductRepository();
                
                $product_data = [
                    'product_key' => 'bag-purchase',
                    'notes' => $bag->number_invoices . ' x ' . TypeDocumentSector::getName($bag->sector_document_type_code),
                    'price' => $bag->amount,
                    'quantity' => 1
                ];
                
                $felData = [
                    'codigo_producto_sin' => config('prepagobag.codigo_producto_sin'),
                    'codigo_actividad_economica' => config('prepagobag.codigo_actividad_economica'),
                    'codigo_unidad' => config('prepagobag.codigo_unidad'),
                    'nombre_unidad' => config('prepagobag.nombre_unidad'),
                ];
                
                $product = $product_repo->save($product_data, ProductFactory::create($company->id, $company->owner()->id));
                
                $fel_product_repo->create($felData, $product);
            }
            
            
            $client_id = $company_client->company_detail->client_id;
            
            if (!$client_id) {
                // TODO: crear cliente
            }
            $hashids = new Hashids(config('ninja.hash_salt'), 10);
            
            $invoice_data = [
                'client_id' => $client_id,
                'discount' => 0,
                'date' => Carbon::now()->toDateTimeString(),
                'is_amount_discount' => true,
                'discount' => 0,
                'codigoEstado' => 0,
                'entity_type' => 'invoice',
                'auto_bill_enabled' => false,
                'line_items' => [
                    [
                        'product_key' => $product->product_key,
                        'notes' => $bag->number_invoices . ' x ' . TypeDocumentSector::getName($bag->sector_document_type_code),
                        'cost' => $bag->amount,
                        'quantity' => 1,
                        'discount' => 0,
                        'type_id' => "1",
                        'product_id' => $hashids->encode($product->id),
                        ]
                        ]
                    ];
                    
                    $fel_data = [
                        'codigoMoneda' => "1",
                'codigoPuntoVenta' => config('prepagobag.codigo_pos'),
                'tipoCambio' => 1,
                'codigoActividad' => $product->fel_product->codigo_actividad_economica,
                'codigoLeyenda' => config('prepagobag.codigo_leyenda'),
                'codigoMetodoPago' => config('prepagobag.codigo_metodo_pago'),
                'sector_document_type_id' => config('prepagobag.tipo_documento_sector'),
                'codigo_sucursal' => config('prepagobag.codigo_sucursal'),
                'codigo_pos' => config('prepagobag.codigo_pos'),
            ];
            
            $invoice_repo = new InvoiceRepository();
            $fel_invoice_repo = new FelInvoiceRequestRepository();
            
            $invoice = $invoice_repo->save($invoice_data, InvoiceFactory::create($company->id, $company->owner()->id));
            
            $fel_invoice_repo->create($fel_data, $invoice);
            
            $invoice->service()->emit();
            
            // event(new InvoiceWasCreated($invoice, $invoice->company, $company->owner()->id));
            $invoice->service()->sendEmail();
            $invoice->service()->markPaid();

            bitacora_info("AccountPrepagoBagService:GenerateInvoice", " Factura generada satisfactoriamente ");

        } catch(ClientFelException $ex){

            bitacora_error("AccountPrepagoBagService:GenerateInvoice", " Error:  ". $ex->getMessage());
            \Log::debug("Error client fel ". $ex->getMessage());

        }catch (Exception $ex) {
            bitacora_error("AccountPrepagoBagService:GenerateInvoice", " Error:  ". $ex->getMessage());

            \Log::debug("Error exception ". $ex->getMessage());
        }
    }
}
