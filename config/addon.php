<?php
/*
FluxCP Vote For Points
Developed By: JayPee Mateo
Email: mandark022@yahoo.com
*/

return array(
	'FB_API_KEY'			=> '', //Public API ID
	'FB_API_SECRET_KEY'		=> '', // Your facebook Application Secret API ID. This is a private API Key so dont tell anyone	
	'NoCase' 				=> false,
	'UseMD5'				=> false,
	'level'					=> 0,
	'MenuItems' => array(
		'Register Via Facebook' => array(
			'Register' 	=> array('module' => 'facebook','action'=>'create'),
		),
	),

	// Do not touch this.
    'FluxTables' => array(
        'REGISTER' 			  => 'cp_fb_register', //Table for Storing Users that register using Facebook this will be use for the login feature
    ),
)
?>