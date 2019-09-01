<?php
error_reporting(E_ALL);

// speed things up with gzip plus ob_start() is required for csv export
if(!ob_start('ob_gzhandler'))
	ob_start();

header('Content-Type: text/html; charset=utf-8');

include('lazy_mofo.php');

echo "
<!DOCTYPE html>
<html>
<head>
	<meta charset='UTF-8'>
	<link rel='stylesheet' type='text/css' href='style.css'>
    <meta name='robots' content='noindex,nofollow'>
</head>
<body>
"; 


// enter your database host, name, username, and password
$db_host = 'pdb9.awardspace.net';
$db_name = '2470728_core';
$db_user = '2470728_core';
$db_pass = 'Aa123456%';


// connect with pdo 
try {
	$dbh = new PDO("mysql:host=$db_host;dbname=$db_name;", $db_user, $db_pass);
}
catch(PDOException $e) {
	die('pdo connection error: ' . $e->getMessage());
}

// create LM object, pass in PDO connection, see i18n folder for country + language options 
$lm = new lazy_mofo($dbh, 'en-us'); 


// table name for updates, inserts and deletes
$lm->table = 'mbienes';


// identity / primary key for table
$lm->identity_name = 'nId';


// new in version >= 2015-02-27 all searches have to be done manually, added in where clause of grid_sql
$lm->grid_show_search_box = true;


// optional, query for grid().
// ** IMPORTANT - last column must be the identity/key for [edit] and [delete] links to appear **
$lm->grid_sql = "
select 
  m.nId Id
, rtrim(m.cTipo) Tipo
, m.cMedio Medio
, m.cSectorEco Sector
, m.cImportancia Importancia
, m.cDetalle Detalle
, m.nId 
from  mbienes m 
where coalesce(m.cDireccion, '') like :_search 
or    coalesce(m.cDistrito, '') like :_search 
or    coalesce(m.cDetalle, '') like :_search 
order by m.nId desc
";
$lm->grid_sql_param[':_search'] = '%' . trim(@$_REQUEST['_search']) . '%';


// optional, define what is displayed on edit form. identity id must be passed in also.  
$lm->form_sql = "
select 
 nId
,cTipo
,cMedio
,cSectorEco
,cImportancia
,cDetalle
from  mbienes 
where nId = :nId
";
$lm->form_sql_param[":$lm->identity_name"] = @$_REQUEST[$lm->identity_name]; 


// copy validation rules, same rules when updating
$lm->on_update_validate = $lm->on_insert_validate;  


// run the controller
$lm->run();


echo "</body></html>";


