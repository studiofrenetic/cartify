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
		// Show the page.
		//
		return View::make('cartify::cart.index');
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
			// Get the items to be updated.
			//
			$items = array();
			foreach(Input::get('items') as $item_id => $qty)
			{
				$items[] = array(
					'rowid' => $item_id,
					'qty'   => $qty,
					'options' => array()
				);
			}

			// Update the cart contents.
			//
			Cartify::cart()->update($items);

			// Redirect back to the cart home.
			//
			return Redirect::to('cartify/cart')->with('success', 'Your shopping cart was updated.');
		}

		// If we are emptying the cart.
		//
		elseif (Input::get('empty'))
		{
			// Let's make the cart empty!
			//
			Cartify::cart()->destroy();

			// Redirect back to the cart home.
			//
			return Redirect::to('cartify/cart')->with('warning', 'Your shopping cart was cleared!');
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
		// Remove the item from the cart.
		//
		Cartify::cart()->remove($item_id);

		// Redirect back to the cart page.
		//
		return Redirect::to('cartify/cart')->with('warning', 'The item was removed from the shopping cart.');
	}
}
