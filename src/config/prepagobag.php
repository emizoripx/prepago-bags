<?php

return [
    'entity_table_account' => \App\Models\Account::class,

    'company_admin_id' => 164,
    
    'codigo_producto_sin' => env('PRODUCT_SIN', '83141'),
    
    'codigo_actividad_economica' => env('ACTIVITY_CODE', '620100'),
    
    'codigo_unidad' => env('UNIT_CODE', '58'),
    
    'nombre_unidad' => env('UNIT_NAME', 'UNIDAD (SERVICIOS)'),
    
    'codigo_leyenda' => env('CAPTION_CODE', '376'),

    'codigo_metodo_pago' => env('PAYMENT_METHOD', '7'),

    'tipo_documento_sector' => env('DOCUMENT_SECTOR_TYPE', '1'),

    'codigo_sucursal' => env('BRANCH_CODE', '0'),

    'codigo_pos' => env('POS_CODE', '0'),
];