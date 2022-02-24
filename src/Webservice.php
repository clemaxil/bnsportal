<?php

namespace App;

use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

/**
 * Get webservices different methods
 * @package webservices get methods
 */
class Webservice
{
	/**
	 * 
	 * @param mixed $url 
	 * @return mixed 
	//  * @throws TransportExceptionInterface 
	//  * @throws RedirectionExceptionInterface 
	//  * @throws ClientExceptionInterface 
	//  * @throws ServerExceptionInterface 
	//  * @throws Exception 
	 */
	public static function http($url)
	{
		//REGRESSION PHP 7.3
		return self::curl($url);
		
		//ONLY PHP 7.4
		// $client = HttpClient::create(['verify_peer' => false, 'verify_host' => false]);
		// $response = $client->request('GET', $url);
		// $statusCode = $response->getStatusCode();
		// if ($statusCode === 200) {
		// 	$content = $response->getContent();
		// 	return json_decode($content);
		// } else {
		// 	return 'Webservices retrieve error';
		// }
	}


	/**
	 * 
	 * @param mixed $url 
	 * @return mixed 
	 * @throws Exception 
	 */
	public static function curl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$curlResponse = curl_exec($ch);
		$response = json_decode($curlResponse);

		$response_headers['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response_headers['error']['code'] = curl_errno($ch);
		$response_headers['error']['message'] = curl_error($ch);

		if ($response_headers['http_code'] == 200 || $response_headers['http_code'] == 201) {
			curl_close($ch);
			return ($response);
		} else {
			$error = $response_headers['http_code'] . ' - ' . $response_headers['error']['code'] . ' - ' . $response_headers['error']['message'];
			curl_close($ch);
			return 'Webservices retrieve error ' . $error;
		}
	}


	/**
	 * 
	 * @param string $url 
	 * @return mixed 
	 * @throws Exception 
	 */
	public static function file($url)
	{
		$context = stream_context_create(
			array(
				'ssl' => array(
					"verify_peer"=>false,
					"verify_peer_name"=>false
				),
				'https' => array(
					'header' => 'Connection: close\r\n'
				),
			)
		);
		$content = file_get_contents($url, false, $context);
		if ($content === false) {
			return 'Webservices retrieve error';
		} else {
			return (json_decode(trim($content, "\xEF\xBB\xBF")));
		}
	}
}
