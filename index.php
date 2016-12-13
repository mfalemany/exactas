<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Procesador de Encuestas</title>
	
	<link rel="stylesheet" type="text/css" href="./assets/css/estilos.css" />
	<link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.min.css">
	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="container">
	<div class="row">
		<div class="col-xs-12">
			<h4 id="encabezado">GENERAR REPORTE DE ENCUESTAS</h4>	
		</div>	
	</div>
	
	<form method="POST" action="librerias/procesadorResultados.php" enctype="multipart/form-data" id="formulario" target="_BLANK">
		<!-- ############################ DETALLES DE LA ENCUESTA ########################### -->
		<div class="row">
			<div class="col-xs-12" id="detalles_encuesta">
				<h4>Detalles de la Encuesta</h4>
			</div>	
			<div class="col-xs-12">
				<p class="help-block">Indique a continuaci&oacute;n los detalles que figurarán en el encabezado del reporte.</p>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label for="asignatura" class="sr-only">Asignatura:</label>
					<input type="text" class="form-control" id="asignatura" name="asignatura" placeholder="Asignatura">
				</div>
			</div>
			<div class="col-xs-3">
				<div class="form-group">
					<label for="anio_cursado" class="sr-only">Año de Cursado:</label>
					<input type="text" class="form-control" id="anio_cursado" name="anio_cursado" placeholder="Año de Cursado" value=<?php echo date("Y"); ?>>
				</div>
			</div>
			<div class="col-xs-3">
				<div class="form-group">
					<label for="cuatrimestre" class="sr-only">Cuatrimestre:</label>
					<input type="text" class="form-control" id="cuatrimestre" name="cuatrimestre" placeholder="Cuatrimestre">
				</div>
			</div>
			
			
		</div>			
		<!-- ########################################################################################## -->	

	
		<!-- ############################ RESULTADOS DE ENCUESTAS A DOCENTE ########################### -->
		<div class="row">
			<div class="col-xs-12" id="datos_docente">
				<h4>Resultados del Docente</h4>
				<div class="form-group" id="archivos_docente">
					<p class="help-block">Seleccione los archivos, exportados del SIU-Kolla, que contengan los resultados de encuestas al docente.</p>
					<!-- ############ SE REPITE POR CADA ARCHIVO ###################-->
					
					<div class="form-inline">
						<span class="num_file">1</span><input type="file" name="file_doc_1" id="file_doc_1" class="form-control btn btn-xs"  />
					</div>
					<!-- ###########################################################-->	
				</div>
			</div>
		</div>	
		<div class="row">
			<div class="col-xs-12">
				<input type="button" class="btn btn-success btn-xs" value="Agregar resultado docente" id="add_arch_doc">
			</div>
		</div>
		<!-- ########################################################################################## -->	
		

		<div class="row">
			<div class="col-xs-12 checkbox">
				<input type="checkbox" id="incluye_catedra"> Incluir resultados de c&aacute;tedra.
			</div>
		</div>	

		<!-- ############################ RESULTADOS DE ENCUESTAS A CÁTEDRA ########################### -->
		<div id="contenedor_catedra">
			<div class="row">
				
				<div class="col-xs-12" id="datos_catedra">
					<h4>Resultados de la C&aacute;tedra</h4>
					<div class="form-group" id="archivos_catedra">
						<p class="help-block">Seleccione los archivos, exportados del SIU-Kolla, que contengan los resultados de encuestas a la c&aacute;tedra.</p>
						<!-- ############ SE REPITE POR CADA ARCHIVO ###################-->
						<div class="form-inline">
							<span class="num_file">1</span><input type="file" name="file_cat_1" id="file_cat_1" class="form-control btn btn-xs"  />
						</div>
						<!-- ###########################################################-->	
					</div>
				</div>
			</div>	
			<div class="row">
				<div class="col-xs-12">
					<input type="button" class="btn btn-success btn-xs" value="Agregar resultado c&aacute;tedra" id="add_arch_cat">
				</div>
			</div>
		</div>



		<!-- ########################################################################################## -->	







		<div class="row">
			<div class="col-xs-8 col-xs-offset-2">
				<div class="form-group">
					<input type="submit" value="Procesar" id="btn_procesar" class="form-control btn btn-primary" />
				</div>
			</div>
		</div>
		</form>	
	</div>
	
	<div id="contenedor_error">
		No se ha seleccionado un archivo de resultados para procesar
	</div>

	<!-- Script de jQuery -->
	<script type="text/javascript" src="./assets/js/jquery.min.js"></script>
	<!-- Script de la aplicacion -->
	<script type="text/javascript" src="./assets/js/script.js"></script>
	<!-- Script de Bootstrap -->
	<script type="text/javascript" src="./assets/js/bootstrap.min.js"></script>
	
</body>
</html>