<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api\Scripts;
define('CONSOLE_COLOR_TEXT_BLANCO', '1;37');
define('CONSOLE_COLOR_TEXT_NEGRO', '0;30');
define('CONSOLE_COLOR_TEXT_GRIS', '1;3O');
define('CONSOLE_COLOR_TEXT_ROJO', '0;31');
define('CONSOLE_COLOR_TEXT_ROJO_CLARO', '1;31');
define('CONSOLE_COLOR_TEXT_VERDE', '0;32');
define('CONSOLE_COLOR_TEXT_VERDE_CLARO', '1;32');
define('CONSOLE_COLOR_TEXT_CAFE', '0;33');
define('CONSOLE_COLOR_TEXT_AMARILLO', '1;33');
define('CONSOLE_COLOR_TEXT_AZUL', '0;34');
define('CONSOLE_COLOR_TEXT_CELESTE', '1;34');
define('CONSOLE_COLOR_TEXT_MAGENTA', '0;35');
define('CONSOLE_COLOR_TEXT_MEGENTA_CLARO', '1;35');
define('CONSOLE_COLOR_TEXT_CYAN', '0;36');
define('CONSOLE_COLOR_TEXT_CYAN_CLARO', '1;36');
define('CONSOLE_COLOR_TEXT_GRIS_CLARO', '0;37');

define('CONSOLE_COLOR_BACKGROUND_NEGRO', '40');
define('CONSOLE_COLOR_BACKGROUND_ROJO', '41');
define('CONSOLE_COLOR_BACKGROUND_VERDE', '42');
define('CONSOLE_COLOR_BACKGROUND_AMARILLO', '43');
define('CONSOLE_COLOR_BACKGROUND_AZUL', '44');
define('CONSOLE_COLOR_BACKGROUND_MAGENTA', '45');
define('CONSOLE_COLOR_BACKGROUND_CYAN', '46');
define('CONSOLE_COLOR_BACKGROUND_GRIS_CLARO', '47');

class Console
{
    public static function log(string $text, $color = null)
    {
        if ($color) $text = "\e[$color" . 'm' .  $text . "\e[0m";
        echo "$text\n";
    }

    public static function create(string $path)
    {
        echo "\e[" . CONSOLE_COLOR_TEXT_VERDE_CLARO .  "mCREATE\e[0m: $path ( " . filesize($path) . " bytes )\n";
    }
}