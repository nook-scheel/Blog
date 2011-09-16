<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	define('CI_VERSION',	'1.7.2');
	
	require(BASEPATH.'codeigniter/Common'.EXT);
	require(BASEPATH.'codeigniter/Compat'.EXT);
	require(APPPATH.'config/constants'.EXT);
	
	set_error_handler('_exception_handler');
	
	if ( ! is_php('5.3'))
	{
		@set_magic_quotes_runtime(0); // Kill magic quotes
	}
	
	$BM =& load_class('Benchmark');
	$BM->mark('total_execution_time_start');
	$BM->mark('loading_time_base_classes_start');
	$EXT =& load_class('Hooks');
	$EXT->_call_hook('pre_system');
	$CFG =& load_class('Config');
	$URI =& load_class('URI');
	$RTR =& load_class('Router');
	$OUT =& load_class('Output');
	
	if ($EXT->_call_hook('cache_override') === FALSE)
	{
		if ($OUT->_display_cache($CFG, $URI) == TRUE)
		{
			exit;
		}
	}
	
	$IN		=& load_class('Input');
	$LANG	=& load_class('Language');
	
	if ( ! is_php('5.0.0'))
	{
		load_class('Loader', FALSE);
		require(BASEPATH.'codeigniter/Base4'.EXT);
	}
	else
	{
		require(BASEPATH.'codeigniter/Base5'.EXT);
	}
	
	load_class('Controller', FALSE);
	
	if ( ! file_exists(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT))
	{
		show_error('Unable to load your default controller.  Please make sure the controller specified in your Routes.php file is valid.');
	}
	
	include(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT);
	
	$BM->mark('loading_time_base_classes_end');
	
	$class  = $RTR->fetch_class();
	$method = $RTR->fetch_method();
	
	if 	( ! class_exists($class)
			OR $method == 'controller'
			OR strncmp($method, '_', 1) == 0
			OR in_array(strtolower($method), array_map('strtolower', get_class_methods('Controller')))
		)
	{
		show_404("{$class}/{$method}");
	}
	
	$EXT->_call_hook('pre_controller');
	$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');
	
	$CI = new $class();
	
	if ($RTR->scaffolding_request === TRUE)
	{
		if ($EXT->_call_hook('scaffolding_override') === FALSE)
		{
			$CI->_ci_scaffolding();
		}
	}
	else
	{
		$EXT->_call_hook('post_controller_constructor');
		if (method_exists($CI, '_remap'))
		{
			$CI->_remap($method);
		}
		else
		{
			if ( ! in_array(strtolower($method), array_map('strtolower', get_class_methods($CI))))
			{
				show_404("{$class}/{$method}");
			}
			call_user_func_array(array(&$CI, $method), array_slice($URI->rsegments, 2));
		}
	}
	
	$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_end');
	
	$EXT->_call_hook('post_controller');
	
	if ($EXT->_call_hook('display_override') === FALSE)
	{
		$OUT->_display();
	}
	
	$EXT->_call_hook('post_system');
	
	if (class_exists('CI_DB') AND isset($CI->db))
	{
		$CI->db->close();
	}