<?php

die('teste');

$plulz_template_directory =   get_template_directory_uri();
$plulz_server_directory   =   get_template_directory() . '/';
//$plulz_server_directory =   plugin_dir_path(__FILE__);
//$plulz_template_directory =   WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
require( $plulz_server_directory .'plulz/lib/Model/PlulzImport.php' );
//
new PlulzImport($plulz_server_directory);

// fix autoload

// lib model
//require($plulz_server_directory . 'plulz/lib/Model/PlulzSession.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzObjectAbstract.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzFacebookAbstract.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzUserAbstract.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzAdmin.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzBaseWidget.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzImport.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzNotices.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzPost.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzTaxonomy.php');
//require($plulz_server_directory . 'plulz/lib/Model/PlulzTools.php');
//
//// lib controller
//require($plulz_server_directory . 'plulz/lib/Controller/PlulzControllerAbstract.php');
//require($plulz_server_directory . 'plulz/lib/Controller/PlulzAdminControllerAbstract.php');
//require($plulz_server_directory . 'plulz/lib/Controller/PlulzFrontControllerAbstract.php');
//require($plulz_server_directory . 'plulz/lib/Controller/PlulzPostControllerAbstract.php');
//
//// app model
//require($plulz_server_directory . 'plulz/app/Model/PazzaniBrindesAdmin.php');
//require($plulz_server_directory . 'plulz/app/Model/PazzaniBrindesCarrinho.php');
//require($plulz_server_directory . 'plulz/app/Model/PazzaniBrindesContato.php');
//require($plulz_server_directory . 'plulz/app/Model/PazzaniBrindesFacebook.php');
//require($plulz_server_directory . 'plulz/app/Model/PazzaniBrindesMetas.php');
//require($plulz_server_directory . 'plulz/app/Model/PazzaniBrindesOrcamento.php');
//require($plulz_server_directory . 'plulz/app/Model/PazzaniBrindesProduto.php');
//require($plulz_server_directory . 'plulz/app/Model/PazzaniBrindesUser.php');
//
//// app controller
//require($plulz_server_directory . 'plulz/app/Controller/PazzaniBrindesAdminController.php');
//require($plulz_server_directory . 'plulz/app/Controller/PazzaniBrindesFacebookController.php');
//require($plulz_server_directory . 'plulz/app/Controller/PazzaniBrindesFrontController.php');
//require($plulz_server_directory . 'plulz/app/Controller/PazzaniBrindesOrcamentoController.php');
//require($plulz_server_directory . 'plulz/app/Controller/PazzaniBrindesProdutoController.php');
//require($plulz_server_directory . 'plulz/app/Controller/PazzaniBrindesUserController.php');

// Start Front Controller
new PazzaniBrindesFrontController();