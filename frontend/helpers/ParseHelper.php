<?php
namespace frontend\helpers;

use DOMDocument;
use DOMXPath;

class ParseHelper
{
    const USER_AGENT_FILENAME = 'user_agents_list.txt';
    const DIR_NAME_WITH_FILES = 'files';

    /**
     * @param $url
     * @return mixed
     */
    public static function getRequestToUrl($url)
    {
        $userAgent = file(self::DIR_NAME_WITH_FILES . DIRECTORY_SEPARATOR. self::USER_AGENT_FILENAME);
        $userAgentsCount = count($userAgent) - 1;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $code = $userAgent[rand(0, $userAgentsCount)]);
        curl_setopt($ch, CURLOPT_HEADER, 0); // 1
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // added
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // added
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * @param $content
     * @return DOMXPath
     */
    public static function dom($content)
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $doc->loadHTML($content);
        libxml_use_internal_errors($internalErrors);
        return new DOMXpath($doc);
    }
}