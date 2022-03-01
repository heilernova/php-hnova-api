<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */
namespace HNova\Api\Classes;

class AppInfoClass
{
    private string $name = '';
    public string $namespace = "";
    public bool $disable = false;
    
    /** Directorio donde se alamacenaran los archivos. */
    public string $dirResources = "";

    /** Nombre de la base de datos por default */
    public string $database = "";

    /**
     * Método constructor
     * @param string $name Nombre de la app.
     */
    public function __construct(string $name, $namespace = null, $dir_resources = "", $disable = false, string $database = '', CorsClass $cors = null )
    {
        
    }
}