<?php
define('APP_ENTRY', 'myApp');

use App\Webservice;
use PHPUnit\Framework\TestCase;

class WebserviceTest extends TestCase
{

	/**
	 * 
	 * @return void 
	 */
	public function testWebserviceGetCurlFailed()
	{
		$webservice = new Webservice();
		$url = "https://catfact.ninja/fact2";
		$this->assertMatchesRegularExpression('#Webservices retrieve error#', $webservice::curl($url), 'Webservice function curl failed');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testWebserviceGetCurlSuccess()
	{
		$webservice = new Webservice();
		$url = "https://catfact.ninja/fact";
		$out = $webservice::curl($url);
		$this->assertGreaterThan(2, $out->length);
	}

	/**
	 * 
	 * @return void 
	 */
	public function testWebserviceGetHttpFailed()
	{
		$webservice = new Webservice();
		$url = "https://catfact.ninja/fact2";
		$this->assertMatchesRegularExpression('#Webservices retrieve error#', $webservice::http($url), 'Webservice function http failed');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testWebserviceGetHttpSuccess()
	{
		$webservice = new Webservice();
		$url = "https://catfact.ninja/fact";
		$out = $webservice::http($url);
		$this->assertGreaterThan(2, $out->length);
	}

	/**
	 * 
	 * @return void 
	 */
	public function testWebserviceGetFileFailed()
	{
		$webservice = new Webservice();
		$url = "https://catfact.ninja/fact2";
		$this->assertMatchesRegularExpression('#Webservices retrieve error#', $webservice::file($url), 'Webservice function http failed');
	}

	/**
	 * 
	 * @return void 
	 */
	public function testWebserviceGetFileSuccess()
	{
		$webservice = new Webservice();
		$url = "https://catfact.ninja/fact";
		$out = $webservice::file($url);
		$this->assertGreaterThan(2, $out->length);
	}
}
