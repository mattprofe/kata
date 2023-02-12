<?php 

	/**
	 * 
	 * Requiere de Dotenv, (composer require symfony/dotenv)
	 * colocar en el archiv ".env"
	 *  
	 * APP_NAME=kata_demo
	 * APP_URL_BASE=https://ruta
	 * APP_VERSION=1.0
	 * 
	 * crea en la raiz del proyecto la carpeta "resources/views" y dentro crea las carpeta con el nombre de la vista a utilizar
	 * los archivos plantilla deben tener la extensión *.tpl.php (index.tpl.php)
	 * 
	 * la plantilla ya tiene por defecto constantes a utilizar como:
	 * 
	 * {{APP_VERSION}} // para la version del proyecto
	 * {{APP_NAME}} // para el nombre del proyecto
	 * {{APP_URL_BASE}} // para las urls
	 * 
	 * */

	namespace ElMattProfe\Component\Kata;

	/**
	 * 
	 * reemplaza las variables de kata {{var}}
	 * condicionales @if('var')@endif('var')
	 * @section('nombre')@endsection('nombre')
	 * @yield('nombre_bloque')
	 * @extends('archivo') trae otra plantilla dentro de la plantilla
	 * 
	 */
	class Kata
	{

		private static $tpl;
		private $buff;
		private $extends;

		/**
		 *	@param string $mode dev|prod
		 * */

		public static function loadView($template_url, $vars = false, $mode = 'dev'){

			// singleton para que solo se cree una Kata
			if(!self::$tpl instanceof self)
				self::$tpl = new self();

			// comprobamos si tenemos variables en $_ENV o en getenv
			if(isset($_ENV['APP_VERSION'])){
				$app_version = $_ENV['APP_VERSION'];
				$app_url_base = $_ENV['APP_URL_BASE'];
				$app_name = $_ENV['APP_NAME'];
			}else{
				$app_version = getenv('APP_VERSION');
				$app_url_base = getenv('APP_URL_BASE');
				$app_name = getenv('APP_NAME');
			}

			// si esta en modo desarrollo se crea un id para que no cachee el navegador
			if(strtolower($mode) == 'dev')
				$mode = uniqid();
			else
				$mode = $app_version;

			self::$tpl->load($template_url, $vars, $app_url_base, $app_name, $mode);
		}

		/**
		 * Carga un archivo *.tpl.php
		 * 
		 * @param string $url	Ruta de la plantilla
		 * @param array $variables	Vector indexado asociativamente para reemplazar  "ejemplo" => "1234" {{ejemplo}}
		 * @param string $url_project	Es el dominio del proyecto ejemplo https://www.dino.com.ar , en las rutas de las etiquetas {{URL_PROJECT}} de esta manera el htaccess no rompe las rutas
		 * @param string $version_project	Se utiliza para evitar el cacheo de archivos css/js/otros, se utiliza ?v={{VERSION_PROJECT}}
		 * 
		 * @return string	código HTML
		 */
		private function load($url, $variables = false, $url_project = false, $app_name = false, $version_project = false){

			if(!file_exists('../resources/views/'.$url.'.tpl.php')){
				die("<b>Error:</b> No se enconto la plantilla dentro de la carpeta 'resources/views/".$url.".tpl.php'<br>\n");
			}

			$this->buff = file_get_contents('../resources/views/'.$url.'.tpl.php');

			while($this->findExtends()){};

			// si tenemos una version de proyecto
			if($version_project){
				$this->buff = str_replace("{{APP_VERSION}}", $version_project, $this->buff);
			}

			// si tenemos nombre de la app
			if($app_name){
				$this->buff = str_replace("{{APP_NAME}}", $app_name, $this->buff);
			}

			// si tenemos una url de proyecto
			if($url_project){
				$this->buff = str_replace("{{APP_URL_BASE}}", $url_project, $this->buff);
			}

			// si hay variables dentro del vector
			if($variables){
				foreach ($variables as $atributo => $valor) {

					$this->buff=str_replace("@if('$atributo')", "", $this->buff);
					$this->buff=str_replace("@endif('$atributo')", "", $this->buff);

					$this->buff = str_replace("{{".$atributo."}}", $valor, $this->buff);
				}
			}

			// Quita las secciones de if sin variables existentes
			while($this->findIf()){};
			

			echo $this->buff;
		}

		private function findIf(){
			$pos = strpos($this->buff, "@if('");

			if($pos !== false){

				$init = $pos+strlen("@if('");
				$end = strpos($this->buff, "'",$init);
				$var = substr($this->buff, $init, $end-$init);

				$final = strpos($this->buff, "@endif('".$var."')")+strlen("@endif('".$var."')");

				$borrar = substr($this->buff, $pos, $final-$pos);					
				$this->buff=str_replace($borrar, "", $this->buff);

				return true;
			}

			return false;
		}

		private function findSection($name){
			$pos = strpos($this->buff, "@section('".$name."')");

			if($pos !== false){
				$init = $pos + strlen("@section('".$name."')");

				$end = strpos($this->buff, "@endsection('".$name."')");

				if($end !== false){

					$buff = substr($this->buff, $init, $end-$init);

					$this->buff = str_replace($buff, "", $this->buff);
					$this->buff = str_replace("@section('".$name."')", "", $this->buff);
					$this->buff = str_replace("@endsection('".$name."')", "", $this->buff);

					// ver de optimizar
					return $buff;
				}
			}
		}

		// yields son lugares donde se reemplazara con section
		private function findYields(){
			$pos = strpos($this->extends, "@yield('");

			if($pos !== false){

				$init = $pos+strlen("@yield('");
				$end = strpos($this->extends, "'",$init);

				$this->extends=str_replace("@yield('".substr($this->extends, $init, $end-$init)."')", $this->findSection(substr($this->extends, $init, $end-$init)), $this->extends);

				return true;
			}
			
			return false;
		}

		private function findExtends(){
			$pos = strpos($this->buff, "@extends('");

			if($pos !== false){

				//guardamos la posicion
				$init = $pos+strlen("@extends('");
				$end = strpos($this->buff, "'",$init);				

				$this->extends = file_get_contents('../resources/views/'.substr($this->buff, $init, $end-$init).'.tpl.php');

				while($this->findYields()){}

				$this->buff=str_replace("@extends('".substr($this->buff, $init, $end-$init)."')", $this->extends, $this->buff);

				return true;
			}
			
			return false;
		}
	}

 ?>