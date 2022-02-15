<?php

declare(strict_types=1);

namespace App;


/**
 * Main View
 * @package MainView
 */
class View
{

	/**
	 * @var string[]
	 */
	protected $views;


	/**
	 * 
	 * @return void 
	 */
	public function __construct()
	{
		$this->lang = $_REQUEST['lang'];
		$this->module = $_REQUEST['module'];
		$this->id = $_REQUEST['id'];
		$this->app_lang = include __DIR__ . '/../languages/' . $this->lang . '.php';
		$module_dir = __DIR__ . '/../modules/' . $this->module;
		$this->mod_lang = include $module_dir . '/languages/' . $this->lang . '.php';
	}

	/**
	 * Set this view in array views if exists
	 * 
	 * @param string $viewPath string path complet for view,layout,template
	 * 
	 * @return void 
	 */
	public function setView(string $viewPath): void
	{
		if (file_exists($viewPath)) {
			$this->views[] = $viewPath;
		}
	}



	/**
	 * Get this all layout
	 * 
	 * @return string[]  $viewPath 
	 */
	public function getViews(): array
	{
		return $this->views;
	}

	/**
	 * 
	 * @param array $dataView 
	 * @return string 
	 */
	public function render(array $dataView): string
	{
		$module = $this->module;
		$lang = $this->lang;
		$id = $this->id;
		$app_lang = $this->app_lang;
		$mod_lang = $this->mod_lang;

		ob_start();
		foreach ($this->getViews() as $viewPath) {
			include_once($viewPath);
		}
		$output = ob_get_clean();
		$this->views = array();

		return $output;
	}

	/**
	 * 
	 * @param string $message 
	 * @param string $alert 
	 * @param bool $translate 
	 * @return void 
	 */
	public function setFlash(string $message, string $alert, bool $translate = false)
	{
		if ($translate === true) {
			$message = $this->mod_lang[$message];
		}

		$_SESSION['flash'] = [
			'message' => $message,
			'type' => $alert
		];
	}
}
