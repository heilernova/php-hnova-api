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

class CorsClass
{
    /**
     * @var array[]|string|null
     */
    public array|string|null $origin = null;
    
    /**
     * @var array[]|string|null
     */
    public array|string|null $headers = null;
    
    /**
     * @var array[]|string|null
     */
    public array|string|null $methods = null;

    /**
     * Método constructor.
     * @var array[]|string|null $origin     Dominios que tiene acceso a la api
     * @var array[]|string|null $headers    
     * @var array[]|string|null $methods    Methodos que se puede ejecutar.
     */
    public function __construct(array|string|null $orign = null, array|string|null $headers = null, array|string|null $methdos = null)
    {
        $this->origin = $orign;
        $this->headers = $headers;
        $this->methods = $methdos;
    }
}