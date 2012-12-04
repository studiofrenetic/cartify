<?php
/**
 * --------------------------------------------------------------------------
 * Cartify
 * --------------------------------------------------------------------------
 *
 * Cartify, a shopping cart bundle for use with the Laravel Framework.
 *
 * @package  Cartify
 * @version  2.0.1
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
	 * Empties the wishlist contents.
	 *
	 * @access   public
	 * @return   void
	 */
	public function post_index()
	{
		// Let's make the wishlist empty!
		//
		Cartify::wishlist()->destroy();

		// Redirect back to the wishlist page.
		//
		return Redirect::to('cartify/wishlist')->with('warning', 'Your wishlist was cleared!');
	}

	/**
	 * Adds an item to the wishlist.
	 *
	 * @access   public
	 * @param    string
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

		// Redirect back to the wishlist page.
		//
		return Redirect::to('cartify/wishlist')->with('success', 'The product was added to your wishlist!');
	}

	/**
	 * Removes an item from the wishlist.
	 *
	 * @access   public
	 * @param    string
	 * @return   void
	 */
	public function get_remove($item_id = null)
	{
		// Remove the item from the wishlist.
		//
		Cartify::wishlist()->remove($item_id);

		// Redirect back to the wishlist page.
		//
		return Redirect::to('cartify/wishlist')->with('warning', 'The item was removed from the wishlist.');
	}
}
