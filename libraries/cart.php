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

namespace Cartify\Libraries;

/**
 * Libraries we can use.
 */
use Exception;
use Laravel\Config;
use Laravel\Session;

/**
 * Cart class.
 */
class Cart
{
	/**
	 * Regular expression to validate item ID's.
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
	protected $item_id_rules = '\.a-z0-9_-';

	/**
	 * Regular expression to validate item Names.
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
	protected $item_name_rules = '\.\:\-_ a-z0-9';

	/**
	 * Holds the cart name.
	 *
	 * @access   protected
	 * @var      string
	 */
	protected $cart_name = null;

	/**
	 * Shopping Cart contents.
	 *
	 * @access   protected
	 * @var      array
	 */
	protected $cart_contents = array();

	/**
	 * Shopping cart initializer.
	 *
	 * @access   public
	 * @return   void
	 */
	public function __construct($cart_name = null)
	{
		// Store the cart name.
		//
		$this->cart_name = (is_null($cart_name) ? Config::get('cartify::cart.session_name') : $cart_name);

		// Check if we have the Cart contents on the session.
		//
		if ($cart_contents = Session::get($this->cart_name))
		{
			$this->cart_contents[ $this->cart_name ] = $cart_contents;
		}

		// We don't have any cart session, set some base values.
		//
		else
		{
			$this->cart_contents[ $this->cart_name ]['cart_total']  = 0;
			$this->cart_contents[ $this->cart_name ]['total_items'] = 0;
		}
	}

	/**
	 * Insert items into the cart.
	 *
	 * @access   public
	 * @param    array
	 * @return   mixed
	 * @throws   Exception
	 */
	public function insert($items = array())
	{
		// Check if we have data.
		//
		if ( ! is_array($items) or count($items) == 0)
		{
			throw new Exception('Invalid data passed, an array is expected!');
		}

		// We only update the cart when we insert data into it.
		//
		$update_cart = false;

		// Single item?
		//
		if ( ! isset($items[0]))
		{
			// Check if the item was added to the cart.
			//
			if ($rowid = $this->_insert($items))
			{
				$update_cart = true;
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
				// Check if the item was added to the cart.
				//
				if ($this->_insert($item))
				{
					$update_cart = true;
				}
			}
		}

		// Update the cart if the insert was successful.
		//
		if ($update_cart)
		{
			// Update the cart.
			//
			$this->update_cart();

			// See what we want to return.
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
	 * @throws   Exception
	 */
	public function update($items = array())
	{
		// Check if we have data.
		//
		if ( ! is_array($items) or count($items) == 0)
		{
			throw new Exception('Invalid data passed, an array is expected!');
		}

		// We only update the cart when we insert data into it.
		//
		$update_cart = false;

		// Single item.
		//
		if (isset($items['rowid']) and isset($items['qty']))
		{
			// Try to update the item.
			//
			if ($this->_update($items) === true)
			{
				$update_cart = true;
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
						$update_cart = true;
					}
				}
			}
		}

		// Update the cart if the insert was successful.
		//
		if ($update_cart)
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
	 * @throws   Exception
	 */
	public function remove($rowid = null)
	{
		// Check if we have an id passed.
		//
		if (is_null($rowid))
		{
			throw new Exception('You need to pass a row id.');
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
		// Check if this item have options.
		//
		if ( ! isset($this->cart_contents[ $this->cart_name ][ $rowid ]['options']) or count($this->cart_contents[ $this->cart_name ][ $rowid ]['options']) === 0)
		{
			// We don't have options for this item.
			//
			return false;
		}

		// We have options for this item.
		//
		return true;
	}

	/**
	 * Returns an array of options, for a particular item row ID.
	 *
	 * @access   public
	 * @param    integer
	 * @return   array
	 */
	public function item_options($rowid = null)
	{
		// Check if this item have options.
		//
		if ( ! $this->has_options($rowid))
		{
			// No options, return an empty array.
			//
			return array();
		}

		// Return this item options.
		//
		return $this->cart_contents[ $this->cart_name ][ $rowid ]['options'];
	}

	/**
	 * Insert an item into the cart.
	 *
	 * @access   protected
	 * @param    array
	 * @return   integer
	 * @throws   Exception
	 */
	protected function _insert($item = array())
	{
		// Check if we have data.
		//
		if ( ! is_array($item) or count($item) == 0)
		{
			throw new Exception('Invalid data passed, an array is expected!');
		}

		// Required indexes.
		//
		$required_indexes = array('id', 'qty', 'price', 'name');

		// Loop through the required indexes.
		//
		foreach ($required_indexes as $index)
		{
			// Make sure the array contains this index.
			//
			if ( ! isset($item[ $index ]))
			{
				throw new Exception('Required index [' . $index . '] is missing.');
			}
		}

		// Prepare the quantity.
		//
		$item['qty'] = trim(preg_replace('/([^0-9])/i', '', $item['qty']));
		$item['qty'] = trim(preg_replace('/(^[0]+)/i', '', $item['qty']));

		// If the quantity is zero or blank there's nothing for us to do.
		//
		if ( ! is_numeric($item['qty']) OR $item['qty'] == 0)
		{
			return false;
		}

		// Validate the item id.
		//
		if ( ! preg_match("/^[" . $this->item_id_rules . "]+$/i", $item['id']))
		{
			return false;
		}

		// Validate the item name.
		//
		if ( ! preg_match("/^[" . $this->item_name_rules . "]+$/i", $item['name']))
		{
			return false;
		}

		// Prepare the price.
		//
		$item['price'] = trim(preg_replace('/([^0-9\.])/i', '', $item['price']));
		$item['price'] = trim(preg_replace('/(^[0]+)/i', '', $item['price']));

		// Is the price a valid number?
		//
		if ( ! is_numeric($item['price']))
		{
			return false;
		}

		// Create a unique identifier.
		//
		if (isset($item['options']) and count($item['options']) > 0)
		{
			$rowid = md5($item['id'] . implode('', $item['options']));
		}
		else
		{
			$rowid = md5($item['id']);
		}

		// Let's unset this first, just to make sure our index contains only the data from this submission.
		//
		unset($this->cart_contents[ $this->cart_name ][ $rowid ]);

		// Create a new index with our new row ID.
		//
		$this->cart_contents[ $this->cart_name ][ $rowid ]['rowid'] = $rowid;

		// And add the new item to the cart array.
		//
		foreach ($item as $key => $val)
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
	 * @access   protected
	 * @param    array
	 * @return   boolean
	 * @throws   Exception
	 */
	protected function _update($item = array())
	{
		// Check if the item exists in the cart.
		//
		if ( ! isset($this->cart_contents[ $this->cart_name ][ $item['rowid'] ]))
		{
			throw new Exception('Item does not exist!');
		}

		// Required indexes.
		//
		$required_indexes = array('qty', 'rowid');

		// Loop through the required indexes.
		//
		foreach ($required_indexes as $index)
		{
			// Make sure the array contains this index.
			//
			if ( ! isset($item[ $index ]))
			{
				throw new Exception('Required index [' . $index . '] is missing.');
			}
		}

		// Prepare the quantity.
		//
		$item['qty'] = trim(preg_replace('/([^0-9])/i', '', $item['qty']));

		// Is the quantity a number ?
		//
		if ( ! is_numeric($item['qty']))
		{
			throw new Exception('Quantity needs to be numeric!');
		}

		// If the new quantaty is the same the already in the cart, there is nothing to update.
		//
		if ($this->cart_contents[ $this->cart_name ][ $item['rowid'] ]['qty'] == $item['qty'])
		{
			return true;
		}

		// If the quantity is zero, we will be removing the item from the cart.
		//
		if ($item['qty'] == 0)
		{
			unset($this->cart_contents[ $this->cart_name ][ $item['rowid'] ]);
		}

		// Quantity is greater than zero, let's update the item cart.
		//
		else
		{
			$this->cart_contents[ $this->cart_name ][ $item['rowid'] ]['qty'] = $item['qty'];
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
		unset($this->cart_contents[ $this->cart_name ]['total_items']);
		unset($this->cart_contents[ $this->cart_name ]['cart_total']);

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
