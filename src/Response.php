<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phpnv\Api;

use SplFileInfo;

class Response
{
    /**
     * @param mixed $body valor que desea responser en la api
     */
    public function __construct(
        public mixed $body,
        public int $reponseCode = 200,
        public $type = 'json'
    ){}

    public function echo(){
        switch ($this->type) {
            case 'json':
                header('content-type: application/json');
                echo str_replace( '\/', '/', json_encode($this->body));
                break;
            case 'file':
                $path = Api::getApi()->getResourcesDir() . "/" . $this->body;
                $file = new SplFileInfo($path);
                $exte = $file->getExtension();
                $type_content = $this->getContentType($exte);
                if ($type_content) header("content-type: $type_content");
                require $path;
                break;
            default:
                
                break;
        }
        http_response_code($this->reponseCode);
        exit;
    }

    private function getContentType(string $extension):string
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
