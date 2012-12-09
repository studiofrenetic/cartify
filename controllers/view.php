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


#############################################################
############ This file is for the examples only! ############
#############################################################


/**
 * Libraries we can use.
 */
use Cartify\Models\Products;

/**
 * The product information page.
 */
class Cartify_View_Controller extends Controller
{
	/**
	 * Flag for whether the controller is RESTful.
	 *
	 * @access   public
	 * @var      boolean
	 */
	public $restful = true;

	/**
	 * Shows the product information page.
	 *
	 * @access   public
	 * @param    string
	 * @return   Redirect
	 */
	public function get_index($product_slug = null)
	{
		// Check if the product exists.
		//
		if ( ! $product = Products::find($product_slug))
		{
			// Redirect back to the home page.
			//
			return Redirect::to('cartify')->with('error', 'The product does not exist!');
		}

		// Show the page.
		//
		return View::make('cartify::view')->with('product', $product);
	}

	/**
	 *
	 *
	 * @access   public
	 * @return   Redirect
	 */
	public function post_index()
	{

	}
}
