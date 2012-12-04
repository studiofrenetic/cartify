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
	 * @return   View
	 */
	public function get_index()
	{
		// Show the page.
		//
		return View::make('cartify::home')->with('products', Products::get_list());
	}

	/**
	 * Adds a product to the shopping cart or to the wishlist.
	 *
	 * @access   public
	 * @return   Redirect
	 */
	public function post_index()
	{
		// Get the action, this basically tells what button was pressed.
		//
		$action = Input::get('action');

		// Get the static list of products.
		//
		$products = Products::get_list();

		// Retrieve some data.
		//
		$item_id = Input::get('item_id');
		$qty     = Input::get('qty');
		$options = Input::get('options', array());

		// Get the product information.
		//
		$info = $products[ $item_id ];

		// Populate a proper product array.
		//
		$product = array(
			array(
				'id'      => $info['id'],
				'qty'     => $qty,
				'price'   => $info['price'],
				'name'    => $info['name'],
				'image'   => $info['image'],
				'options' => $options
			),
			array(
				'idx'      => 'xptop',
				'qty'     => $qty,
				'price'   => $info['price'],
				'name'    => $info['name'],
				'image'   => $info['image'],
				'options' => $options
			)
		);

		$product = array(
			'id'      => $info['id'],
			'qty'     => $qty,
			'price'   => $info['price'],
			'name'    => $info['name'],
			'image'   => $info['image'],
			'options' => $options
		);

		// Do we want to add the product to the wishlist?
		//
		if ($action === 'add_to_wishlist')
		{
			// Add the item to the wishlist.
			//
			try
			{
				Cartify::wishlist()->insert($product);
			}
			catch (Exception $e)
			{
				return Redirect::to('cartify')->with('error', $e->getMessage());
			}

			// Redirect to the wishlist page.
			//
			return Redirect::to('cartify/wishlist')->with('success', 'The product was added to your wishlist!');
		}

		// Do we want to add the product to the shopping cart?
		//
		elseif ($action === 'add_to_cart')
		{
			// Add the item to the shopping cart.
			//
			try
			{
				Cartify::cart()->insert($product);
			}
			catch (Exception $e)
			{
				return Redirect::to('cartify')->with('error', $e->getMessage());
			}

			// Redirect to the cart page.
			//
			return Redirect::to('cartify/cart')->with('success', 'The product was added to your shopping cart!');
		}

		// Invalid action, redirect to the home page.
		//
		return Redirect::to('cartify');
	}
}
