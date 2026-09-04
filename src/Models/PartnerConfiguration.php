<?php

namespace EmizorIpx\PrepagoBags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class PartnerConfiguration extends Model
{
    protected $table = "fel_partner_configurations";

    protected $guarded = [];

    /**
     * Get the partner configuration for a specific environment (testing or production).
     * Falls back to .env variables if database record is missing or empty.
     *
     * @param string $environment
     * @return array
     */
    public static function getConfig($environment = "production")
    {
        $normalizedEnv = in_array(strtolower($environment), ["testing", "piloto testing", "piloto"]) ? "testing" : "production";
        
        $record = null;
        try {
            if (Schema::hasTable("fel_partner_configurations")) {
                $record = self::where("environment", $normalizedEnv)->first();
            }
        } catch (\Exception $e) {
            Log::warning("No se pudo consultar fel_partner_configurations: " . $e->getMessage());
        }

        if ($normalizedEnv === "testing") {
            $defaultHost = config("clientfel.api_url", env("API_URL_TESTING", "http://localhost:9080"));
            $defaultToken = env("PARTNER_TOKEN_TESTING", env("EMIZOR_PARTNER_TOKEN", ""));
        } else {
            $defaultHost = config("clientfel.api_url", env("API_URL_PRODUCTION", "http://localhost:9080"));
            $defaultToken = env("PARTNER_TOKEN_PRODUCTION", env("EMIZOR_PARTNER_TOKEN", ""));
        }

        return [
            "environment" => $normalizedEnv,
            "api_url" => (!empty($record) && !empty($record->api_url)) ? $record->api_url : $defaultHost,
            "partner_token" => (!empty($record) && !empty($record->partner_token)) ? $record->partner_token : $defaultToken,
            "webhook_url" => (!empty($record) && !empty($record->webhook_url)) ? $record->webhook_url : null,
        ];
    }

    /**
     * Update or create configuration in the database.
     *
     * @param string $environment
     * @param string $apiUrl
     * @param string|null $partnerToken
     * @param string|null $webhookUrl
     * @return PartnerConfiguration
     */
    public static function updateConfig($environment, $apiUrl, $partnerToken = null, $webhookUrl = null)
    {
        $normalizedEnv = in_array(strtolower($environment), ["testing", "piloto testing", "piloto"]) ? "testing" : "production";

        return self::updateOrCreate(
            ["environment" => $normalizedEnv],
            [
                "api_url" => $apiUrl,
                "partner_token" => $partnerToken,
                "webhook_url" => $webhookUrl,
            ]
        );
    }
}
