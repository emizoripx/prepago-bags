<?php

namespace EmizorIpx\PrepagoBags\Services;

use App\Models\Company;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use Exception;
use Illuminate\Support\Facades\DB;

class PurgeCompanyDataService {

    protected $company_id;

    public function setCompanyId($company_id){
        $this->company_id = $company_id;
    }


    public function purgeInvoices(){
        try {
            $affected = DB::delete('delete from fel_invoice_requests where company_id = ?', [$this->company_id]);
            \Log::debug($affected.' Registros Afectados: Facturas');
            return $this;
        } catch (Exception $ex) {
            throw new PrepagoBagsException('Error al purgar los datos de facturas. '.$ex->getMessage());
        }
    }

    public function purgeClients(){
        try {
            $affected = DB::delete('delete from fel_clients where company_id = ?', [$this->company_id]);
            \Log::debug($affected.' Registros Afectados: Clientes');
            return $this;
        } catch (Exception $ex) {
            throw new PrepagoBagsException('Error al purgar los datos de clientes. '.$ex->getMessage());
        }
    }


    public function purgeSinProducts(){
        try {
            $affected = DB::delete('delete from fel_sin_products where company_id = ?', [$this->company_id]);
            \Log::debug($affected.' Registros Afectados: SIN productos');
            return $this;
        } catch (Exception $ex) {
            throw new PrepagoBagsException('Error al purgar los datos de SIN Productos. '.$ex->getMessage());
        }
    }
    public function purgeSyncProducts(){
        try {
            $affected = DB::delete('delete from fel_sync_products where company_id = ?', [$this->company_id]);
            \Log::debug($affected.' Registros Afectados: Sync Productos');
            return $this;
        } catch (Exception $ex) {
            throw new PrepagoBagsException('Error al purgar los datos de Productos. '.$ex->getMessage());
        }
    }
    public function purgeActivities(){
        try {
            $affected = DB::delete('delete from fel_activities where company_id = ?', [$this->company_id]);
            \Log::debug($affected.' Registros Afectados: actividades');
            return $this;
        } catch (Exception $ex) {
            throw new PrepagoBagsException('Error al purgar los datos de Actividades. '.$ex->getMessage());
        }
    }
    public function purgeCaptions(){
        try {
            $affected = DB::delete('delete from fel_captions where company_id = ?', [$this->company_id]);
            \Log::debug($affected.' Registros Afectados: Leyendas');
            return $this;
        } catch (Exception $ex) {
            throw new PrepagoBagsException('Error al purgar los datos de Leyendas. '.$ex->getMessage());
        }
    }
    public function purgeSectorDocuments(){
        try {
            $affected = DB::delete('delete from fel_sector_document_types where company_id = ?', [$this->company_id]);
            \Log::debug($affected.' Registros Afectados: Tipos Documento Sector');
            return $this;
        } catch (Exception $ex) {
            throw new PrepagoBagsException('Error al purgar los datos de Tipos de Documento Sector. '.$ex->getMessage());
        }
    }

    public function purgeBranches(){
        try{
            $affected = DB::delete('delete from fel_branches where company_id = ?', [$this->company_id]);

            \Log::debug($affected.' Registros Afectados: Sucursales');

            return $this;
        }catch(Exception $ex){
            throw new PrepagoBagsException('Error al purgar los datos de sucursal');
        }
    }

    public function purgePOS(){
        try{
            $affected = DB::delete('delete from fel_pos where company_id = ?', [$this->company_id]);

            \Log::debug($affected.' Registros Afectados: POS');

            return $this;
        }catch(Exception $ex){
            throw new PrepagoBagsException('Error al purgar los datos de POS');
        }
    }
    public function purgeCompanyDocumentSector(){
        try{
            $affected = DB::delete('delete from fel_company_document_sectors where company_id = ?', [$this->company_id]);

            \Log::debug($affected.' Registros Afectados: CompanyDocumentSectors');

            return $this;
        }catch(Exception $ex){
            throw new PrepagoBagsException('Error al purgar los datos de CompanyDocumentSectors');
        }
    }
    public function resetCompanyDocumentSector(){
        try{
            $affected = DB::table('fel_company_document_sectors')->where('company_id', $this->company_id)->update([
                'invoice_number_available' => 0
            ]);

            \Log::debug($affected.' Registros Afectados: CompanyDocumentSectors');

            return $this;
        }catch(Exception $ex){
            throw new PrepagoBagsException('Error al purgar los datos de CompanyDocumentSectors');
        }
    }

    public function purgeActivityDocumentSector(){
        try{
            $affected = DB::delete('delete from fel_activity_document_sector where company_id = ?', [$this->company_id]);

            \Log::debug($affected.' Registros Afectados: ActivityDocumentSectors');

            return $this;
        }catch(Exception $ex){
            throw new PrepagoBagsException('Error al purgar los datos de ActivityDocumentSectors');
        }
    }

    public function resetNumbersCounter(Company $company){
        $settings = $company->settings;
        // $settings->reset_counter_date = $reset_date->format('Y-m-d');
        $settings->invoice_number_counter = 1;
        $settings->quote_number_counter = 1;
        $settings->credit_number_counter = 1;
        $settings->vendor_number_counter = 1;
        $settings->ticket_number_counter = 1;
        $settings->payment_number_counter = 1;
        $settings->project_number_counter = 1;
        $settings->task_number_counter = 1;
        $settings->expense_number_counter = 1;
        $settings->client_number_counter = 1;

        $company->settings = $settings;
        $company->save();

        \Log::debug("Counters Reset  Successfully");
        \Log::debug("Invoice #". $settings->invoice_number_counter);
    }


}