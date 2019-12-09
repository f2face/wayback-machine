<?php

/**
 * Wayback Machine (Archive.org) API wrapper
 *
 * f2face <f2face@f2face.com>
 * 2016
 *
 */

namespace f2face\WaybackMachine;

class WaybackMachine
{
    private $endpoint = 'https://archive.org/wayback';
    
    /**
     * Check website archive availability.
     * 
     * @param string|array $arg
     * 
     * @return object
     */
    public function available($arg) {
        $url = $this->endpoint . '/available?';
        
        if (!empty($arg) && is_array($arg))
            $url .= http_build_query($arg);
        
        elseif (!empty($arg) && is_string($arg) && preg_match('#^https?://#', $arg))
            $url .= http_build_query(
                array('url' => $arg)
            );
        
        else
            throw new \Exception('Argument must be a URL or an array.');
        
        $data = $this->getUrl($url);
        
        return json_decode($data);
    }
    
    /**
     * Save a web page to Archive.org Wayback Machine.
     * 
     * @param string $url
     * 
     * @return string
     */    
    public function save($url) {
        if (!preg_match('#^https?://#', $url))
            throw new \Exception('Invalid URL.');
        
        $url = 'https://web.archive.org/save/' . $url;
        
        $data = $this->getUrl($url, array(), true);
        
        if (!array_key_exists('content-location', $data)) {
            $errorNum = 0;
            
            if (isset($data['http_code'])) {
                preg_match("/ (\d{3}) /", $data['http_code'], $matches);
                
                if (sizeof($matches) == 2) {
                    $errorNum = (int)$matches[1];
                }
            }

            throw new \Exception('Page saving failed: ' . json_encode($data), $errorNum);
        }    
        
        return 'https://web.archive.org' . $data['content-location'];
    }
    
    /**
     * Get web page via cURL.
     * 
     * @param string $url
     * @param array $options
     * @param boolean $headers_only
     * 
     * @return string|array
     */
    protected function getUrl($url, $options = array(), $headers_only = false) {
        $opt = array(
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_SSL_VERIFYHOST' => false,
            'CURLOPT_CONNECTTIMEOUT' => 60,
            'CURLOPT_TIMEOUT' => 60,
            'CURLOPT_USERAGENT' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.94 Safari/537.36'
        );
        
        if (!empty($options) && is_array($options))
            $opt = array_merge($opt, $options);
        
        if (!preg_match('#^https?://#i', $url))
            throw new \Exception('Invalid URL.');
        
        $a = curl_init();
        curl_setopt($a, CURLOPT_URL, $url);
        curl_setopt($a, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($a, CURLOPT_RETURNTRANSFER, true);
        if ($headers_only) {
            curl_setopt($a, CURLOPT_HEADER, true);
        }
        curl_setopt_array($a, $this->mapCurlOptArray($opt));
        
        $out = curl_exec($a);
        
        if (!$out)
            throw new \Exception(curl_error($a));
        
        if ($headers_only)
            return $this->getCurlResponseHeaders($out);
        
        curl_close($a);
        
        return $out;
    }
    
    /**
     * Parse getURL() options to cURL options.
     * 
     * @param array $array
     * 
     * @return array
     */
    private function mapCurlOptArray($array) {
        $out = array();
        
        foreach ($array as $key => $value) {
            if (!is_string($key))
                throw new \Exception('The options array key must be a string.');
            
            $out[constant($key)] = $value;
        }
        
        return $out;
    }
    
    /**
     * Parse cURL response headers.
     * 
     * @param string $response
     * 
     * @return array
     */
    private function getCurlResponseHeaders($response) {
        $headers = array();
        
        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
        
        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0)
                $headers['http_code'] = $line;
            else {
                list ($key, $value) = explode(': ', $line);
                $headers[strtolower($key)] = $value;
            }
        }
        
        return $headers;
    }
}
