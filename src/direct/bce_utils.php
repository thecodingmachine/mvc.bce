<?php

use Mouf\MVC\BCE\Services\BCEUtils;
session_start();
if (function_exists('apache_getenv')) {
	define('ROOT_URL', apache_getenv("BASE")."/../../../");
}
require_once '../../../../../mouf/Mouf.php';

$query = $_GET['q'];
$inputName = $_GET['n'];

$utils = new BCEUtils();

//depending on the $query parameter, return instance or dao data
switch ($query) {
	case 'daoData':
		echo json_encode($utils->getDaoDataFromInstance($inputName));
	break;
	
	case 'instanceData':
		echo json_encode($utils->getInstanceData($inputName));
	break;
	
	case 'formMainDaoData':
		echo json_encode($utils->getDaoDataFromForm($inputName));
	break;
}