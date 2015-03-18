<?php

$plulz_template_directory =   get_template_directory_uri();
$plulz_server_directory   =   get_template_directory() . '/';
//$plulz_server_directory =   plugin_dir_path(__FILE__);
//$plulz_template_directory =   WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
require( $plulz_server_directory .'plulz/lib/Model/PlulzImport.php' );
//
new PlulzImport($plulz_server_directory);

// Start Front Controller
new PazzaniBrindesFrontController();