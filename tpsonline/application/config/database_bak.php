<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'smartcargo';
$active_record = TRUE;

$db_config_base = array(
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'autoinit' => TRUE,
	'stricton' => FALSE
);

$db_coll = array(
	'smartcargo' => array(
		'hostname' => '10.8.0.33', //'localhost',10.8.0.33
		'username' => 'dashboard', //'root',dashboard
		'password' => 'c4rg0sm4rt!', //'',c4rg0sm4rt!
		'database' => 'smartcargo_demo',
		'dbdriver' => 'mysqli',
		'dbprefix' => ''
	),
	'ilcs_master_reference' => array(
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'ilcs_master_reference',
		'dbdriver' => 'mysqli',
		'dbprefix' => ''
	),
	'ilcs_manifest' => array(
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'ilcs_manifest',
		'dbdriver' => 'mysqli',
		'dbprefix' => ''
	),
	'ilcs_tps_online' => array(
		'hostname' => '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=10.8.3.181)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=oradb)))',
		'username' => 'CTOS',
		'password' => 'CTOS4ILCS!',
		'database' => 'CTOS',
		'dbdriver' => 'oci8',
		'dbprefix' => ''
	),
	'ilcs_cartos' => array(
		'hostname' => '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=10.8.3.181)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=oradb)))',
		'username' => 'CTOS',
		'password' => 'CTOS4ILCS!',
		'database' => 'CTOS',
		'dbdriver' => 'oci8',
		'dbprefix' => ''
	),
	'ilcs_dashboard' => array(
		'hostname' => '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=10.10.32.60)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=IPCDB)))',
		'username' => 'CTOS',
		'password' => 'Ct054ilcs!',
		'database' => 'CTOS',
		'dbdriver' => 'oci8',
		'dbprefix' => ''
	),
	'ilcs_log_autogate' => array(
		'hostname' => '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=10.8.3.181)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=oradb)))',
		'username' => 'mta',
		'password' => 'mtajug4',
		'database' => 'oradb',
		'dbdriver' => 'oci8',
		'dbprefix' => ''
	),
	'smartcargo_prod' => array(
		'hostname' => '10.8.0.33',
		'username' => 'dashboard',
		'password' => 'c4rg0sm4rt!',
		'database' => 'smartcargo',
		'dbdriver' => 'mysqli',
		'dbprefix' => ''
	),
);

$db = array(
	'smartcargo' 			=> array_merge($db_config_base, $db_coll['smartcargo_prod']),
	'ilcs_master_reference' => array_merge($db_config_base, $db_coll['ilcs_master_reference']),
	'ilcs_manifest' 		=> array_merge($db_config_base, $db_coll['ilcs_manifest']),
	'ilcs_tps_online' 		=> array_merge($db_config_base, $db_coll['ilcs_tps_online']),
	'ilcs_cartos' 			=> array_merge($db_config_base, $db_coll['ilcs_cartos']),
	'ilcs_dashboard'		=> array_merge($db_config_base, $db_coll['ilcs_dashboard']),
	'ilcs_log_autogate'		=> array_merge($db_config_base, $db_coll['ilcs_log_autogate']),
);																		   

/* End of file database.php */
/* Location: ./application/config/database.php */
