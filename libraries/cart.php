<?php
/**
 * --------------------------------------------------------------------------
 * Cartify
 * --------------------------------------------------------------------------
 *
 * Cartify, a shopping cart bundle for use with the Laravel Framework.
 *
 * @package  Cartify
 * @version  1.0.0
 * @author   Bruno Gaspar <brunofgaspar1@gmail.com>
 * @link     https://github.com/bruno-g/cartify
 */

namespace Cartify\Libraries;

/**
 * Libraries we can use.
 */
use Laravel\Config;
use Laravel\Session;

/**
 *
 */
class Cart
{
	/**
	 * Regular expression to validate product ID's.
	 *
	 *  Allowed:
	 *		alpha-numeric
	 *		dashes
	 *		underscores
	 *		periods
	 *
	 * @access   protected
	 * @var      string
	 */
	protected $product_id_rules = '\.a-z0-9_-';

	/**
	 * Regular expression to validate product Names.
	 *
	 *  Allowed:
	 *		alpha-numeric
	 *		dashes
	 *		underscores
	 *		colons
	 *		periods
	 *
	 * @access   protected
	 * @var      string
	 */
	protected $product_name_rules = '\.\:\-_ a-z0-9';

	/**
	 * Shopping Cart config.
	 *
	 * @access   protected
	 * @var      array
	 */
	protected $config = array();

	protected $cart_name = null;

	/**
	 * Shopping Cart contents.
	 *
	 * @access   public
	 * @var      array
	 */
	public $cart_contents = array();

	/**
	 * Shopping cart initializer.
	 *
	 * @access   public
	 * @return   void
	 */
	public function __construct($cart_name = null)
	{
		#Session::flush();
		//
		//
		$cart_name = (is_null($cart_name) ? Config::get('cartify::cart.session_name') : $cart_name);

		// Store the cart name.
		//
		$this->cart_name = $cart_name;

		// Get the Cart configuration.
		//
		$this->config = Config::get('cartify::cart');

		#if (isset($this->cart_contents[ $this->cart_name ]))
		if ($cart_contents = Session::get($this->cart_name))
		{
			$this->cart_contents[ $this->cart_name ] = $cart_contents;
		}
		else
		{
			$this->cart_contents[ $this->cart_name ]['cart_total']  = 0;
			$this->cart_contents[ $this->cart_name ]['total_items'] = 0;
		}


		// Check if we have the Cart contents on the session.
		//
		/*if ($cart_contents = Session::get($this->cart_name))
		{
			$this->cart_contents = $cart_contents;
		}

		// We don't have any cart session, set some base values.
		//
		else
		{
			$this->cart_contents['cart_total']  = 0;
			$this->cart_contents['total_items'] = 0;
		}*/
	}

	/**
	 * Insert items into the cart.
	 *
	 * @access   public
	 * @param    array
	 * @return   boolean
	 */
	public function add($items = array())
	{
		// Check if we have data passed.
		//
		if ( ! is_array($items) or count($items) == 0)
		{
			return false;
		}

		// We only update the cart when we insert data into it..
		//
		$save_cart = false;

		// Single item.
		//
		if (isset($items['id']))
		{
			// Try to add the item to the cart.
			//
			if ($rowid = $this->_insert($items))
			{
				$save_cart = true;
			}
		}

		// Multiple items.
		//
		else
		{
			// Loop through the items.
			//
			foreach ($items as $item)
			{
				// Validate the item.
				//
				if (is_array($item) and isset($item['id']))
				{
					// Try to add the item to the cart.
					//
					if ($this->_insert($item))
					{
						$save_cart = true;
					}
				}
			}
		}

		// Update the cart if the insert was successful.
		//
		if ($save_cart)
		{
			// Update the cart.
			//
			$this->update_cart();

			// See what we want to return..
			//
			return (isset($rowid) ? $rowid : true);
		}

		// Something went wrong...
		//
		return false;
	}

	/**
	 * Updates an item quantity, or items quantities.
	 *
	 * @access   public
	 * @param    array
	 * @return   boolean
	 */
	public function update($items = array())
	{
		// Check if we have data.
		//
		if ( ! is_array($items) or count($items) == 0)
		{
			return false;
		}

		// We only update the cart when we insert data into it..
		//
		$save_cart = false;

		// Single item.
		//
		if (isset($items['rowid']) and isset($items['qty']))
		{
			// Try to update the item.
			//
			if ($this->_update($items) == true)
			{
				$save_cart = true;
			}
		}

		// Multiple items.
		//
		else
		{
			// Loop through the items.
			//
			foreach ($items as $item)
			{
				// Validate the item.
				//
				if (is_array($item) and isset($item['rowid']) and isset($item['qty']))
				{
					// Try to update the item.
					//
					if ($this->_update($item))
					{
						$save_cart = true;
					}
				}
			}
		}

		// Update the cart if the insert was successful.
		//
		if ($save_cart)
		{
			// Update the cart.
			//
			$this->update_cart();

			// We are done here.
			//
			return true;
		}

		// Something went wrong...
		//
		return false;
	}

	/**
	 * Removes an item from the cart.
	 *
	 * @access   public
	 * @param    integer
	 * @return   boolean
	 */
	public function remove($rowid = null)
	{
		// Check if we have an id passed.
		//
		if (is_null($rowid))
		{
			return false;
		}

		// Try to remove the item.
		//
		if ($this->update(array('rowid' => $rowid, 'qty' => 0)))
		{
			// Success, item removed.
			//
			return true;
		}

		// Something went wrong.
		//
		return false;
	}

	/**
	 * Returns the cart total.
	 *
	 * @access   public
	 * @return   integer
	 */
	public function total()
	{
		return $this->cart_contents[ $this->cart_name ]['cart_total'];
	}


	/**
	 * Returns the total item count.
	 *
	 * @access   public
	 * @return   integer
	 */
	public function total_items()
	{
		return $this->cart_contents[ $this->cart_name ]['total_items'];
	}

	/**
	 * Returns the cart contents.
	 *
	 * @access   public
	 * @return   array
	 */
	public function contents()
	{
		// Get the cart contents.
		//
		$cart = $this->cart_contents[ $this->cart_name ];

		// Remove these so they don't create a problem when showing the cart table.
		//
		unset($cart['total_items']);
		unset($cart['cart_total']);

		// Return the cart contents.
		//
		return $cart;
	}

	/**
	 * Checks if an item has options.
	 *
	 * It returns 'true' if the rowid passed to this function correlates to an item
	 * that has options associated with it, otherwise returns 'false'.
	 *
	 * @access   public
	 * @param    integer
	 * @return   boolean
	 */
	public function has_options($rowid = null)
	{
		// Check if this product have options.
		//
		if ( ! isset($this->cart_contents[ $this->cart_name ][ $rowid ]['options']) or count($this->cart_contents[ $this->cart_name ][ $rowid ]['options']) === 0)
		{
			// We don't have options for this item.
			//
			return false;
		}

		// We have options for this product.
		//
		return true;
	}

	/**
	 * Returns an array of options, for a particular product row ID.
	 *
	 * @access   public
	 * @param    integer
	 * @return   array
	 */
	public function product_options($rowid = null)
	{
		// Check if this product have options.
		//
		if ( ! $this->has_options($rowid))
		{
			// No options, return an empty array.
			//
			return array();
		}

		// Return this product options.
		//
		return $this->cart_contents[ $this->cart_name ][ $rowid ]['options'];
	}

	/**
	 * Insert an item into the cart.
	 *
	 * @access   private
	 * @param    array
	 * @return   mixed
	 */
	private function _insert($items = array())
	{
		// Check if we have data.
		//
		if ( ! is_array($items) or count($items) == 0)
		{
			return false;
		}

		// Make sure the array contains the proper indexes.
		//
		if ( ! isset($items['id']) or ! isset($items['qty']) or ! isset($items['price']) or ! isset($items['name']))
		{
			return false;
		}

		// Prepare the quantity.
		//
		$items['qty'] = trim(preg_replace('/([^0-9])/i', '', $items['qty']));
		$items['qty'] = trim(preg_replace('/(^[0]+)/i', '', $items['qty']));

		// If the quantity is zero or blank there's nothing for us to do.
		//
		if ( ! is_numeric($items['qty']) OR $items['qty'] == 0)
		{
			return false;
		}

		// Validate the product id.
		//
		if ( ! preg_match("/^[" . $this->product_id_rules . "]+$/i", $items['id']))
		{
			return false;
		}

		// Validate the product name.
		//
		if ( ! preg_match("/^[" . $this->product_name_rules . "]+$/i", $items['name']))
		{
			return false;
		}

		// Prepare the price.
		//
		$items['price'] = trim(preg_replace('/([^0-9\.])/i', '', $items['price']));
		$items['price'] = trim(preg_replace('/(^[0]+)/i', '', $items['price']));

		// Is the price a valid number?
		//
		if ( ! is_numeric($items['price']))
		{
			return false;
		}

		// Create a unique identifier.
		//
		if (isset($items['options']) and count($items['options']) > 0)
		{
			$rowid = md5($items['id'] . implode('', $items['options']));
		}
		else
		{
			$rowid = md5($items['id']);
		}

		// Let's unset this first, just to make sure our index contains only the data from this submission.
		//
		unset($this->cart_contents[ $this->cart_name ][ $rowid ]);

		// Create a new index with our new row ID.
		//
		$this->cart_contents[ $this->cart_name ][ $rowid ]['rowid'] = $rowid;

		// And add the new items to the cart array.
		//
		foreach ($items as $key => $val)
		{
			$this->cart_contents[ $this->cart_name ][ $rowid ][ $key ] = $val;
		}

		// Item added with success.
		//
		return $rowid;
	}

	/**
	 * Updates the cart items.
	 *
	 * @access   private
	 * @param    array
	 * @return   boolean
	 */
	private function _update($items = array())
	{
		// Make sure the array contains the proper indexes.
		//
		if ( ! isset($items['qty']) or ! isset($items['rowid']) or ! isset($this->cart_contents[ $this->cart_name ][ $items['rowid'] ]))
		{
			return false;
		}

		// Prepare the quantity.
		//
		$items['qty'] = trim(preg_replace('/([^0-9])/i', '', $items['qty']));

		// Is the quantity a number ?
		//
		if ( ! is_numeric($items['qty']))
		{
			return false;
		}

		// Is the new quantity different than what is already saved in the cart?
		// If it's the same there's nothing to do
		if ($this->cart_contents[ $this->cart_name ][ $items['rowid'] ]['qty'] == $items['qty'])
		{
			return false;
		}

		// Is the quantity zero?  If so we will remove the item from the cart.
		// If the quantity is greater than zero we are updating
		if ($items['qty'] == 0)
		{
			unset($this->cart_contents[ $this->cart_name ][ $items['rowid'] ]);
		}
		else
		{
			$this->cart_contents[ $this->cart_name ][ $items['rowid'] ]['qty'] = $items['qty'];
		}

		// Cart updated.
		//
		return true;
	}

	/**
	 * Updates the cart session.
	 *
	 * @access   protected
	 * @return   boolean
	 */
	protected function update_cart()
	{
		// Unset these so our total can be calculated correctly below.
		//
		unset( $this->cart_contents[ $this->cart_name ]['total_items'] );
		unset( $this->cart_contents[ $this->cart_name ]['cart_total'] );

		// Initiate the needed counters.
		//
		$total = 0;
		$items = 0;

		// Loop through the cart items.
		//
		foreach ($this->cart_contents[ $this->cart_name ] as $rowid => $item)
		{
			// Make sure the array contains the proper indexes.
			//
			if ( ! is_array($item) or ! isset($item['price']) or ! isset($item['qty']))
			{
				continue;
			}

			// Calculations...
			//
			$total += ($item['price'] * $item['qty']);
			$items += $item['qty'];

			// Set the subtotal of this item.
			//
			$this->cart_contents[ $this->cart_name ][ $rowid ]['subtotal'] = ($this->cart_contents[ $this->cart_name ][ $rowid ]['price'] * $this->cart_contents[ $this->cart_name ][ $rowid ]['qty']);
		}

		// Set the cart total and total items.
		//
		$this->cart_contents[ $this->cart_name ]['total_items'] = $items;
		$this->cart_contents[ $this->cart_name ]['cart_total']  = $total;

		// Is our cart empty?
		//
		if (count($this->cart_contents[ $this->cart_name ]) <= 2)
		{
			// If so we delete it from the session
			//
			$this->destroy();

			// Nothing more to do here...
			//
			return false;
		}

		// Update the cart session data.
		//
		Session::put($this->cart_name, $this->cart_contents[ $this->cart_name ]);

		// Success.
		//
		return true;
	}

	/**
	 * Returns the supplied number with commas and a decimal point.
	 *
	 * @access   public
	 * @param    integer
	 * @return   integer
	 */
	public static function format_number($number = null)
	{
		// Check if we have a valid number.
		//
		if ( is_null( $number ) )
		{
			return '';
		}

		// Remove anything that isn't a number or decimal point.
		//
		$number = trim(preg_replace('/([^0-9\.])/i', '', $number));

		// Return the formated number.
		//
		return number_format($number, 2, '.', ',');
	}

	/**
	 * Empties the cart, and removes the session.
	 *
	 * @access   public
	 * @return   void
	 */
	public function destroy()
	{
		// Remove all the data from the cart and set some base values
		//
		$this->cart_contents[ $this->cart_name ] = array(
			'cart_total'  => 0,
			'total_items' => 0
		);

		// Remove the session.
		//
		Session::forget($this->cart_name);
	}
}
