<?php namespace LaraCart;

/*
 * --------------------------------------------------------------------------
 * What we can use in this class.
 * --------------------------------------------------------------------------
 */
use Config, Session;


/**
 * --------------------------------------------------------------------------
 * Lara Cart
 * --------------------------------------------------------------------------
 *
 * A Shopping Cart based on the Cart library from CodeIgniter for use with
 * the Laravel Framework.
 *
 * @package  lara-cart
 * @version  1.0
 * @author   Bruno Gaspar <brunofgaspar@live.com.pt>
 * @link     https://github.com/bruno-g/lara-cart
 */
class xCart
{
    /**
     * Regular expression to validate product ID's.
     *
     *  Allowed:
     *        alpha-numeric
     *        dashes
     *        underscores
     *        periods
     *
     * @access   protected
     * @var      string
     */
    protected static $product_id_rules = '\.a-z0-9_-';

    /**
     * Regular expression to validate product Names.
     *
     *  Allowed:
     *        alpha-numeric
     *        dashes
     *        underscores
     *        colons
     *        periods
     *
     * @access   protected
     * @var      string
     */
    protected static $product_name_rules = '\.\:\-_ a-z0-9';

    /**
     * Shopping Cart config.
     *
     * @access   protected
     * @var      array
     */
    protected static $config = array();

    /**
     * Shopping Cart contents.
     *
     * @access   public
     * @var      array
     */
    public static $cart_contents = array();


    /**
     * --------------------------------------------------------------------------
     * Function: init()
     * --------------------------------------------------------------------------
     *
     * Shopping cart initializer.
     *
     * @access    public
     * @return    void
     */
    public static function init()
    {
        // Get the Cart configuration.
        //
        static::$config = Config::get('lara-cart::cart');

        // Check if we have the Cart contents on the session.
        //
        if ( $cart_contents = Session::get( static::$config['session_name'] ) ):
            static::$cart_contents = $cart_contents;

        // We don't have any cart session, set some base values.
        //
        else:
            static::$cart_contents['cart_total']  = 0;
            static::$cart_contents['total_items'] = 0;
        endif;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: add()
     * --------------------------------------------------------------------------
     *
     * Insert items into the cart.
     *
     * @access    public
     * @param     array
     * @return    boolean
     */
    public static function add( $items = array() )
    {
        // Check if we have data passed.
        //
        if ( ! is_array($items) or count($items) == 0 ):
            return false;
        endif;

        // We only update the cart when we insert data into it..
        //
        $save_cart = false;

        // Single item.
        //
        if ( isset($items['id']) ):
            // Try to add the item to the cart.
            //
            if ( $rowid = static::_insert($items) ):
                $save_cart = true;
            endif;

        // Multiple items.
        //
        else:
            // Loop through the items.
            //
            foreach( $items as $item ):
                // Validate the item.
                //
                if ( is_array($item) and isset($item['id'] ) ):
                    // Try to add the item to the cart.
                    //
                    if ( static::_insert($item) ):
                        $save_cart = true;
                    endif;
                endif;
            endforeach;
        endif;

        // Update the cart if the insert was successful.
        //
        if ( $save_cart ):
            // Update the cart.
            //
            self::update_cart();

            // See what we want to return..
            //
            return ( isset($rowid) ? $rowid : true );
        endif;

        // Something went wrong...
        //
        return false;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: update()
     * --------------------------------------------------------------------------
     *
     * Updates an item quantity, or items quantities.
     *
     * @access    public
     * @param     array
     * @return    boolean
     */
    public static function update( $items = array() )
    {
        // Check if we have data passed.
        //
        if ( ! is_array($items) or count($items) == 0 ):
            return false;
        endif;

        // We only update the cart when we insert data into it..
        //
        $save_cart = false;

        // Single item.
        //
        if ( isset($items['rowid']) and isset($items['qty']) ):
            // Try to update the item.
            //
            if ( self::_update($items) == true):
                $save_cart = true;
            endif;

        // Multiple items.
        //
        else:
            // Loop through the items.
            //
            foreach ( $items as $item ):
                // Validate the item.
                //
                if ( is_array($item) and isset($item['rowid']) and isset($item['qty']) ):
                    // Try to update the item.
                    //
                    if ( self::_update($item) ):
                        $save_cart = true;
                    endif;
                endif;
            endforeach;
        endif;

        // Update the cart if the insert was successful.
        //
        if ( $save_cart ):
            // Update the cart.
            //
            self::update_cart();

            // We are done here.
            //
            return true;
        endif;

        // Something went wrong...
        //
        return false;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: remove()
     * --------------------------------------------------------------------------
     *
     * Removes an item from the cart.
     *
     * @access    public
     * @param     integer
     * @return    boolean
     */
    public static function remove( $rowid = null )
    {
        // Check if we have an id passed.
        //
        if ( is_null( $rowid ) ):
            return false;
        endif;

        // Try to remove the item.
        //
        if ( self::update( array('rowid' => $rowid, 'qty' => 0) ) ):
            // Success, item removed.
            //
            return true;
        endif;

        // Something went wrong.
        //
        return false;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: total()
     * --------------------------------------------------------------------------
     *
     * Returns the cart total.
     *
     * @access    public
     * @return    integer
     */
    public static function total()
    {
        return static::$cart_contents['cart_total'];
    }


    /**
     * --------------------------------------------------------------------------
     * Function: total_items()
     * --------------------------------------------------------------------------
     *
     * Returns the total item count.
     *
     * @access    public
     * @return    integer
     */
    public static function total_items()
    {
        return static::$cart_contents['total_items'];
    }


    /**
     * --------------------------------------------------------------------------
     * Function: contents()
     * --------------------------------------------------------------------------
     *
     * Returns the cart contents.
     *
     * @access    public
     * @return    array
     */
    public static function contents()
    {
        // Get the cart contents.
        //
        $cart = static::$cart_contents;

        // Remove these so they don't create a problem when showing the cart table.
        //
        unset( $cart['total_items'] );
        unset( $cart['cart_total'] );

        // Return the cart contents.
        //
        return $cart;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: has_options()
     * --------------------------------------------------------------------------
     *
     * Returns TRUE if the rowid passed to this function correlates to an item
     * that has options associated with it.
     *
     * @access    public
     * @param     integer
     * @return      boolean
     */
    public static function has_options( $rowid = null )
    {
        // Check if this product have options.
        //
        if ( ! isset(static::$cart_contents[ $rowid ]['options']) or count(static::$cart_contents[ $rowid ]['options']) === 0 ):
            // We don't have options for this item.
            //
            return false;
        endif;

        // We have options for this product.
        //
        return true;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: product_options()
     * --------------------------------------------------------------------------
     *
     * Returns an array of options, for a particular product row ID.
     *
     * @access    public
     * @param     integer
     * @return      array
     */
    public static function product_options( $rowid = null )
    {
        // Check if this product have options.
        //
        if ( ! self::has_options( $rowid ) ):
            // No options, return an empty array.
            //
            return array();
        endif;

        // Return this product options.
        //
        return static::$cart_contents[ $rowid ]['options'];
    }


    /**
     * --------------------------------------------------------------------------
     * Function: _insert()
     * --------------------------------------------------------------------------
     *
     * Insert an item into the cart.
     *
     * @access      private
     * @param     array
     * @return    mixed
     */
    private static function _insert($items = array())
    {
        // Check if we have data passed.
        //
        if ( ! is_array($items) or count($items) == 0 ):
            return false;
        endif;

        // Make sure the array contains the proper indexes.
        //
        if ( ! isset($items['id']) or ! isset($items['qty']) or ! isset($items['price']) or ! isset($items['name']) ):
            return false;
        endif;

        // Prepare the quantity.
        //
        $items['qty'] = trim( preg_replace('/([^0-9])/i', '', $items['qty']) );
        $items['qty'] = trim( preg_replace('/(^[0]+)/i', '', $items['qty']) );

        // If the quantity is zero or blank there's nothing for us to do.
        //
        if ( ! is_numeric($items['qty']) OR $items['qty'] == 0 ):
            return false;
        endif;

        // Validate the product id.
        //
        if ( ! preg_match("/^[" . self::$product_id_rules . "]+$/i", $items['id']) ):
            return false;
        endif;

        // Validate the product name.
        //
        if ( ! preg_match("/^[" . self::$product_name_rules . "]+$/i", $items['name']) ):
            return false;
        endif;

        // Prepare the price.
        //
        $items['price'] = trim( preg_replace('/([^0-9\.])/i', '', $items['price']) );
        $items['price'] = trim( preg_replace('/(^[0]+)/i', '', $items['price']) );

        // Is the price a valid number?
        //
        if ( ! is_numeric($items['price']) ):
            return false;
        endif;

        // Create a unique identifier.
        //
        if ( isset($items['options']) and count($items['options']) > 0 ):
            $rowid = md5( $items['id'] . implode('', $items['options']) );
        else:
            $rowid = md5( $items['id'] );
        endif;

        // Let's unset this first, just to make sure our index contains only the data from this submission.
        //
        unset( static::$cart_contents[ $rowid ] );

        // Create a new index with our new row ID.
        //
        static::$cart_contents[ $rowid ]['rowid'] = $rowid;

        // And add the new items to the cart array.
        //
        foreach ( $items as $key => $val ):
            static::$cart_contents[ $rowid ][ $key ] = $val;
        endforeach;

        // Item added with success.
        //
        return $rowid;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: _update()
     * --------------------------------------------------------------------------
     *
     * Updates the cart items.
     *
     * @access      private
     * @param     array
     * @return    boolean
     */
    private static function _update( $items = array() )
    {
        // Make sure the array contains the proper indexes.
        //
        if ( ! isset($items['qty']) or ! isset($items['rowid']) or ! isset(static::$cart_contents[ $items['rowid'] ]) ):
            return false;
        endif;

        // Prepare the quantity.
        //
        $items['qty'] = trim( preg_replace('/([^0-9])/i', '', $items['qty']) );

        // Is the quantity a number ?
        //
        if ( ! is_numeric($items['qty']) ):
            return false;
        endif;

        // Is the new quantity different than what is already saved in the cart?
        // If it's the same there's nothing to do
        if ( static::$cart_contents[ $items['rowid'] ]['qty'] == $items['qty'] ):
            return false;
        endif;

        // Is the quantity zero?  If so we will remove the item from the cart.
        // If the quantity is greater than zero we are updating
        if ( $items['qty'] == 0 ):
            unset( static::$cart_contents[ $items['rowid'] ] );
        else:
            static::$cart_contents[ $items['rowid'] ]['qty'] = $items['qty'];
        endif;

        // Cart updated.
        //
        return true;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: update_cart()
     * --------------------------------------------------------------------------
     *
     * Updates the cart session.
     *
     * @access      private
     * @return    boolean
     */
    private static function update_cart()
    {
        // Unset these so our total can be calculated correctly below.
        //
        unset( static::$cart_contents['total_items'] );
        unset( static::$cart_contents['cart_total'] );

        // Initiate the needed counters.
        //
        $total = 0;
        $items = 0;

        // Loop through the cart items.
        //
        foreach ( static::$cart_contents as $rowid => $item ):
            // Make sure the array contains the proper indexes.
            //
            if ( ! is_array($item) or ! isset($item['price']) or ! isset($item['qty']) ):
                continue;
            endif;

            // Calculations...
            //
            $total += ($item['price'] * $item['qty']);
            $items += $item['qty'];

            // Set the subtotal of this item.
            //
            static::$cart_contents[ $rowid ]['subtotal'] = (static::$cart_contents[ $rowid ]['price'] * static::$cart_contents[ $rowid ]['qty']);
        endforeach;

        // Set the cart total and total items.
        //
        static::$cart_contents['total_items'] = $items;
        static::$cart_contents['cart_total']  = $total;

        // Is our cart empty?
        //
        if ( count(static::$cart_contents) <= 2 ):
            // If so we delete it from the session
            //
            static::destroy();

            // Nothing more to do here...
            //
            return false;
        endif;

        // Update the cart session data.
        //
        Session::put( static::$config['session_name'], static::$cart_contents );

        // Success.
        //
        return true;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: format_number()
     * --------------------------------------------------------------------------
     *
     * Returns the supplied number with commas and a decimal point.
     *
     * @access    public
     * @param     integer
     * @return    integer
     */
    public static function format_number( $number = null )
    {
        // Check if we have a valid number.
        //
        if ( is_null( $number ) ):
            return '';
        endif;

        // Remove anything that isn't a number or decimal point.
        //
        $number = trim(preg_replace('/([^0-9\.])/i', '', $number));

        // Return the formated number.
        //
        return number_format($number, 2, '.', ',');
    }


    /**
     * --------------------------------------------------------------------------
     * Function: destroy()
     * --------------------------------------------------------------------------
     *
     * Empties the cart, and removes the session.
     *
     * @access    public
     * @return    void
     */
    public static function destroy()
    {
        // Remove all the data from the cart and set some base values
        //
        static::$cart_contents = array(
            'cart_total'  => 0,
            'total_items' => 0
        );

        // Remove the session.
        //
        Session::forget( static::$config['session_name'] );
    }
}
