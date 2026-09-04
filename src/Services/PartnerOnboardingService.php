<?php

namespace EmizorIpx\PrepagoBags\Services;

use App\Models\Company;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelPOS;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PartnerOnboardingService
{
    protected $host;
    protected $token;
    protected $environment;
    protected $defaultTimeout = 15;

    public function __construct($environment = 'production')
    {
        $this->environment = $environment;
        $config = PartnerConfiguration::getConfig($environment);

        $this->host = rtrim($config['api_url'] ?? '', '/');
        $this->token = $config['partner_token'] ?? '';

        if (empty($this->token)) {
            Log::warning("Partner token para $environment no está configurado. La automatización del Partner fallará.");
        }
    }

    public function hasToken()
    {
        return !empty($this->token);
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function registerCompany(Company $company, $settings = [])
    {
        $nit = !empty($settings['nit']) ? (string)$settings['nit'] : (string)($company->settings->id_number ?? '');
        $name = !empty($settings['name']) ? (string)$settings['name'] : ($company->settings->name ?? '');
        $businessName = !empty($settings['business_name']) ? (string)$settings['business_name'] : ($company->settings->name ?? $name);

        Log::info("PartnerOnboardingService: Registrando empresa '$name' con NIT '$nit'");

        $basePayload = [
            'nit' => $nit,
            'name' => $name,
            'business_name' => $businessName,
            'is_uniper' => 0,
            'modality_code' => 1,
            'type_document_sectors_codes' => [1],
            'platform' => 'emizor5',
        ];

        $payload = array_merge($basePayload, $settings);
        $payload['nit'] = (string)($payload['nit'] ?? $nit);
        $payload['name'] = (string)($payload['name'] ?? $name);
        $payload['business_name'] = (string)($payload['business_name'] ?? $businessName);
        $payload['is_uniper'] = !empty($payload['is_uniper']) ? (int)$payload['is_uniper'] : 0;

        // Asegurar que system_code contenga strings si está presente
        if (isset($payload['system_code']) && is_array($payload['system_code'])) {
            $payload['system_code'] = array_values(array_filter(array_map('strval', $payload['system_code'])));
            if (empty($payload['system_code'])) {
                unset($payload['system_code']);
            }
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->timeout($this->defaultTimeout)->post($this->host . '/api/partner/v1/companies', $payload);

        if ($response->failed()) {
            if ($response->status() == 409) {
                Log::info("La empresa ya existe en el Partner: " . $response->body());
                return $response->json(); // Si ya existe y pertenece al mismo Partner
            }
            Log::error("Error al registrar la empresa en el Partner API: " . $response->body());
            throw new Exception("Error al registrar la empresa en la plataforma central: " . $response->body());
        }

        return $response->json();
    }

    public function updateCompanySettings($tenantKey, $settings)
    {
        Log::info("PartnerOnboardingService: Actualizando configuración para Tenant Key " . $tenantKey);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'tenant-key' => $tenantKey,
            'Accept' => 'application/json',
        ])->timeout($this->defaultTimeout)->put($this->host . '/api/partner/v1/companies/settings', $settings);

        if ($response->failed()) {
            Log::error("Error en updateCompanySettings: " . $response->body());
            throw new Exception("Error al actualizar la configuración de la empresa: " . $response->body());
        }

        return $response->json();
    }

    public function uploadCredentials($tenantKey, $certificate = null, $password = null, $sinToken = null, $systemCode = null)
    {
        Log::info("PartnerOnboardingService: Subiendo credenciales para Tenant Key " . $tenantKey);

        $request = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'tenant-key' => $tenantKey,
            'Accept' => 'application/json',
        ]);

        if ($certificate instanceof \Illuminate\Http\UploadedFile || $certificate instanceof \Livewire\TemporaryUploadedFile) {
            $request = $request->attach(
                'certificate', 
                file_get_contents($certificate->getRealPath()), 
                $certificate->getClientOriginalName() ?: 'certificate.p12',
                ['Content-Type' => 'application/x-pkcs12']
            );
        } elseif (is_string($certificate) && !empty($certificate)) {
            $isPath = (strlen($certificate) < 4096 && @file_exists($certificate));
            $filename = $isPath ? basename($certificate) : 'certificate.p12';
            $content = $isPath ? file_get_contents($certificate) : $certificate;
            $request = $request->attach(
                'certificate', 
                $content, 
                $filename,
                ['Content-Type' => 'application/x-pkcs12']
            );
        }

        $payload = [];
        if ($password) $payload['certificate_password'] = $password;
        if ($sinToken) $payload['sin_token'] = $sinToken;
        if ($systemCode) $payload['system_code'] = $systemCode;

        $response = $request->timeout(30)->post($this->host . '/api/partner/v1/companies/credentials', $payload);

        if ($response->failed()) {
            Log::error("Error en uploadCredentials: " . $response->body());
            throw new Exception("Error al subir credenciales: " . $response->body());
        }

        return $response->json();
    }

    public function beginOperations($tenantKey)
    {
        Log::info("PartnerOnboardingService: Begin Operations para Tenant Key " . $tenantKey);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'tenant-key' => $tenantKey,
            'Accept' => 'application/json',
        ])->timeout($this->defaultTimeout)->post($this->host . '/api/partner/v1/companies/begin-operations');

        if ($response->failed()) {
            Log::error("Error en beginOperations: " . $response->body());
            throw new Exception("Error al iniciar operaciones (begin-operations): " . $response->body());
        }

        return $response->json();
    }

    public function syncParametrics($tenantKey, $specificParametrics = null)
    {
        Log::info("PartnerOnboardingService: Sincronizando paramétricas para Tenant Key " . $tenantKey);

        $parametricsToSync = $specificParametrics ?? [
            'SIN_MOTIVO_ANULACION',
            'SIN_DOCUMENTO_SECTOR',
            'SIN_TIPO_DOCUMENTO_IDENTIDAD',
            'SIN_UNIDAD_MEDIDA',
            'SIN_MENSAJE_SERVICIOS',
            'SIN_METODOS_PAGO',
            'SIN_MONEDAS',
            'SIN_PAIS',
            'SIN_LEYENDAS_FACTURAS',
            'SIN_TIPO_HABITACION',
            'SIN_ACTIVIDADES',
            'SIN_PRODUCTOS_SERVICIOS'
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'tenant-key' => $tenantKey,
            'Accept' => 'application/json',
        ])->timeout(30)->post($this->host . '/api/partner/v1/companies/sync-parametrics', [
            'type_parametrics' => $parametricsToSync
        ]);

        if ($response->failed()) {
            Log::error("Error en syncParametrics: " . $response->body());
            throw new Exception("Error al sincronizar paramétricas: " . $response->body());
        }

        return $response->json();
    }

    public function getOnboardingStatus($tenantKey)
    {
        Log::info("PartnerOnboardingService: Obteniendo Onboarding Status para Tenant Key " . $tenantKey);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'tenant-key' => $tenantKey,
            'Accept' => 'application/json',
        ])->timeout($this->defaultTimeout)->get($this->host . '/api/partner/v1/companies/onboarding-status');

        if ($response->failed()) {
            Log::error("Error en getOnboardingStatus: " . $response->body());
            throw new Exception("Error al obtener el status de onboarding: " . $response->body());
        }

        return $response->json();
    }

    public function checkCompanyStatus($tenantKey)
    {
        try {
            if (!$this->hasToken() || empty($tenantKey)) {
                return null;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'tenant-key' => $tenantKey,
                'Accept' => 'application/json',
            ])->timeout(5)->get($this->host . '/api/partner/v1/companies/onboarding-status');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::warning("PartnerOnboardingService checkCompanyStatus: " . $e->getMessage());
            return null;
        }
    }

    public function getCompanyDetails($tenantKey)
    {
        Log::info("PartnerOnboardingService: Obteniendo detalles completos de la compañía para Tenant Key " . $tenantKey);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'tenant-key' => $tenantKey,
            'Accept' => 'application/json',
        ])->timeout($this->defaultTimeout)->get($this->host . '/api/partner/v1/companies/details');

        if ($response->failed()) {
            Log::error("Error en getCompanyDetails: " . $response->body());
            throw new Exception("Error al obtener los detalles de la compañía: " . $response->body());
        }

        return $response->json();
    }

    public function getTemplates($tenantKey)
    {
        Log::info("PartnerOnboardingService: Obteniendo plantillas disponibles para Tenant Key " . $tenantKey);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'tenant-key' => $tenantKey,
            'Accept' => 'application/json',
        ])->timeout($this->defaultTimeout)->get($this->host . '/api/partner/v1/companies/templates');

        if ($response->failed()) {
            Log::error("Error en getTemplates: " . $response->body());
            throw new Exception("Error al obtener las plantillas de la compañía: " . $response->body());
        }

        return $response->json();
    }

    public function syncCompanyDetailsAndBranches($tenantKey, $companyId)
    {
        Log::info("PartnerOnboardingService: Sincronizando detalles, sucursales y POS para Tenant Key $tenantKey, Company ID $companyId");

        try {
            $data = $this->getCompanyDetails($tenantKey);
            $companyData = $data['company'] ?? null;

            if (!$companyData) {
                Log::warning("PartnerOnboardingService: No se encontró información de la compañía en la respuesta.");
                return null;
            }

            // 1. Actualizar datos base en AccountPrepagoBags
            AccountPrepagoBags::where('company_id', $companyId)->update([
                'fel_company_id' => $companyData['id'] ?? null,
                'modality_code' => $companyData['modality_code'] ?? 1,
                'business_name' => $companyData['business_name'] ?? ($companyData['name'] ?? null),
                'is_uniper' => !empty($companyData['is_uniper']) ? 1 : 0,
            ]);

            // 2. Sincronizar Sucursales y Puntos de Venta (POS)
            if (!empty($companyData['branches']) && is_array($companyData['branches'])) {
                foreach ($companyData['branches'] as $branch) {
                    $branchCode = $branch['branch_code'] ?? 0;
                    $branchModel = FelBranch::updateOrCreate(
                        [
                            'company_id' => $companyId,
                            'codigo' => $branchCode,
                        ],
                        [
                            'descripcion' => $branch['name'] ?? ($branchCode == 0 ? 'Casa Matriz' : 'Sucursal ' . $branchCode),
                            'zona' => $branch['zone'] ?? '',
                            'telefono' => $branch['phone'] ?? '',
                            'pais' => 'Bolivia',
                            'ciudad' => $branch['city'] ?? '',
                            'municipio' => $branch['municipio'] ?? '',
                        ]
                    );

                    // Sincronizar Puntos de Venta (POS)
                    if (!empty($branch['pos']) && is_array($branch['pos'])) {
                        foreach ($branch['pos'] as $p) {
                            $posCode = $p['pos_code'] ?? 0;
                            FelPOS::updateOrCreate(
                                [
                                    'company_id' => $companyId,
                                    'branch_id' => $branchModel->id,
                                    'codigo' => $posCode,
                                ],
                                [
                                    'descripcion' => $p['name'] ?? ('Punto de Venta ' . $posCode),
                                ]
                            );
                        }
                    }
                }
            }

            return $companyData;
        } catch (\Exception $e) {
            Log::error("PartnerOnboardingService: Error al sincronizar detalles y sucursales: " . $e->getMessage());
            throw $e;
        }
    }

    public function syncTemplates($tenantKey, $companyId)
    {
        Log::info("PartnerOnboardingService: Sincronizando plantillas para Tenant Key $tenantKey, Company ID $companyId");

        try {
            $data = $this->getTemplates($tenantKey);
            $templates = $data['templates'] ?? [];

            $companyDetails = null;
            try {
                $detailsRes = $this->getCompanyDetails($tenantKey);
                $companyDetails = $detailsRes['company'] ?? null;
            } catch (\Exception $e) {
                Log::warning("PartnerOnboardingService: No se pudo obtener detalle adicional de plantillas por sucursal: " . $e->getMessage());
            }

            $arrayTemplates = [];
            $now = Carbon::now()->toDateTimeString();

            if (!empty($companyDetails['branches']) && is_array($companyDetails['branches'])) {
                foreach ($companyDetails['branches'] as $branch) {
                    $branchCode = $branch['branch_code'] ?? 0;
                    if (!empty($branch['assigned_documents']) && is_array($branch['assigned_documents'])) {
                        foreach ($branch['assigned_documents'] as $doc) {
                            $arrayTemplates[] = [
                                'company_id' => $companyId,
                                'display_name' => $doc['template_name'] ?? ('Plantilla Documento ' . ($doc['document_code'] ?? 1)),
                                'document_sector_code' => $doc['document_code'] ?? 1,
                                'blade_resource' => $doc['blade_resource'] ?? null,
                                'branch_code' => $branchCode,
                                'pos_code' => null,
                                'header_custom' => false,
                                'footer_custom' => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }
                }
            }

            if (empty($arrayTemplates) && !empty($templates)) {
                foreach ($templates as $template) {
                    $arrayTemplates[] = [
                        'company_id' => $companyId,
                        'display_name' => $template['name'] ?? ($template['display_name'] ?? 'Plantilla Estándar'),
                        'document_sector_code' => $template['document_by_sector_type_id'] ?? ($template['document_sector_code'] ?? 1),
                        'blade_resource' => $template['blade_resource'] ?? null,
                        'branch_code' => 0,
                        'pos_code' => null,
                        'header_custom' => false,
                        'footer_custom' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($arrayTemplates)) {
                DB::table('fel_templates')->upsert(
                    $arrayTemplates,
                    ['document_sector_code', 'company_id', 'branch_code', 'pos_code'],
                    ['display_name', 'blade_resource', 'updated_at']
                );
                Log::info("PartnerOnboardingService: Plantillas sincronizadas exitosamente para Company ID $companyId");
            }

            return $arrayTemplates;
        } catch (\Exception $e) {
            Log::error("PartnerOnboardingService: Error al sincronizar plantillas: " . $e->getMessage());
            throw $e;
        }
    }
}

