<?php
 /* This file is part of HNova/api.
 *
 * (c) Heiler Nova <https://github.com/heilernova>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HNova\Api\Http;

class Response
{
    private mixed $_body = null;
    private int $_code = 200;
    private array $_headers = [];
    private string $_exposeHeaders = 'nv-data';
    private array $_messages = [];

    public function __construct($body = null, private array $config = ['type'=>'json'])
    {
        $this->_body = $body;
    }

    public function status(int $code):Response{
        $this->_code = $code;
        return $this;
    }

    public function addMessage(string $text):Response{
        $this->_messages[] = $text;
        return $this;
    }

    public function addHeader(array $name, $value):Response{
        $this->_headers[$name] = $value;
        $this->_exposeHeaders .= ", $name";
        return $this;
    }

    public function echo(): void {
        $headers = $this->_headers;
        $expose_headers = '';
        $body = $this->_body;

        $api_data = [
            'messge' => $this->_messages
        ];

        $headers['nv-data'] = json_encode($api_data);
        
        switch ($this->config['type']) {
            case 'text':
                
                $headers['Content-Type'] = "text; charset=UFT-8";
                break;
            case 'json':

                $headers['Content-Type'] = "application/json; charset=UFT-8";
                $body = json_encode($body);
                break;
            case 'file':
                $auto_delete = $this->config['auto-delete'] ?? true;
                break;
            case 'html':
                $headers['Content-Type'] = "text/html; charset=UFT-8";
            default:
                break;
        }

        http_response_code($this->_code);
        foreach ($headers as $key=>$value) {
            header("$key: $value");
        }
        $expose_headers = ltrim($this->_exposeHeaders, ', ');
        header("Access-Control-Expose-Headers: $expose_headers");
        echo $body;
    }
}