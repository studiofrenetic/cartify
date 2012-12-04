<?php
/**
 * --------------------------------------------------------------------------
 * Cartify
 * --------------------------------------------------------------------------
 *
 * Cartify, a shopping cart bundle for use with the Laravel Framework.
 *
 * @package  Cartify
 * @version  2.1.0
 * @author   Bruno Gaspar <brunofgaspar1@gmail.com>
 * @link     https://github.com/bruno-g/cartify
 */

/*
 * --------------------------------------------------------------------------
 * Register some namespaces.
 * --------------------------------------------------------------------------
 */
Autoloader::namespaces(array(
	'Cartify\\Libraries' => __DIR__ . DS . 'libraries',
	'Cartify\\Models'    => __DIR__ . DS . 'models',
	'Cartify'            => __DIR__ . DS
));

/*
 * --------------------------------------------------------------------------
 * Set the global alias.
 * --------------------------------------------------------------------------
 */
Autoloader::alias('Cartify\\Cartify', 'Cartify');

/*
 * --------------------------------------------------------------------------
 * Include our helpers file.
 * --------------------------------------------------------------------------
 */
require_once __DIR__ . DS . 'helpers.php';
