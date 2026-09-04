<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PartnerWebhookReceiverController extends BaseController
{
    /**
     * Handle incoming webhook callbacks from Emizor Core Partner API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // 1. Verificación de Seguridad Configurable (Desactivada por defecto)
        $strictAuth = filter_var(env('PARTNER_WEBHOOK_STRICT_AUTH', false), FILTER_VALIDATE_BOOLEAN);
        $incomingToken = $request->header('X-Webhook-Secret') ?? $request->bearerToken();

        if ($strictAuth) {
            $configuredSecret = env('PARTNER_WEBHOOK_SECRET');
            $testingToken = PartnerConfiguration::getConfig('testing')['partner_token'] ?? null;
            $productionToken = PartnerConfiguration::getConfig('production')['partner_token'] ?? null;

            $validTokens = array_filter([$configuredSecret, $testingToken, $productionToken]);

            if (empty($incomingToken) || (!empty($validTokens) && !in_array($incomingToken, $validTokens))) {
                Log::warning("PartnerWebhookReceiverController: Intento no autorizado de acceso al webhook.", [
                    'ip' => $request->ip(),
                    'has_token' => !empty($incomingToken),
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'No autorizado: Credenciales de webhook inválidas.',
                ], 401);
            }
        } elseif (!empty($incomingToken)) {
            Log::info("PartnerWebhookReceiverController: Webhook recibido con token de autenticación (Modo estricto inactivo).");
        }

        $payload = $request->all();
        $eventType = $request->input('event_type') ?? $request->input('event') ?? 'unknown';
        $tenantId = $request->input('tenant_id') ?? $request->input('company_key') ?? $request->input('nit');

        Log::info("PartnerWebhookReceiverController: Evento recibido [$eventType]", [
            'tenant_id' => $tenantId,
            'payload' => $payload
        ]);

        if (empty($tenantId)) {
            // Check if tenant is inside data wrapper
            $tenantId = $request->input('data.tenant_id') ?? $request->input('data.company_key') ?? $request->input('data.nit');
        }

        switch ($eventType) {
            case 'update_parametrics':
            case 'parametrics_synced':
                $this->handleParametricsSynced($tenantId, $payload);
                break;

            case 'invoice_status':
            case 'invoice_update':
                $this->handleInvoiceStatus($tenantId, $payload);
                break;

            default:
                Log::info("PartnerWebhookReceiverController: Evento no manejado específicamente [$eventType]");
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook procesado exitosamente',
        ], 200);
    }

    protected function handleParametricsSynced($tenantId, $payload)
    {
        if (empty($tenantId)) {
            Log::warning("PartnerWebhookReceiverController: update_parametrics recibido sin tenant_id");
            return;
        }

        // Búsqueda directa en MySQL 8 usando json_extract sin cargar la tabla en memoria
        $targetCompany = Company::whereRaw("json_extract(settings, '$.tenant_id') = ?", [(string)$tenantId])
            ->orWhereRaw("json_extract(settings, '$.id_number') = ?", [(string)$tenantId])
            ->first();

        if (!$targetCompany) {
            Log::warning("PartnerWebhookReceiverController: No se encontró compañía local para tenant_id: $tenantId");
            return;
        }

        // Normalización segura de settings (sea objeto stdClass o string)
        $settings = is_string($targetCompany->settings) ? json_decode($targetCompany->settings) : (object)($targetCompany->settings ?? []);
        if (!is_object($settings)) {
            $settings = new \stdClass();
        }

        $settings->partner_onboarding_status = 'COMPLETED';
        $settings->partner_parametrics_synced_at = now()->toDateTimeString();
        $targetCompany->settings = $settings;
        $targetCompany->save();

        Log::info("PartnerWebhookReceiverController: Compañía ID {$targetCompany->id} (" . ($settings->name ?? '') . ") actualizada a COMPLETED tras sincronización de paramétricas.");
    }

    protected function handleInvoiceStatus($tenantId, $payload)
    {
        Log::info("PartnerWebhookReceiverController: Actualización de estado de factura para tenant $tenantId");
    }
}
