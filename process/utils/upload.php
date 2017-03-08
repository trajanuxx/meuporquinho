<?php
require_once $_SERVER[DOCUMENT_ROOT].'config.php';
require_once $_SERVER[DOCUMENT_ROOT].'process/data/mysql.php';
require_once $_SERVER[DOCUMENT_ROOT].'process/data/geral.php';

if (!isset($_SESSION)) {
  session_start();
}


date_default_timezone_set("America/Sao_Paulo");
$generico = new Geral();

$nome_final = $_SESSION["seq_usuario"].'_'.strtolower(str_replace(' ', '', date('dmYHis') . $_FILES['file']['name']));

$arquivo = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($nome_final)));


move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER[DOCUMENT_ROOT].'/arquivos/' . $arquivo);


?>
