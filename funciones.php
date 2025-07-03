<?php

// Solo declarar la función si no existe
if (!function_exists('baseDatos')) {
    function baseDatos($consulta)
    {
        // Conexión
        $conexion = mysqli_connect("localhost", "root", "");
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        // Selección de base de datos
        if (!mysqli_select_db($conexion, "inventario")) {
            die("Error al seleccionar la base de datos: " . mysqli_error($conexion));
        }

        // Ejecutar consulta
        $resultado = mysqli_query($conexion, $consulta);
        if (!$resultado) {
            die("Error en la consulta: " . mysqli_error($conexion));
        }

        // Cerrar conexión
        mysqli_close($conexion);

        // Devolver resultado
        return $resultado;
    }
}

// Función para sanitizar entradas (ayuda a prevenir ataques XSS)
if (!function_exists('sanitizar')) {
    function sanitizar($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = sanitizar($value);
            }
        } else {
            $input = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
        return $input;
    }
}

// Función para evitar inyección SQL básica
if (!function_exists('escapar')) {
    function escapar($input) {
        $conexion = mysqli_connect("localhost", "root", "");
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        if (!mysqli_select_db($conexion, "inventario")) {
            die("Error al seleccionar la base de datos: " . mysqli_error($conexion));
        }

        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = escapar($value);
            }
        } else {
            $input = mysqli_real_escape_string($conexion, $input);
        }

        mysqli_close($conexion);
        return $input;
    }
}
?>


<!--
ss
function buscarmar()
{
	$nombre = $_POST["nombre"];
	$consulta = "select * from productos where nombre LIKE '$nombre%' order by nombre";
	$resultado = baseDatos($consulta);
	$n = mysqli_num_rows($resultado);
	if ($n >= 1) {
		for ($i = 0; $i < $n; $i++) {
			mysqli_data_seek($resultado, $i);
			$fila = mysqli_fetch_array($resultado);
			$nombre = $_POST["nombre"];
			$descrip = $_POST["descrip"];
			$stock = $_POST["stock"];
			$lugar = $_POST["lugar"];
			$categoria = $_POST["categoria"];
			$imagen = $_POST["imagen"];
			$estado = $_POST["estado"];
			$imagen = $_FILES["imagen"]["name"];
			echo "
										<div class='producto_amp'>
											<div class='foto_amp'>
												<a href='img/grandes/$imagen' title='Ampliar' target='blank'>
													<img src='img/grandes/$imagen' alt='foto'>
												</a>
											</div>
											<div class='texto_amp'>
												
												<p>Nombre: $nombre</p>
												<p>Desc.: $descrip</p>
												<p>Stock: $stock</p>
												<p>Lugar: $lugar</p>
												<p>Categoria: $categoria</p>
												<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
											</div>
										</div>
									";
		}
	} else {
		echo "
									<div class='producto_amp'>
										<div class='foto_amp'>
											<img src='img/triste.webp' alt='foto'>
										</div>
										<div class='texto_amp'>
											
											<p>Error 404</p>
											<p>No se encontró lo que buscaba</p>
											<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
										</div>
									</div>
								";
	}
}
?>
-->


<!--
function buscarcod(){
						$codigo=$_POST["codigo"];
						$consulta="select * from productos where codigo=$codigo";
						$resultado=baseDatos($consulta);
						$n=mysqli_num_rows($resultado);
						if($n==1)
						{	
							mysqli_data_seek($resultado, 0);
							$fila=mysqli_fetch_array($resultado);
							$codigo=$fila["codigo"];
							$descrip=$fila["descrip"];
							$stock=$fila["stock"];
							$precio=$fila["precio"];
							$foto=$fila["foto"];						
							echo"
									<div class='producto_amp'>
										<div class='foto_amp'>
											<a href='img/grandes/$foto' title='Ampliar' target='blank'>
												<img src='img/grandes/$foto' alt='foto'>
											</a>
										</div>
										<div class='texto_amp'>
											
											<p>Desc.: $descrip</p>
											<p>Stock: $stock</p>
											<p>Precio.: $$precio</p>
											<p>Cod.: $codigo</p>
											<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
										</div>
									</div>
								";
						}
						else
						{
							echo"
									<div class='producto_amp'>
										<div class='foto_amp'>
											<img src='img/triste.webp' alt='foto'>
										</div>
										<div class='texto_amp'>
											
											<p>Error 404</p>
											<p>Su producto no se encontró</p>
											<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
										</div>
									</div>
								";
						}
}
function buscardes(){
						$descrip=$_POST["descrip"];
						$consulta="select * from productos where descrip like'$descrip%' order by descrip";
						$resultado=baseDatos($consulta);
						$n=mysqli_num_rows($resultado);
						if($n>=1)
						{	
							for($i=0;$i<$n;$i++)
							{
								mysqli_data_seek($resultado, $i);
								$fila=mysqli_fetch_array($resultado);
								$codigo=$fila["codigo"];
								$descrip=$fila["descrip"];
								$stock=$fila["stock"];
								$precio=$fila["precio"];
								$foto=$fila["foto"];						
								echo"
										<div class='producto_amp'>
											<div class='foto_amp'>
												<a href='img/grandes/$foto' title='Ampliar' target='blank'>
													<img src='img/grandes/$foto' alt='foto'>
												</a>
											</div>
											<div class='texto_amp'>
												
												<p>Desc.: $descrip</p>
												<p>Stock: $stock</p>
												<p>Precio.: $$precio</p>
												<p>Cod.: $codigo</p>
												<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
											</div>
										</div>
									";
							}
						}
						else
						{
							echo"
									<div class='producto_amp'>
										<div class='foto_amp'>
											<img src='img/triste.webp' alt='foto'>
										</div>
										<div class='texto_amp'>
											
											<p>Error 404</p>
											<p>Su producto no se encontró</p>
											<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
										</div>
									</div>
								";
						}
}
function buscarmar(){
						$marca=$_POST["marca"];
						$consulta="select * from productos where marca='$marca' order by descrip";
						$resultado=baseDatos($consulta);
						$n=mysqli_num_rows($resultado);
						if($n>=1)
						{	
							for($i=0;$i<$n;$i++)
							{
								mysqli_data_seek($resultado, $i);
								$fila=mysqli_fetch_array($resultado);
								$codigo=$fila["codigo"];
								$descrip=$fila["descrip"];
								$stock=$fila["stock"];
								$precio=$fila["precio"];
								$foto=$fila["foto"];						
								echo"
										<div class='producto_amp'>
											<div class='foto_amp'>
												<a href='img/grandes/$foto' title='Ampliar' target='blank'>
													<img src='img/grandes/$foto' alt='foto'>
												</a>
											</div>
											<div class='texto_amp'>
												
												<p>Desc.: $descrip</p>
												<p>Stock: $stock</p>
												<p>Precio.: $$precio</p>
												<p>Cod.: $codigo</p>
												<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
											</div>
										</div>
									";
							}
						}
						else
						{
							echo"
									<div class='producto_amp'>
										<div class='foto_amp'>
											<img src='img/triste.webp' alt='foto'>
										</div>
										<div class='texto_amp'>
											
											<p>Error 404</p>
											<p>Su producto no se encontró</p>
											<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
										</div>
									</div>
								";
						}
}
function buscarpre(){
						$preciomin=$_POST["preciomin"];
						$preciomax=$_POST["preciomax"];
						$consulta="select * from productos where precio>='$preciomin' && precio<='$preciomax' order by descrip";
						$resultado=baseDatos($consulta);
						$n=mysqli_num_rows($resultado);
						if($n>=1)
						{	
							for($i=0;$i<$n;$i++)
							{
								mysqli_data_seek($resultado, $i);
								$fila=mysqli_fetch_array($resultado);
								$codigo=$fila["codigo"];
								$descrip=$fila["descrip"];
								$stock=$fila["stock"];
								$precio=$fila["precio"];
								$foto=$fila["foto"];						
								echo"
										<div class='producto_amp'>
											<div class='foto_amp'>
												<a href='img/grandes/$foto' title='Ampliar' target='blank'>
													<img src='img/grandes/$foto' alt='foto'>
												</a>
											</div>
											<div class='texto_amp'>
												
												<p>Desc.: $descrip</p>
												<p>Stock: $stock</p>
												<p>Precio.: $$precio</p>
												<p>Cod.: $codigo</p>
												<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
											</div>
										</div>
									";
							}
						}
						else
						{
							echo"
									<div class='producto_amp'>
										<div class='foto_amp'>
											<img src='img/triste.webp' alt='foto'>
										</div>
										<div class='texto_amp'>
											
											<p>Error 404</p>
											<p>Su producto no se encontró</p>
											<p><a href='multi_busc.php' title='borrar'>
												Borrar
												</a></p>
										</div>
									</div>
								";
						}
}-->