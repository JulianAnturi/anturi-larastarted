<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de Mi Paquete Laravel
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir valores configurables para tu paquete.
    | Los usuarios pueden sobrescribir estas configuraciones publicando el archivo.
    |
    */

    'default_model_namespace' => 'App\\Models\\', // Namespace de los modelos generados

    'default_controller_namespace' => 'App\\Http\\Controllers\\', // Namespace de los controladores generados

    'enable_logging' => true, // Activar o desactivar logs de errores

    'api_routes_prefix' => 'api', // Prefijo de las rutas API

    'default_response_format' => 'json', // Formato de respuesta por defecto

    'default_pagination' => 15, // Número de elementos por página en paginaciones

    'log_table' => 'logs', // Tabla donde se almacenarán los logs generados por el paquete

    'custom_middleware' => [], // Middleware personalizado para las rutas generadas

    'default_validation_rules' => [
        'string' => 'required|string|max:255',
        'integer' => 'required|integer',
        'boolean' => 'required|boolean',
    ],

];

