<?php

/**
 * --------------------------------------------------------------------------
 * Lara Cart
 * --------------------------------------------------------------------------
 * 
 * A Shopping Cart based on the Cart library from CodeIgniter for use with
 * the Laravel Framework.
 *
 * @package  lara-cart
 * @version  1.0
 * @author   Bruno Gaspar <brunofgaspar@live.com.pt>
 * @link     https://github.com/bruno-g/lara-cart
 */


/*
 * --------------------------------------------------------------------------
 * Register the class.
 * --------------------------------------------------------------------------
 */
Autoloader::namespaces(array(
    'LaraCart' => __DIR__ . DS
));


/*
 * --------------------------------------------------------------------------
 * Set the global alias.
 * --------------------------------------------------------------------------
 */
Autoloader::alias('LaraCart\\Cart', 'Cart');


/*
 * --------------------------------------------------------------------------
 * Initialize the Cart.
 * --------------------------------------------------------------------------
 */
Cart::init();