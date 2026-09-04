<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use App\Models\Company;
use EmizorIpx\ClientFel\Models\FelClientToken;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Livewire\Component;
use Carbon\Carbon;
use Exception;

class EditFelCredentialsModal extends Component
{
    public $open = false;
    public $company_id;
    public $company_name = '';
    public $host = '';
    public $client_id = '';
    public $client_secret = '';
    
    // Status info
    public $has_token = false;
    public $token_preview = '';
    public $token_expires_at = null;
    public $is_expired = false;
    
    public $status_message = null;
    public $error_message = null;

    protected $listeners = ['openEditFelCredentialsModal' => 'openModal'];

    protected $rules = [
        'host' => 'required|url',
        'client_id' => 'required|string',
        'client_secret' => 'required|string',
    ];

    public function openModal($companyId)
    {
        $this->resetErrorBag();
        $this->status_message = null;
        $this->error_message = null;
        $this->company_id = $companyId;

        $company = Company::find($companyId);
        $this->company_name = $company ? ($company->settings->name ?? 'Compañía #' . $companyId) : '#' . $companyId;

        $tokenRecord = FelClientToken::where('account_id', $companyId)->first();

        if ($tokenRecord) {
            $this->host = $tokenRecord->host ?: config('clientfel.host_demo', 'https://staging.emizor.com');
            $this->client_id = $tokenRecord->client_id ?: '';
            $this->client_secret = $tokenRecord->client_secret ?: '';
            $this->has_token = !empty($tokenRecord->access_token);
            $this->token_preview = $this->has_token ? (substr($tokenRecord->access_token, 0, 15) . '...' . substr($tokenRecord->access_token, -10)) : '';
            
            $expiryCarbon = $this->resolveExpiryDate($tokenRecord->expires_in, $tokenRecord->updated_at ?? $tokenRecord->created_at);
            $this->token_expires_at = $expiryCarbon ? $expiryCarbon->format('d/m/Y H:i') : null;
            $this->is_expired = $expiryCarbon ? $expiryCarbon->isPast() : false;
        } else {
            $this->host = config('clientfel.host_demo', 'https://staging.emizor.com');
            $this->client_id = '';
            $this->client_secret = '';
            $this->has_token = false;
            $this->token_preview = '';
            $this->token_expires_at = null;
            $this->is_expired = true;
        }

        $this->open = true;
    }

    public function closeModal()
    {
        $this->open = false;
        $this->status_message = null;
        $this->error_message = null;
    }

    protected function resolveExpiryDate($expiresIn, $updatedAt = null)
    {
        if (empty($expiresIn)) {
            return null;
        }

        try {
            if (is_numeric($expiresIn)) {
                $num = (int)$expiresIn;
                if ($num > 1000000000) {
                    return Carbon::createFromTimestamp($num);
                } else {
                    $base = $updatedAt ? Carbon::parse($updatedAt) : Carbon::now();
                    return $base->addSeconds($num);
                }
            }

            return Carbon::parse($expiresIn);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function saveAndAuthenticate()
    {
        $this->validate();
        $this->status_message = null;
        $this->error_message = null;

        try {
            $credentialRepo = new FelCredentialRepository();
            $credentialRepo->setCredentials(trim($this->client_id), trim($this->client_secret))
                ->setCompanyId($this->company_id)
                ->setHost(rtrim(trim($this->host), '/'))
                ->register()
                ->authenticate();

            $this->status_message = "¡Credenciales guardadas y autenticación exitosa con el servicio FEL!";
            
            // Reload token status
            $tokenRecord = FelClientToken::where('account_id', $this->company_id)->first();
            if ($tokenRecord) {
                $this->has_token = !empty($tokenRecord->access_token);
                $this->token_preview = $this->has_token ? (substr($tokenRecord->access_token, 0, 15) . '...' . substr($tokenRecord->access_token, -10)) : '';
                $expiryCarbon = $this->resolveExpiryDate($tokenRecord->expires_in, $tokenRecord->updated_at ?? $tokenRecord->created_at);
                $this->token_expires_at = $expiryCarbon ? $expiryCarbon->format('d/m/Y H:i') : null;
                $this->is_expired = $expiryCarbon ? $expiryCarbon->isPast() : false;
            }

            $this->emit('credentialsUpdated');
        } catch (Exception $e) {
            \Log::error("Error al autenticar credenciales FEL directas: " . $e->getMessage());
            $this->error_message = "Error de autenticación: " . $e->getMessage();
        }
    }

    public function setPresetHost($type)
    {
        if ($type === 'demo') {
            $this->host = config('clientfel.host_demo', 'https://staging.emizor.com');
        } elseif ($type === 'production') {
            $this->host = config('clientfel.host_production', 'https://fel.emizor.com');
        }
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.edit-fel-credentials-modal');
    }
}
