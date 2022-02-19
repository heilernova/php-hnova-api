<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api\Files;

class File
{
    private $file;
    public function __construct($path = null, $mode = 'a+')
    {
        if ($path){
            $this->file = fopen($path, $mode);
        }
    }

    public static function openToRewrite(string $path):File
    {
        $f = new File($path, 'w+');
        return $f;
    }

    public static function create():File
    {
        return new File();
    }

    public function addText(string $text){
        fputs($this->file, $text);
    }

    public function addContentFile(string $path){
        $this->addText(file_get_contents($path));
    }

    public function addLine(string $text):void
    {
        $this->addText($text ."\n");
    }

    public function close(){
        fclose($this->file);
    }
}