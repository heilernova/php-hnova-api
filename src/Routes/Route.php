<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HNova\Api\Routes;

use HNova\Api\ApiException;
use HNova\Api\Response;
use ReflectionFunction;
use ReflectionMethod;

class Route
{
    public string   $id = '';
    
    /**
     * URL de la petición realizada por el cliente.
     */
    private string   $httpRequest = '';
    
    
    /** @var array|callable */
    private         $action;
    
    /** Método http asignado a la ruta */
    private string  $method = '';
    
    /**
     * Array de los canActive 
     * @var callable[]
     * */
    private array    $canActivate = [];
    
    /** Ruta de acceso */
    private string  $path = '';
    
    /** Número de items de ruta */
    private int     $pathCount = 0;
    
    /** Hace referencia los item de la ruta que no son parametros */
    private array   $pathActions = [];
    
    /** Número de actions de la ruta */
    public int      $pathActionsCount = 0;
    
    /**
     * Array que contiene los parametros de la ruta.
     * @var object[]
     */
    private array   $pathParams = [];

    /** Número de parametros de la ruta */
    private int     $pathParamsCount = 0;

    /** array de los items de la ruta */
    private array   $pathItems = [];

    /**
     * @param string                    $path
     * @param string|array|callable     $action
     * @param callable[]                $canActivate
     */
    public function __construct(string $path,string|array|callable $action, string $http_method, array $can_activate = [])
    {
        $path = trim($path, '/');
        $this->path = $path;
        $this->action = $action;
        $this->method = $http_method;
        $this->canActivate = $can_activate;

        // Separamos la keys y los parametros.
        $this->id ="$http_method:";
        $i = 0;
        $this->pathItems = explode('/', $path);
        foreach ($this->pathItems as $item){

            if (str_starts_with($item, '{')){
                $item = explode(':', trim($item, "{\}"));
                $optional = false;
                if (str_ends_with($item[1] ?? '', '?')){
                    $item[1] =  rtrim($item[1], '?');
                    $optional = true;
                }

                $this->pathParams[] = (object)[
                    'index'=>$i,
                    'name'=>$item[0],
                    'type'=>($item[1] ?? 'mixed'),
                    'optional'=>$optional
                ];

                $this->id .= "{p}/";

            }else{
                $this->pathActions[] = $i;
                $this->id .= "$item/";
            }

            $i++;
        }

        $this->id = rtrim($this->id, '/');
        $this->pathActionsCount = count($this->pathActions);
        $this->pathParamsCount = count($this->pathParams);
        $this->pathCount = $this->pathActionsCount + $this->pathParamsCount;
    }

    /**
     * Agrega un canActive a la ruta.
     */
    public function addCanActivate(callable $canActivate):void
    {
        $this->canActivate[] = $canActivate;
    }

    /**
     * Valida si la ruta ingresada concuerda con las ruta.
     * @param string $url URL de la petición realizada
     * @param array $url_explode Array de los items de la URL
     * @param int $url_num Número de items de la URL
     */
    public function validRoute(string $url, array $url_explode, int $url_num):bool
    {
        if ($this->method == Router::getMethod() && ($this->pathCount == $url_num || $this->pathCount == ($url_num + 1))){
            foreach ($this->pathActions as $index){
                if ($this->pathItems[$index] == ($url_explode[$index] ?? '')){
                    $this->httpRequest = $url;
                }else{
                    return false;
                }
            }
            return true;
        }else{
            return false;
        }
    }
    
    
    /**
     * @param array Return an array of parameters.
     */
    private function getHttpParams():array
    {
        $url_items = explode('/', $this->httpRequest);
        $params = [];

        foreach ($this->pathParams as $param) {
            $index = $param->index;
            $name = $param->name;
            $type = $param->type;
            $optinal = $param->optional;

            if (isset($url_items[$index])){
                $value = $url_items[$index];
                if ($optinal && $value == ''){
                    // $params[$name] = null;
                }else{
                    $params[$name] = match ($type){
                        'int'   => (int)$url_items[$index],
                        'float' => (float)$url_items[$index],
                        default => $url_items[$index]
                    };
                }
            }
        }
        return $params;
    }

    /**
     * Ejecuta la acción asignada a la ruta de acceso. y retorna un objeto response.
     * @throws Throwable en coso de ocurrir un error en la ejecución.
     */
    public function callAction():Response
    {
        try {
            
            $http_method = Router::getMethod();
    
            // Validamos lo canActivates;
            foreach ($this->canActivate as $canActivate)
            {
                $result = $canActivate();

                // Si el canActivate retorna un objeto Response. detenemos la acción.
                if ($result){
                    return $result;
                }
            }
    
            if (is_callable($this->action)){
                $reflection = new ReflectionFunction($this->action);
            }else{
                // Inicalizamos el controlador
                $namespace = $this->action[0];
                $controller_method = $this->action[1] ?? $http_method;
    
                // Inicializamos la case del controlador.
                $controller = new $namespace();
    
                // En caso de que el método no existe en la clase controlador
                // retornamos un error
                if (!method_exists($controller, $controller_method)){
                    throw new ApiException(
                        [
                            'No se encontrol el metodo ha ejecutar en el controladro', 
                            "$namespace::$controller_method"
                        ],
                        null,
                        'Error controller'
                    );
                }
    
                $reflection = new ReflectionMethod($controller, $controller_method);
            }
    
            $params = $this->getHttpParams();
            $params_count = count($params);
            $num_params = $reflection->getNumberOfParameters();
            $num_params_require = $reflection->getNumberOfRequiredParameters();
    
            if ($num_params == 0 && $params_count > 0){
                return new Response("Method not allowed for URL - params > required", 404);
            }else{
                if ($num_params_require <= $params_count){
    
                    if ($reflection::class == ReflectionFunction::class){
                        return $reflection->invokeArgs($params);
                    }else{
                        return $reflection->invokeArgs($controller, $params);
                    }
    
    
                }else{
                    return new Response("Error - params - url", 400);
                }
            }
    
            return new Response([$params, $num_params, $num_params_require]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}