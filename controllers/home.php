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
 * The products page.
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
		// Get the products.
		//
		$products = Products::all();

		// Show the page.
		//
		return View::make('cartify::home')->with('products', $products);
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
		$products = Products::all();

		// Retrieve some data.
		//
		$item_id = Input::get('item_id');
		$qty     = Input::get('qty');
		$options = Input::get('options', array());

		// Get the product information.
		//
		$info = $products[ $item_id ];

		// Populate a proper item array.
		//
		$item = array(
			'id'      => $info['id'],
			'qty'     => $qty,
			'price'   => $info['price'],
			'name'    => $info['name'],
			'image'   => $info['image'],
			'options' => $options
		);

		// Do we want to add the item to the wishlist?
		//
		if ($action === 'add_to_wishlist')
		{
			try
			{
				// Add the item to the wishlist.
				//
				Cartify::wishlist()->insert($item);
			}
/*
			// Check if we have invalid data passed.
			//
			catch (Cartify\CartInvalidDataException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid data passed.');
			}

			// Check if we a required index is missing.
			//
			catch (Cartify\CartRequiredIndexException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', $e->getMessage());
			}

			// Check if the quantity is invalid.
			//
			catch (Cartify\CartInvalidItemQuantityException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid item quantity.'');
			}

			// Check if the item row id is invalid.
			//
			catch (Cartify\CartInvalidItemRowIdException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid item row id.');
			}

			// Check if the item name is invalid.
			//
			catch (Cartify\InvalidItemNameException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid item name.');
			}

			// Check if the item price is invalid.
			//
			catch (Cartify\CartInvalidItemPriceException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid item price.');
			}
*/
			// Maybe we want to catch all the errors? Sure.
			//
			catch (Cartify\CartException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'An unexpected error occurred!');
			}

			// Redirect to the wishlist page.
			//
			return Redirect::to('cartify/wishlist')->with('success', 'The item was added to your wishlist!');
		}

		// Do we want to add the item to the shopping cart?
		//
		elseif ($action === 'add_to_cart')
		{
			try
			{
				// Add the item to the shopping cart.
				//
				Cartify::cart()->insert($item);
			}
/*
			// Check if we have invalid data passed.
			//
			catch (Cartify\CartInvalidDataException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid data passed.');
			}

			// Check if we a required index is missing.
			//
			catch (Cartify\CartRequiredIndexException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', $e->getMessage());
			}

			// Check if the quantity is invalid.
			//
			catch (Cartify\CartInvalidItemQuantityException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid item quantity.'');
			}

			// Check if the item row id is invalid.
			//
			catch (Cartify\CartInvalidItemRowIdException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid item row id.');
			}

			// Check if the item name is invalid.
			//
			catch (Cartify\InvalidItemNameException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid item name.');
			}

			// Check if the item price is invalid.
			//
			catch (Cartify\CartInvalidItemPriceException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'Invalid item price.');
			}
*/
			// Maybe we want to catch all the errors? Sure.
			//
			catch (Cartify\CartException $e)
			{
				// Redirect back to the home page.
				//
				return Redirect::to('cartify')->with('error', 'An unexpected error occurred!');
			}

			// Redirect to the cart page.
			//
			return Redirect::to('cartify/cart')->with('success', 'The item was added to your shopping cart!');
		}

		// Invalid action, redirect to the home page.
		//
		return Redirect::to('cartify');
	}
}
