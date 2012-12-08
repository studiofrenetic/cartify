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

namespace Cartify\Models;


#############################################################
############ This file is for the examples only! ############
#############################################################


/**
 *
 */
class Products
{
	public static function get_list()
	{
		// Declare our static products, this is just for the example !!!
		//
		return array(
			// Product 1
			//
			'sku_123ABC' => array(
				'id'      => 'sku_123ABC',
				'price'   => 39.95,
				'name'    => 'T-Shirt',
				'image'   => 'tshirt.jpg',
				'options' => array(
					'Size'  => array(
						's' => 'S',
						'm' => 'M',
						'l' => 'L'
					),
					'Color' => array(
						'red'    => 'Red',
						'blue'   => 'Blue',
						'yellow' => 'Yellow',
						'white'  => 'White'
					),
					'Style' => array(
						'unisex' => 'Unisex',
						'womens' => 'Womens'
					)
				)
			),

			// Product 2
			//
			'sku_567ZYX' => array(
				'id'      => 'sku_567ZYX',
				'price'   => 9.95,
				'name'    => 'Coffee Mug',
				'image'   => 'coffee_mug.jpg',
				'options' => array(
					'Design' => array(
						'design 1'=> 'Design 1',
						'design xpt0' => 'Design XpT0'
					)
				)
			),

			// Product 3
			//
			'sku_965QRS' => array(
				'id'      => 'sku_965QRS',
				'price'   => 29.95,
				'name'    => 'Shot Glass',
				'image'   => 'shot_glass.jpg'
			)
		);
	}

	public static function get_options($item_id)
	{
		// Get the list of products.
		//
		$products = static::get_list();

		// Return the product options.
		//
		return array_get($products, $item_id . '.options', array());
	}
}
