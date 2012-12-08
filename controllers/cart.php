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
 * The cart main page.
 */
class Cartify_Cart_Controller extends Controller
{
	/**
	 * Flag for whether the controller is RESTful.
	 *
	 * @access   public
	 * @var      boolean
	 */
	public $restful = true;

	/**
	 * Shows the cart contents.
	 *
	 * @access   public
	 * @return   void
	 */
	public function get_index()
	{
		// Get the cart contents.
		//
		$cart_contents = Cartify::cart()->contents();



		// Show the page.
		//
		return View::make('cartify::cart.index')->with('cart_contents', $cart_contents);
	}

	/**
	 * Updates or empties the cart contents.
	 *
	 * @access   public
	 * @return   void
	 */
	public function post_index()
	{
		// If we are updating the quantities.
		//
		if (Input::get('update'))
		{
			try
			{
				// Get the items to be updated.
				//
				$items = array();
				foreach(Input::get('items') as $item_id => $qty)
				{
					$items[] = array(
						'rowid' => $item_id,
						'qty'   => $qty,
						#'options' => array()
					);
				}

				// Update the cart contents.
				//
				Cartify::cart()->update($items);
			}
			catch (Cartify\CartException $e)
			{
				echo 'an error occurred while updating your shopping cart';
				die;
			}

			// Redirect back to the cart home.
			//
			return Redirect::to('cartify/cart')->with('success', 'Your shopping cart was updated.');
		}

		// If we are emptying the cart.
		//
		elseif (Input::get('empty'))
		{
			try
			{
				// Let's make the cart empty!
				//
				Cartify::cart()->destroy();
			}
			catch (CartifyException $e)
			{
				echo 'error occurred';
				die;
			}

			// Redirect back to the cart home.
			//
			return Redirect::to('cartify/cart')->with('success', 'Your shopping cart was cleared!');
		}
	}

	/**
	 * Removes an item from the shopping cart.
	 *
	 * @access   public
	 * @param    string
	 * @return   void
	 */
	public function get_remove($item_id = null)
	{
		try
		{
			// Remove the item from the cart.
			//
			Cartify::cart()->remove($item_id);
		}
		catch (Cartify\CartInvalidItemRowIdException $e)
		{
			// Redirect back to the shopping cart page.
			//
			return Redirect::to('cartify/cart')->with('error', 'Invalid Item Row ID!');
		}
		catch (Cartify\CartItemNotFoundException $e)
		{
			// Redirect back to the shopping cart page.
			//
			return Redirect::to('cartify/cart')->with('error', 'Item was not found in your shopping cart!');
		}

		// Redirect back to the cart page.
		//
		return Redirect::to('cartify/cart')->with('success', 'The item was removed from the shopping cart.');
	}
}
