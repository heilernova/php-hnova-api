<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

// retornamos el contenido HTML y los javascripts para el funcionamiento de la app panel.
Routes::get('nv-panel', function(){
    require __DIR__.'/../view/nv-panel/index.php';
    exit;
});