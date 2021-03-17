<?php

namespace EmizorIpx\PrepagoBags\Services;

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


}