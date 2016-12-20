<?php 
	require_once "fpdf/fpdf.php";
	include_once "pchart/pChart/pData.class";
 	include_once "pchart/pChart/pChart.class";

	class ReporteEncuesta extends FPDF{
		private $imagen_encabezado;

		function __construct($datos){
			parent::__construct();
			$this->set_imagen_encabezado("../assets/img/encabezado.jpg");
			$this->SetTopMargin(50);
			$this->generar($datos);
		}

		private function set_fuente($familia,$tamano,$color_texto,$color_fondo,$variantes,$color_borde){
			$this->setFont($familia,$variantes,$tamano);
			$this->setTextColor($color_texto[0],$color_texto[1],$color_texto[2]);
			$this->SetFillColor($color_fondo[0],$color_fondo[1],$color_fondo[2]);
			$this->SetDrawColor($color_borde[0],$color_borde[1],$color_borde[2]);
		}

		
		public function set_imagen_encabezado($imagen){
			$this->imagen_encabezado = $imagen;
		}

		public function get_imagen_encabezado(){
			return $this->imagen_encabezado;
		}

		public function Header(){
			$imagen = $this->get_imagen_encabezado();
			$extension = explode(".",$imagen);
			$this->Image($imagen,20,15,0,0, strtoupper(end($extension)));
		}

		public function Footer(){
		    $this->SetY(-15);
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
		}

		function generar($datos){
			//genero una página
			$this->AddPage();
			
			//Obtengo el nombre del elemento bajo observación
			$nom_elem = explode("(",$datos->get_nombre_elemento());
			
			//Acá se muestran los detalles de la encuesta
			//ASIGNATURA
			$this->setXY(40,44);
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'B',array(255,255,255));
			$this->Cell(17,4,utf8_decode('Asignatura: '),1,0,'L',true);
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'',array(255,255,255));
			$this->Cell(0,4,utf8_decode($datos->get_asignatura()),1,1,'L',true);
			
			//AÑO DE CURSADO
			$this->setX(32);
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'B',array(255,255,255));
			$this->Cell(25,4,utf8_decode('Año de Cursado: '),1,0,'L',true);
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'',array(255,255,255));
			$this->Cell(22,4,$datos->get_anio_cursado(),1,0,'L',true);

			//AÑO DE CURSADO
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'B',array(255,255,255));
			$this->Cell(20,4,'Cuatrimestre: ',1,0,'L',true);
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'',array(255,255,255));
			$this->Cell(22,4,$datos->get_cuatrimestre(),1,0,'L',true);

			//AÑO DE CURSADO
			
			//ESTE PUNTO ES X:121 Y:48
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'B',array(255,255,255));
			$this->Cell(33,4,'Alumnos Encuestados: ',1,0,'L',true);
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'',array(255,255,255));
			$this->Cell(15,4,"",1,1,'L',true);

			//NOMBRE DEL DOCENTE
			$this->setX(12);
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'B',array(255,255,255));
			$this->Cell(44,4,'Apellido y Nombre del Docente: ',1,0,'L',true);
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'',array(255,255,255));
			$this->Cell(0,4,$datos->get_nombre_elemento(),1,1,'L',true);

			$this->Ln();
			
			//las dos categorias de preguntas (el orden de este array influye el orden de aparicion en el reporte)
			$categorias = array(
				"catedra"=>array("Muy Bueno"=>0,"Bueno"=>0,"Regular"=>0,"Malo"=>0,"No sabe"=>0),
				"docente"=>array("Muy Bueno"=>0,"Bueno"=>0,"Regular"=>0,"Malo"=>0,"No sabe"=>0)
			);
			


			foreach($categorias as $categoria => $totales){
				$acum_opc = $datos->get_acumulador_opcion();
				if( count($acum_opc[$categoria]) == 0 ){
					continue;
				}
				
				//obtengo los subtotales, el puntaje y la cantidad total de respuestas por categoria y general
				$subtotales = $this->calcular_puntaje($datos->get_acumulador_opcion());
				

				//SOBRE LA ASIGNATURA/DOCENTE
				$y = $this->getY();
				
				$this->set_fuente("Times",10,array(0,0,0),array(255,255,255),'B',array(0,0,0));
				$margen = $this->getX();
				$ancho_col_1 = 70;
				if($categoria == "catedra"){
					$this->Cell($ancho_col_1,8,'SOBRE LA ASIGNATURA ',1,0,'C',true);	
				}else{
					$this->Cell($ancho_col_1,8,'SOBRE EL DOCENTE',1,0,'C',true);
				}
				

				$inicio_col_2 = $margen + $ancho_col_1;
				$ancho_col_2 = 27;
				$this->setXY($inicio_col_2,$y);
				$this->set_fuente("Times",8,array(0,0,0),array(255,255,255),'B',array(0,0,0));
				$this->Cell($ancho_col_2,4,'Puntaje: ',1,1,'R',true);
				$this->setX($inicio_col_2);
				$this->Cell($ancho_col_2,4,'Resultado: ',1,0,'R',true);
				
				$inicio_col_3 = $inicio_col_2 + $ancho_col_2;
				$ancho_col_3 = 0;
				$this->setXY($inicio_col_3,$y);
				$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'B',array(0,0,0));
				$this->Cell($ancho_col_3,4,round($subtotales[$categoria]['puntaje'],2),1,1,'L',true);
				$this->setX($inicio_col_3);
				$this->Cell($ancho_col_3,4,$this->obtener_resultado($subtotales[$categoria]['puntaje']),1,1,'L',true);

				//encabezado de los resultados (títulos de las columnas)
				$this->set_fuente("Times",9,array(0,0,0),array(255,255,255),'B',array(0,0,0));
				$ancho_col = 9;
				
				//encabezado de los resultados (títulos de las columnas)
				$this->Cell($ancho_col,4,"Nro.",1,0,'C',true);
				$this->Cell(136,4,"Pregunta",1,0,'C',true);
				$this->Cell($ancho_col,4,"MB",1,0,'C',true);
				$this->Cell($ancho_col,4,"B",1,0,'C',true);
				$this->Cell($ancho_col,4,"R",1,0,'C',true);
				$this->Cell($ancho_col,4,"M",1,0,'C',true);
				$this->Cell($ancho_col,4,"NS",1,1,'C',true);
				
				//muestro todos los numeros y de paso calculo los subtotales por opcion
				$num = 1;
				$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'',array(0,0,0));
				$preg=$datos->get_preguntas();
				foreach ($preg[$categoria] as $pregunta => $opciones) {
					$this->Cell($ancho_col,6,$num,1,0,'C',true);
					$y = $this->getY();
					
					
					if(strlen($pregunta) < 110){
						$pregunta .= "\n     ";
					}
					//la pregunta puede ser multilinea
					$this->MultiCell(136,3,$pregunta,1,'L',true);

					
					$this->setXY(155,$y);

					if(array_key_exists("Muy Bueno", $opciones)){
						$this->Cell($ancho_col,6,$opciones["Muy Bueno"],1,0,'C',true);	
						$categorias[$categoria]['Muy Bueno'] += $opciones["Muy Bueno"];
					}else{
						$this->Cell($ancho_col,6,"0",1,0,'C',true);	
					}
					if(array_key_exists("Bueno", $opciones)){
						$this->Cell($ancho_col,6,$opciones["Bueno"],1,0,'C',true);	
						$categorias[$categoria]['Bueno'] += $opciones["Bueno"];
					}else{
						$this->Cell($ancho_col,6,"0",1,0,'C',true);	
					}
					if(array_key_exists("Regular", $opciones)){
						$this->Cell($ancho_col,6,$opciones["Regular"],1,0,'C',true);	
						$categorias[$categoria]['Regular'] += $opciones["Regular"];
					}else{
						$this->Cell($ancho_col,6,"0",1,0,'C',true);	
					}
					if(array_key_exists("Malo", $opciones)){
						$this->Cell($ancho_col,6,$opciones["Malo"],1,0,'C',true);	
						$categorias[$categoria]['Malo'] += $opciones["Malo"];
					}else{
						$this->Cell($ancho_col,6,"0",1,0,'C',true);	
					}
					if(array_key_exists("No sabe", $opciones)){
						$this->Cell($ancho_col,6,$opciones["No sabe"],1,1,'C',true);	
						$categorias[$categoria]['No sabe'] += $opciones["No sabe"];
					}else{
						$this->Cell($ancho_col,6,"0",1,1,'C',true);	
					}
					$num++;
				}
				
				//totales de la categoría
				if($categoria == "catedra"){
					$this->Cell(($ancho_col+136),4,"Totales sobre la asignatura",1,0,'C',true);
				}else{
					$this->Cell(($ancho_col+136),4,"Totales sobre el docente",1,0,'C',true);
				}
				$this->Cell($ancho_col,4,$categorias[$categoria]["Muy Bueno"],1,0,'C',true);
				$this->Cell($ancho_col,4,$categorias[$categoria]["Bueno"],1,0,'C',true);
				$this->Cell($ancho_col,4,$categorias[$categoria]["Regular"],1,0,'C',true);
				$this->Cell($ancho_col,4,$categorias[$categoria]["Malo"],1,0,'C',true);
				$this->Cell($ancho_col,4,$categorias[$categoria]["No sabe"],1,1,'C',true);
				$this->Ln();	
			}//cierra el foreach

			
			//imprimo en la cabecera del reporte, el numero total de encuestados (antes, guardo la posicion actual para volver)
			$y = $this->getY();
			$x = $this->getX();

			$this->setXY(154,48);
			$this->Cell(7,4,$datos->numero_encuestados,0,1,'',false);

			//vuelvo a la posicion anterior
			$this->setY($y);
			$this->setX($x);

			//genero los graficos (pero antes, verifico que tengan algún valor)
			$generar = false;
			foreach (array_values($categorias['catedra']) as $key => $value) {
				if($value>0){
					$generar=true;
				}
			}
			if($generar){
				$y_alt = $this->getY();
				$x=46;
				$this->setX($x);
				$this->Cell(60,5,"Opinion del alumnado sobre la asignatura",0,0,'C',false);

				//se genera el grafico con los totales
	 			$nombre_grafico = $this->generar_grafico($categorias['catedra'],"catedra");
				$y = $this->getY() + 5;
				$this->setY($y);
	 			$this->Image($nombre_grafico, $x+4, null, 51, 34, "PNG");
			}
			$generar = false;
			foreach (array_values($categorias['docente']) as $key => $value) {
				if($value>0){
					$generar=true;
				}
			}
			if($generar){
				$x2 = $x + 80;
				$this->setXY($x2,$y_alt);
				$this->Cell(40,5,"Opinion del alumnado sobre el docente",0,0,'C',false);
				//se genera el grafico con los totales
	 			$nombre_grafico = $this->generar_grafico($categorias['docente'],"docente");
				$this->setY($y); 			
	 			$this->Image($nombre_grafico, ($x2-4), null, 51, 34, "PNG");
			}
 			
			$this->Ln();
			
			//fuente para el ultimo cuadro de Puntaje General
			$this->set_fuente("Arial",8,array(0,0,0),array(255,255,255),'B',array(0,0,0));
			
			//obtengo la posicion vertical actual para imprimir un cuadro al lado
			$posicion = $this->getY();

			$this->Cell(100,4,"Puntaje General",1,1,'C',true);
			
			//imprimo el cuadro

			$this->Cell(50,4,"Puntaje: ",1,0,'R',false); 
			$this->Cell(50,4,round($subtotales['general']['puntaje'],2),1,1,'L',false);
			$this->Cell(50,4,"Resultado:",1,0,'R',false); 
			$this->Cell(50,4,$this->obtener_resultado($subtotales['general']['puntaje']),1,1,'L',false);

			//Cuadro de Referencias
			$this->setY($posicion);
			$this->setX(115);
			$this->MultiCell(80,4,"Referencias\n0.00 - 0.99: No Satisfactorio\n1.00 - 1.49: Satisfactorio con Observaciones\n1.50 - 3.00: Satisfactorio",1,'L',false);
			
			//Se imprime el PDF
			//$this->Output("D",$datos->get_nombre_elemento().".pdf");
			$this->Output("F","../temporales/".$datos->get_nombre_elemento().".pdf",TRUE);
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
			header("Location: ../temporales/".$datos->get_nombre_elemento().".pdf");
			//var_dump($datos);
		}



		private function obtener_resultado($puntaje){
			$referencias = array(
					array("resultado"=>"No Satisfactorio","min"=>0,"max"=>0.99),
					array("resultado"=>"Satisfactorio con Observaciones","min"=>1,"max"=>1.49),
					array("resultado"=>"Satisfactorio","min"=>1.5,"max"=>3)
				);
			foreach ($referencias as $value) {
				if($puntaje >= $value['min'] && $puntaje <= $value['max']){
					return $value['resultado'];
				}
			}
		}

		private function generar_grafico($acumulador_opcion,$categoria){
			$opciones = array();
			$cantidades = array();
			array_map('unlink', glob("../temporales/*.pdf"));
			
			//borro, si existen, los graficos que puedan estar almacenados en el servidor 
			if(file_exists ( "../temporales/".$categoria.".png" )){
				unlink ("../temporales/".$categoria.".png");
			}

			// Definicion de los datos del gráfico 
			$DataSet = new pData;
			//obtengo un array con las cantidades
			foreach ($acumulador_opcion as $opcion => $cantidad) {
				//para que no se muestre en el grafico las preguntas con valor 0%
				if($cantidad > 1){
					$opciones[] = $opcion;
					$cantidades[] = $cantidad;	
				}
			}
			
			$DataSet->AddPoint($cantidades,"Serie1");
			$DataSet->AddPoint($opciones,"Serie2");

			$DataSet->AddAllSeries();
			$DataSet->SetAbsciseLabelSerie("Serie2");
			// Initialise the graph
			$Test = new pChart(420,250);
			$Test->drawFilledRoundedRectangle(7,7,413,243,5,200,200,200);
			$Test->drawRoundedRectangle(5,5,415,245,5,230,230,230);
			$Test->createColorGradientPalette(195,204,56,223,110,41,5);
			 // Draw the pie chart
			$Test->setFontProperties("pchart/Fonts/tahoma.ttf",8,0,0,0);
			$Test->AntialiasQuality = 0;
			$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),180,130,110,PIE_PERCENTAGE_LABEL,FALSE,50,20,5);
			$Test->drawPieLegend(330,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
			// Write the title
			$Test->setFontProperties("pchart/Fonts/MankSans.ttf",11);
			$Test->drawTitle(10,22,"Resultado de Encuestas",0,0,0);
			$nombre_grafico = "../temporales/".$categoria.".png";
			$Test->Render($nombre_grafico);
			return $nombre_grafico;
		}

		private function calcular_puntaje($acumulador_opcion){
			$resultado = array();

			//obtengo el total de respuestas de esta categoria
			$resultado['general']['total_respuestas'] = 0;
			$resultado['general'] = array("puntaje"=>0,"mb"=>0,"b"=>0,"r"=>0,"m"=>0,"ns"=>0,"total_respuestas"=>0);
			foreach ($acumulador_opcion as $categoria => $opciones) {
				$resultado[$categoria]['total_respuestas'] = 0;
				
				foreach ($opciones as $opcion => $cantidad) {
					$resultado[$categoria]['total_respuestas'] += $cantidad; 
					$resultado['general']['total_respuestas'] += $cantidad;
				}
				$puntos = $acumulador_opcion[$categoria];

				//calculo el puntaje 
				$resultado[$categoria]['mb'] = (array_key_exists('Muy Bueno', $puntos)) ? $puntos['Muy Bueno'] : 0;
				$resultado['general']['mb'] += (array_key_exists('Muy Bueno', $puntos)) ? $puntos['Muy Bueno'] : 0;
				$resultado[$categoria]['b'] = (array_key_exists('Bueno', $puntos)) ? $puntos['Bueno'] : 0;
				$resultado['general']['b'] += (array_key_exists('Bueno', $puntos)) ? $puntos['Bueno'] : 0;
				$resultado[$categoria]['r'] = (array_key_exists('Regular', $puntos)) ? $puntos['Regular'] : 0;
				$resultado['general']['r'] += (array_key_exists('Regular', $puntos)) ? $puntos['Regular'] : 0;
				$resultado[$categoria]['m'] = (array_key_exists('Malo', $puntos)) ? $puntos['Malo'] : 0;
				$resultado['general']['m'] += (array_key_exists('Malo', $puntos)) ? $puntos['Malo'] : 0;
				$resultado[$categoria]['ns'] = (array_key_exists('No sabe', $puntos)) ? $puntos['No sabe'] : 0;
				$resultado['general']['ns'] += (array_key_exists('No sabe', $puntos)) ? $puntos['No sabe'] : 0;

				$resultado[$categoria]['puntaje'] = (($resultado[$categoria]['mb']*3)+($resultado[$categoria]['b']*2)+$resultado[$categoria]['r'])/($resultado[$categoria]['total_respuestas']-$resultado[$categoria]['ns']);
			}
			$resultado['general']['puntaje'] = (($resultado['general']['mb']*3)+($resultado['general']['b']*2)+$resultado['general']['r'])/($resultado['general']['total_respuestas']-$resultado['general']['ns']);
			return $resultado;
		}
	}
?>