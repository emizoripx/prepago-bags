<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\CompanyStatistic;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPayment;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Services\Parametrics\Parametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use Livewire\Component;
use DB;

class CompanyInformationTab extends Component
{
    public $company_id;

    protected $listeners = ['credentialsUpdated' => '$refresh'];

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
            \Log::error("Error sincronizando documentos sector en CompanyInformationTab: " . $e->getMessage());
            session()->flash('sync_error', "Error al sincronizar documentos sector: " . $e->getMessage());
        }
    }

    public function render()
    {
        $company = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
            ->select('companies.id', 'fel_company.company_id', 'fel_company.client_id', 'fel_company.production', 'fel_company.is_postpago', 'companies.settings')
            ->where('companies.id', $this->company_id)
            ->first();

        $fel_token = \EmizorIpx\ClientFel\Models\FelClientToken::where('account_id', $this->company_id)->first();

        $document_sectors = FelCompanyDocumentSector::whereCompanyId($this->company_id)->get(['invoice_number_available', 'accumulative', 'duedate', 'fel_doc_sector_id', 'counter', 'postpago_limit', 'postpago_exceded_limit', 'postpago_counter']);

        $prepago_bags_payments = PrepagoBagsPayment::join('prepago_bags', 'prepago_bags.id', '=', 'prepago_bags_payments.prepago_bag_id')
            ->where('prepago_bags_payments.company_id', $this->company_id)
            ->where('prepago_bags_payments.status_code', '!=', -1)
            ->groupBy('sector_document_type_code')
            ->select(DB::raw('sector_document_type_code, max(prepago_bags_payments.paid_on) as purchase_last_date'))
            ->pluck('purchase_last_date', 'sector_document_type_code');

        $stats = CompanyStatistic::where('company_id', $company->company_id)->first();
        $sum_all_documents = 0;
        $post_pago_plan_companies = [];
        $due_invoices = collect();

        if ($company->is_postpago) {
            $sum_all_documents = collect($document_sectors)->sum("postpago_counter");
            $post_pago_plan_companies = PostpagoPlanCompany::whereCompanyId($this->company_id)->first();
            $due_invoices = DB::table('invoices')->whereClientId($company->client_id)->where('balance', '>', 0)->get(['number', 'date', 'balance']);
        }

        $arrayNames = [
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

        $settings = json_decode($company->settings ?? '{}');
        $custom_reports = $settings->custom_reports ?? [];
        $company_logo = $settings->company_logo ?? null;
        if ($company_logo && !str_starts_with($company_logo, 'http')) {
            $company_logo = url($company_logo);
        }

        return view('prepagobags::components.superadmin.company-information-tab', compact('company', 'fel_token', 'document_sectors', 'arrayNames', 'post_pago_plan_companies', 'stats', 'sum_all_documents', 'due_invoices', 'prepago_bags_payments', 'custom_reports', 'company_logo', 'settings'));
    }
}
