<?php

namespace Kenjiefx\Forte\GitHub;

use Kenjiefx\Forte\Helpers\Configuration;
use Kenjiefx\Forte\Exceptions\NotFoundException;

class GitHubAPI
{
    private $curlHandler;
    private string $username;
    private string $token;
    public const BASE_API = 'https://api.github.com/';

    public function __construct(
        int $AuthMethod
        )
    {
        $this->curlHandler = curl_init();
        $this->username    = Configuration::init()['username'];
        $this->token       = Configuration::init()['token'];
        $this->setHttpAuth($AuthMethod);
        $this->setUserAgent();
        $this->setReturnTransfer();
    }

    public function setHttpAuth(
        int $AuthMethod
        )
    {
        curl_setopt($this->curlHandler,CURLOPT_HTTPAUTH,$AuthMethod);
        if ($AuthMethod===1)
            curl_setopt(
                $this->curlHandler,
                CURLOPT_USERPWD, 
                $this->username.':'.$this->token
           );
    }

    public function setUserAgent(
        ?string $userAgent = null
        )
    {
        curl_setopt($this->curlHandler, 
            CURLOPT_USERAGENT, 
            $userAgent ?? 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'
        );
    }

    public function setReturnTransfer()
    {
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER,1);
    }

    public function getRefs(
        string $repository
        )
    {
        curl_setopt($this->curlHandler,CURLOPT_URL,GitHubAPI::BASE_API.'repos/'.$repository.'/git/refs');
        $result = json_decode(curl_exec($this->curlHandler),TRUE);
        if (isset($result['message']))
                if ($result['message']==='Not Found')
                    throw new NotFoundException('[github] repository not found: '.$repository);
        return $result;
    }

    public function getTree(
        string $repository,
        string $sha
        )
    {
        curl_setopt($this->curlHandler, CURLOPT_URL,GitHubAPI::BASE_API.'repos/'.$repository.'/git/trees/'.$sha);
        $result = json_decode(curl_exec($this->curlHandler),TRUE);
        return $result;
    }

    public function getFile(
        string $fileUrl
        )
    {
        curl_setopt($this->curlHandler,CURLOPT_URL, $fileUrl);
        $result = json_decode(curl_exec($this->curlHandler),TRUE);
        return $result;
    }
}
