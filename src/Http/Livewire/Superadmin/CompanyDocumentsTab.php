<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPayment;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use Livewire\Component;
use DB;

class CompanyDocumentsTab extends Component
{
    public $company_id;
    public $search = '';

    protected $arrayNames = [
        1 => "Factura compra venta", 2 => "Recibo de Alquiler de Bienes Inmuebles", 3 => "Factura comercial de exportación",
        4 => "Factura Comercial de Exportación en Libre Consignación", 5 => "Factura de Zona Franca", 6 => "Factura de Servicio Turístico y Hospedaje",
        7 => "Factura de Comercialización de Alimentos – Seguridad ", 8 => "Factura de tasa cero por venta de libros y transporte internacional de carga",
        9 => "Factura de Compra y Venta de Moneda Extranjera ", 10 => "Factura Dutty Free", 11 => "Factura sectores educativos",
        12 => "Factura de Comercialización de Hidrocarburos", 13 => "Servicios básicos", 14 => "Factura Productos Alcanzados por el ICE",
        15 => "Factura Entidades Financieras", 16 => "Factura de hoteles", 17 => "Factura de Hospitales/Clínicas", 18 => "Factura de Juegos de Azar",
        19 => "Factura Hidrocarburos", 20 => "Factura de exportación de minerales", 21 => "Factura de venta interna de minerales",
        22 => "Factura telecomunicaciones", 23 => "Factura Prevalorada", 24 => "Nota débito crédito", 25 => "Factura de Productos Nacionales",
        26 => "Factura de Productos Nacionales - ICE", 27 => "Factura Regimen 7RG", 28 => "Factura Comercial de Exportación de Servicios",
        29 => "Factura Comercial de Exportación de Servicios", 34 => "Factura compra venta Bonificaciones", 35 => "Factura compra venta Bonificaciones",
        38 => "Factura compra venta Bonificaciones", 46 => "Factura compra venta Bonificaciones", 51 => "Factura compra venta Bonificaciones",
    ];

    public function mount($company_id)
    {
        $this->company_id = $company_id;
    }

    public function syncDocumentSectors()
    {
        try {
            $company = \App\Models\Company::find($this->company_id);
            if (!$company) {
                session()->flash('sync_error', "No se encontró la empresa.");
                return;
            }

            $companyPrepago = AccountPrepagoBags::where('company_id', $this->company_id)->first();
            if (!$companyPrepago) {
                session()->flash('sync_error', "No se encontró configuración de facturación para esta empresa.");
                return;
            }

            $isPartner = !empty($company->settings->is_b2b2b_partner);
            $phase = ($companyPrepago->phase === 'Production') ? 'production' : 'testing';

            if ($isPartner) {
                $partnerConfig = PartnerConfiguration::getConfig($phase);
                $host = rtrim($partnerConfig['api_url'] ?? '', '/');
                $token = $partnerConfig['partner_token'] ?? '';
                $tenantKey = $company->settings->tenant_id ?? ($company->settings->id_number ?? $company->id);

                if (empty($token)) {
                    session()->flash('sync_error', "El Partner Token para ambiente '$phase' no está configurado.");
                    return;
                }

                $parametricService = new Parametric($token, $host, $tenantKey);
            } else {
                if (!$companyPrepago->fel_company_token) {
                    session()->flash('sync_error', "No se encontraron credenciales FEL locales para esta empresa.");
                    return;
                }

                $host = $companyPrepago->fel_company_token->getHost();
                $token = $companyPrepago->fel_company_token->getAccessToken();
                $parametricService = new Parametric($token, $host);
            }

            $parametricService->get(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, '', true);
            $response = $parametricService->getResponse();

            if (!empty($response) && is_array($response)) {
                FelParametric::saveParametrics(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, $this->company_id, $response, true);
            }

            $companyPrepago->service()->registerCompanySectorDocuments();
            session()->flash('sync_message', "Documentos sectoriales sincronizados y disponibilidad actualizada.");
        } catch (\Exception $e) {
            \Log::error("Error sincronizando documentos sector en CompanyDocumentsTab: " . $e->getMessage());
            session()->flash('sync_error', "Error al sincronizar documentos sector: " . $e->getMessage());
        }
    }

    public function render()
    {
        $company = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
            ->select('companies.id', 'fel_company.company_id', 'fel_company.client_id', 'fel_company.production', 'fel_company.is_postpago', 'companies.settings')
            ->where('companies.id', $this->company_id)
            ->first();

        $query = FelCompanyDocumentSector::whereCompanyId($this->company_id);

        $document_sectors = $query->get([
            'invoice_number_available',
            'accumulative',
            'duedate',
            'fel_doc_sector_id',
            'counter',
            'postpago_limit',
            'postpago_exceded_limit',
            'postpago_counter'
        ]);

        $prepago_bags_payments = PrepagoBagsPayment::join('prepago_bags', 'prepago_bags.id', '=', 'prepago_bags_payments.prepago_bag_id')
            ->where('prepago_bags_payments.company_id', $this->company_id)
            ->where('prepago_bags_payments.status_code', '!=', -1)
            ->groupBy('sector_document_type_code')
            ->select(DB::raw('sector_document_type_code, max(prepago_bags_payments.paid_on) as purchase_last_date'))
            ->pluck('purchase_last_date', 'sector_document_type_code');

        $assignedTemplates = DB::table('fel_templates')
            ->where('company_id', $this->company_id)
            ->pluck('display_name', 'document_sector_code')
            ->toArray();

        $arrayNames = $this->arrayNames;

        if (!empty($this->search)) {
            $search = strtolower(trim($this->search));
            $document_sectors = $document_sectors->filter(function ($item) use ($search, $arrayNames) {
                $code = (string) $item->fel_doc_sector_id;
                $name = strtolower($arrayNames[$item->fel_doc_sector_id] ?? '');
                return str_contains($code, $search) || str_contains($name, $search);
            });
        }

        $totalAvailable = $document_sectors->sum('invoice_number_available');
        $totalEmitted = $company->is_postpago ? $document_sectors->sum('postpago_counter') : $document_sectors->sum('counter');

        return view('prepagobags::components.superadmin.company-documents-tab', compact(
            'company',
            'document_sectors',
            'arrayNames',
            'prepago_bags_payments',
            'assignedTemplates',
            'totalAvailable',
            'totalEmitted'
        ));
    }
}
