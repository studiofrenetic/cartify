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

namespace Cartify;

/**
 * Libraries we can use.
 */
use Cartify\Libraries\Cart;
use Cartify\Libraries\Whishlist;

/**
 * Cartify class.
 */
class Cartify
{
	public static function cart()
	{
		return new Cart();
	}

	public static function wishlist()
	{
		return new Wishlist();
	}
}
