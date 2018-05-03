<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 13/11/17
 * Time: 10:23
 */

namespace TodoPago\Utils;

class GithubApi
{
    protected $url;

    public function __construct($url)
    {
        $this->setUrl($url);
    }

    public function github_get()
    {
        $header = array(
            'Content-Type: application/json',
            'Authorization: ' . Constantes::TODOPAGO_TOKEN
        );
        if (function_exists('curl_version'))
            $ch = curl_init();
        else {
            return 501;
        }
        if (isset($ch)) {
            curl_setopt($ch, CURLOPT_URL, $this->getUrl());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'TodoPagoWordpress');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $server_output = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
            if (empty($error))
                return json_decode($server_output);
            else
                return $error;
        } else {
            return 500;
        }

    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
