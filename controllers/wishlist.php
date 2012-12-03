<?php

## This file is for the examples only!

use Cartify\Models\Products;

class Cartify_Wishlist_Controller extends Controller
{
	// Make it RESTful
	//
	public $restful = true;


   /**
	 * --------------------------------------------------------------------------
	 * Function: get_index()
	 * --------------------------------------------------------------------------
	 *
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
	 * --------------------------------------------------------------------------
	 * Function: get_index()
	 * --------------------------------------------------------------------------
	 *
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
		'id'  => $info['id'],
		'qty' => $qty,
		'price'   => $info['price'],
		'name'=> $info['name'],
		'image'=> $info['image'],
		'options' => $options
		);

die;
		// Add the item to the wishlist.
		//
		Cartify::wishlist()->add($product);

		// Redirect back to the cart home.
		//
		return Redirect::to('cartify/wishlist')->with('success', 'The product was added to your wishlist!');
	}
}
