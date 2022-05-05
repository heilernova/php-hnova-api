<?php
/*
 * This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http;

use Composer\XdebugHandler\Status;

class HttpFuns
{
    /**
     * @return string Obtine la ip de cliente que realiza la petición
     */
    public static function getIp():string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? ( $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
    }

    
    /**
     * Obtiene el sistema operativo desde el cual se realiza la petición http
     */
    public static function getPlatform():string
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $plataformas = array(
            'Windows' => 'Windows',
            'iPhone' => 'iPhone',
            'iPad' => 'iPad',
            'Mac OS X' => '(Mac OS X+)|(CFNetwork+)',
            'Mac otros' => 'Macintosh',
            'Android' => 'Android',
            'BlackBerry' => 'BlackBerry',
            'Linux' => 'Linux'
        );
        foreach($plataformas as $plataforma=>$pattern){
            if (preg_match('/(?i)'.$pattern.'/', $user_agent)) return $plataforma;
        }
        return 'others';
    }

    /**
     * Obtiene el tipo de dispositivo del client desde el cual se realiza la peticion http
     * @return int Return 1 for mobile, 2 for tablets, 3 for desktop
     * @link https://mobiforge.com/design-development/tablet-and-mobile-device-detection-php
     * @link http://www.7sabores.com/blog/detectar-tipo-dispositivo-mobile-tablet-desktop-php
     */
    public static function getDevice():int
    {
        $tablet_browser = 0;
        $mobile_browser = 0;
        $body_class = 'desktop';
         
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $tablet_browser++;
            $body_class = "tablet";
        }
         
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
            $body_class = "mobile";
        }
         
        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
            $body_class = "mobile";
        }
         
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda ','xda-');
         
        if (in_array($mobile_ua,$mobile_agents)) {
            $mobile_browser++;
        }
         
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
            $mobile_browser++;
            //Check for tablets on opera mini alternative headers
            $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
              $tablet_browser++;
            }
        }
        if ($tablet_browser > 0) {
           return 2; # Table
        }
        else if ($mobile_browser > 0) {
           return 1; # Mobile
        }
        else {
            return 3; # desktop
        }  
    }

    public static function getContentType(string $extension):?string
    {
        switch ($extension) {
            case 'png': return "image/$extension";
            case 'jpg': return "image/$extension";
            case 'jpeg': return "image/$extension";
            case 'git': return "image/$extension";

            case 'pdf': return 'application/pdf';

            case 'doc': return "application/msword";
            case 'dot': return "application/msword";

            case 'docx': return "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            case 'dotx': return "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
            case 'docm': return "application/vnd.ms-word.document.macroEnabled.12";
            case 'dotm': return "application/vnd.ms-word.document.macroEnabled.12";

            case 'xls': return "application/vnd.ms-excel";
            case 'xlt': return "application/vnd.ms-excel";
            case 'xla': return "application/vnd.ms-excel";

            case 'xlsx': return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            case 'xltx': return "application/vnd.openxmlformats-officedocument.spreadsheetml.template";

            case 'xlsm': return "aapplication/vnd.ms-excel.sheet.macroEnabled.12";
            case 'xltm': return "application/vnd.ms-excel.template.macroEnabled.12";

            case 'xlam': return "application/vnd.ms-excel.addin.macroEnabled.12";
            case 'xlsb': return "pplication/vnd.ms-excel.sheet.binary.macroEnabled.12";

            case 'ppt': return "application/vnd.ms-powerpoint";
            case 'pot': return "application/vnd.ms-powerpoint";
            case 'pps': return "application/vnd.ms-powerpoint";
            case 'ppa': return "application/vnd.ms-powerpoint";

            case 'pptx': return "application/vnd.openxmlformats-officedocument.presentationml.presentation";
            case 'potx': return "application/vnd.openxmlformats-officedocument.presentationml.template";
            case 'ppsx': return "application/vnd.openxmlformats-officedocument.presentationml.slideshow";
            case 'ppam': return "application/vnd.ms-powerpoint.addin.macroEnabled.12";
            case 'pptm': return "application/vnd.ms-powerpoint.presentation.macroEnabled.12";
            case 'potm': return "application/vnd.ms-powerpoint.template.macroEnabled.12";
            case 'ppsm': return "application/vnd.ms-powerpoint.slideshow.macroEnabled.12";
            
            case 'mdb': return "application/vnd.ms-access";

            default: null;
        }
    }
}