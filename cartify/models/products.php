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
 * @link https://github.com/bruno-g/cartify
 */

namespace Cartify\Models;

/**
 * This file is for the examples only!
 */

class Products
{
	public static function get_list()
	{
		// Declare our static products, this is just for the example !!!
		//
		return array(
			'sku_123ABC' => array(
				'id'      => 'sku_123ABC',
				'price'   => 39.95,
				'name'    => 'T-Shirt',
				'image'   => 'tshirt.jpg',
				'options' => array(
					'Size'  => array(
						'S' => 'S',
						'M' => 'M',
						'L' => 'L'
					),
					'Color' => array(
						'Red'    => 'Red',
						'Blue'   => 'Blue',
						'Yellow' => 'Yellow',
						'White'  => 'White'
					),
					'Style' => array(
						'Unisex' => 'Unisex',
						'Womens' => 'Womens'
					)
				)
			),
			'sku_567ZYX' => array(
				'id'      => 'sku_567ZYX',
				'price'   => 9.95,
				'name'    => 'Coffee Mug',
				'image'   => 'coffee_mug.jpg',
				'options' => array(
					'Design' => array(
						'Design 1'=> 'Design 1',
						'Design XpT0' => 'Design XpT0'
					)
				)
			),
			'sku_965QRS' => array(
				'id'      => 'sku_965QRS',
				'price'   => 29.95,
				'name'    => 'Shot Glass',
				'image'   => 'shot_glass.jpg'
			)
		);
	}
}
