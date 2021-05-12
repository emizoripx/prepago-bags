<?php


namespace EmizorIpx\PrepagoBags\Utils;

class ModalityInvoicing{

    const ELECTRONIC_INVOICING = 1;
    const COMPUTARIZED_INVOICING = 2;
    

    public static function getModalityInvoicing( $code ){

        switch ($code) {
            case static::ELECTRONIC_INVOICING:
                return 'Facturación Electrónica en Línea';
                break;
            case static::COMPUTARIZED_INVOICING:
                return 'Facturación Computarizada en Línea.';
                break;
        }

    }

}