<?php
/**
 * --------------------------------------------------------------------------
 * Cartify
 * --------------------------------------------------------------------------
 *
 * Cartify, a shopping cart bundle for use with the Laravel Framework.
 *
 * @package  Cartify
 * @version  2.0.0
 * @author   Bruno Gaspar <brunofgaspar1@gmail.com>
 * @link     https://github.com/bruno-g/cartify
 */

/**
 * Libraries we can use.
 */
use Cartify\Models\Products;

/**
 * This file is for the examples only!
 */

class Cartify_Home_Controller extends Controller
{
	/**
	 * Flag for whether the controller is RESTful.
	 *
	 * @access   public
	 * @var      boolean
	 */
	public $restful = true;

	/**
	 * Show a list of the products on the page.
	 *
	 * @access   public
	 * @return   void
	 */
	public function get_index()
	{
		// Show the page.
		//
		return View::make('cartify::home')->with('products', Products::get_list());
	}
}
