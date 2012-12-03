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

class Cartify_Wishlist_Controller extends Controller
{
	/**
	 * Flag for whether the controller is RESTful.
	 *
	 * @access   public
	 * @var      boolean
	 */
	public $restful = true;

	/**
	 * Show a list of the products on the wishlist.
	 *
	 * @access   public
	 * @return   void
	 */
	public function get_index()
	{
		// Show the page.
		//
		return View::make('cartify::wishlist');
	}

	/**
	 * Adds an item to the wishlist.
	 *
	 * @access   public
	 * @paramstring
	 * @return   void
	 */
	public function get_add($item_id, $qty = 1)
	{
		$products = Products::get_list();

		// Get the product information.
		//
		$info = $products[ $item_id ];

		// Add the qty to the product information.
		//
		$product = array(
			'id'      => $info['id'],
			'qty'     => $qty,
			'price'   => $info['price'],
			'name'    => $info['name'],
			'image'   => $info['image']
		);

		// Add the item to the wishlist.
		//
		Cartify::wishlist()->add($product);

		// Redirect back to the cart home.
		//
		return Redirect::to('cartify/wishlist')->with('success', 'The product was added to your wishlist!');
	}
}
