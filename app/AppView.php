<?php

abstract class AppView {
		

		protected $views;


	
		function __construct(){

		}

				

		public function setView(string $viewPath){
			if(file_exists($viewPath)){
				$this->views[] = $viewPath;
			}
		}




		private function getViews(){
			return $this->views;
		}


		public function render(array $dataView){
			global $app_config, $lang, $app_lang, $mod_lang;
			
			ob_start();
			foreach($this->getViews() as $viewPath){
				include_once($viewPath);
			}
			$output = ob_get_clean();

			return $output;
		}
	
}