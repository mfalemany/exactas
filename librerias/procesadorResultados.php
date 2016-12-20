<?php 
	require_once("reporte_encuesta.php");
	
	class ProcesadorResultados{
		private $error; //contiente detalles de errores en caso de ocurrir
		private $preguntas;
		private $observaciones;
		private $nombre_elemento; //Contiene un nombre de catedra o de docente
		private $acumulador_opcion;
		private $archivos_resultados;
		private $cuatrimestre; 
		private $anio_cursado;
		private $asignatura;
		public $numero_encuestados;
		

		//constructor de clase
		function __construct(){

			//controlamos que todos los archivos se hayan subido y movido con exito
			$this->verificar_archivos();
			$this->set_error();
			$this->set_preguntas();
			$this->set_nombre_elemento();
			$this->acumulador_opcion();
			$this->numero_encuestados = 0;
			
			//datos ingresados a mano
			$this->cuatrimestre = $_POST['cuatrimestre'];
			$this->anio_cursado = $_POST['anio_cursado'];
			$this->asignatura = $_POST['asignatura'];

			//recorro los archivos de resultados y los proceso uno a uno
			foreach($this->archivos_resultados as $categoria => $archivos){
				foreach ($archivos as $indice => $nombre_archivo) {
					if( ! $this->leer_archivo_resultados($categoria, $nombre_archivo)){
						echo $this->get_error();

						die;
					}	
				}
			}

			$this->calcular_numero_encuestados();
		}

		public function set_error($error = FALSE){
			$this->error = $error;
		}
		public function set_preguntas($preguntas = array()){
			if(is_array($preguntas)){
				if(count($preguntas) > 0){
					$this->preguntas = $preguntas;		
				}else{
					$this->preguntas = array();
				}
			}
			
		}
		
		public function set_nombre_elemento($nombre_elemento = null){
			$this->nombre_elemento = $nombre_elemento;
		}
		
		public function get_error(){
			return $this->error;
		}
		public function get_preguntas(){
			return $this->preguntas;
		}
		
		public function get_nombre_elemento(){
			return $this->nombre_elemento;
		}
				
		
		public function get_acumulador_opcion(){
			if(count($this->acumulador_opcion) > 0){
				return $this->acumulador_opcion;
			}else{
				return array();
			}
		}
		public function get_cuatrimestre(){
			return $this->cuatrimestre;
		}
		public function get_anio_cursado(){
			return $this->anio_cursado;
		}
		public function get_asignatura(){
			return $this->asignatura;
		}

		private function leer_archivo_resultados($categoria, $ubicacion){

			//si el archivo no existe o no ex válido
			if( ! is_file($ubicacion)){
				$this->set_error("Ocurrió un error al intentar leer el archivo de $categoria: $ubicacion, por lo cual los números procesados no reflejan la totalidad de respuestas. El reporte no se generará.");
				return FALSE;
			}
			
			//mantiene el número de linea actualmente leida desde el archivo
			$linea = 0;
					
			// variable que contiene el contenido del archivo en texto plano
			$archivo = fopen($ubicacion,"r");

			//mientras pueda leer una linea del fichero
			while($registro = fgets($archivo) ){
				
				//obtengo campos por separado
				$campos = explode("|", $registro);
				
				/*esta fila contiene los encabezados  (tiene que ver con el formato del archivo generado por SIU-KOLLA) */
				if($linea > 0){
					//solo en el primer registro
					if($linea == 1){
						//si no se asignó un nombre de elemento (solo sucede al leer el primer archivo)
						if( ! $this->get_nombre_elemento() ){
							//obtengo el nombre del docente/catedra
							$this->set_nombre_elemento($campos[2]);
						}
						$linea++;
					}
					$linea++;
					
					//registro la pregunta y opcion actuales
					$pregunta = $campos[5];
					$opcion = $campos[6];

					//si aún no se inicializó la categoria en el array de preguntas, lo hacemos
					if( ! array_key_exists($categoria, $this->get_preguntas())){
						$this->preguntas[$categoria] = array();
					}
					$preg = $this->get_preguntas();
					
					//si todavía no existe la pregunta, la inicializo como indice del array
					if( ! array_key_exists($pregunta,$preg[$categoria]) ){
						$this->preguntas[$categoria][$pregunta] = array();	
					}
					$preg = $this->get_preguntas();

					//si no existe la opcion dentro de la pregunta definida, la agrego como indice e inicializo en uno
					if( ! array_key_exists($opcion, $preg[$categoria][$pregunta]) ){
						$this->preguntas[$categoria][$pregunta][$opcion] = 1;
					}else{
						//si ya está definida, solo sumo uno
						$this->preguntas[$categoria][$pregunta][$opcion]++;
					}
					//El acumulador por opción sirve para generar el gráfico
					$this->acumulador_opcion($categoria,$opcion);
					
				}else{
					$linea++;
				}	
				
			}
			
			return true;
			

		}

		/* El atributo "acumulador_opcion" mantiene la cantidad de respuestas de cada tipo */
		private function acumulador_opcion($categoria = '', $opcion = null ){
			//inicializa el contador
			if( ! $opcion ){
				$this->acumulador_opcion['catedra'] = array();
				$this->acumulador_opcion['docente'] = array();
			}else{
				if(array_key_exists($opcion,$this->acumulador_opcion[$categoria])){
					$this->acumulador_opcion[$categoria][$opcion]++;
				}else{
					$this->acumulador_opcion[$categoria][$opcion] = 1;
				}
				
			}

		}

		//funcion que comprueba los archivos subidos y los mueve a la carpeta de temporales para trabajarlos
		private function verificar_archivos(){

			//recorro todos los archivos recibidos
			foreach ($_FILES as $nombre => $detalles) {
				//intento mover el archivo subido a la carpeta de temporales
				if( move_uploaded_file($detalles['tmp_name'], "../temporales/".$nombre.".txt" )){
					//verifico si es un archivo de resultados docente o cátedra
					if( preg_match("/file_doc_(.)*/i", $nombre) ){
						//creo un array con cada conjunto de archivos
						$this->archivos_resultados['docente'][] = "../temporales/".$nombre.".txt";
					}else{
						$this->archivos_resultados['catedra'][] = "../temporales/".$nombre.".txt";
					}
				}else{
					//configurar que hacer con los archivos que fallaron al mover
				}
			}
			//var_dump($this->archivos_resultados); die;
		}

		private function calcular_numero_encuestados(){
			if(array_key_exists("docente",$this->get_preguntas())){
				$elemento = "docente";
			}else{
				if(array_key_exists("docente",$this->get_preguntas())){
					$elemento = "catedra"; 
				}else{
					return false;
				}
			}
			$preg = $this->get_preguntas();
			$preg = $preg[$elemento];
			foreach(current($preg) as $opcion => $cantidad){
				$this->numero_encuestados += $cantidad;
			}
		}
	}
	
	//proceso los resultados
	$datos = new ProcesadorResultados();

	//var_dump($datos->get_acumulador_opcion());die;
	//y con los resultados ya procesados, genero el reporte de encuesta
	$reporte = new ReporteEncuesta($datos);	
?>