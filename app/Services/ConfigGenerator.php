<?php

namespace App\Services;

use App\Models\Dominio;
use App\Models\ServerTemplate;

class ConfigGenerator
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getTemplateForDominio(Dominio $dominio)
    {
        $webServer = $dominio->vm->web_server_type; // 'nginx' o 'apache'
        $stackSlug = $dominio->repositorio->stack->slug; // 'laravel', 'react', 'nodejs'

        // Buscamos una plantilla que coincida con ambos
        return ServerTemplate::where('web_server', $webServer)
            ->where('stack_slug', $stackSlug) // Necesitarás esta columna en server_templates
            ->first();
    }
}
