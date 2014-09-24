<?php

$enlace = mysql_connect('localhost', 'root', 'tatateta');
mysql_select_db('puente_updates');
$query = "DELETE FROM listado";
$resultado = mysql_query($query);
if (!$resultado) {
    die('Consulta no válida: ' . mysql_error());
} else {
    echo "Vaciada la tabla ";
}