<?php

/**
 *	Wayback Machine (Archive.org) API wrapper
 *
 *	f2face <f2face@f2face.com>
 *	2016
 *
 */

namespace f2face;

class WaybackMachine
{
	private $endpoint = 'https://archive.org/wayback';
	
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
	
	protected function getUrl($url, $options = array()) {
		$opt = array(
			'timeout' => 60,
			'useragent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.94 Safari/537.36',
			'parse' => false,
		);
		
		if (!empty($options) && is_array($options))
			$opt = array_merge($opt, $options);
		
		if (!preg_match('#^https?://#i', $url))
			throw new \Exception('Invalid URL.');
		
		$a = curl_init();
		curl_setopt($a, CURLOPT_URL, $url);
		curl_setopt($a, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($a, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($a, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($a, CURLOPT_SSL_VERIFYHOST, false);
		
		$out = curl_exec($a);
		
		curl_close($a);
		
		if (!$out)
			throw new \Exception('Error retrieving URL.');
		
		return $out;
	}
}