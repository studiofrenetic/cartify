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
class Wishlist
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
     * Wishlist contents.
     *
     * @access   public
     * @var      array
     */
    public static $wishlist_contents = array();


    /**
     * --------------------------------------------------------------------------
     * Function: init()
     * --------------------------------------------------------------------------
     *
     * Wishlist initializer.
     *
     * @access    public
     * @param     string
     * @return    void
     */
    public static function init()
    {
        // Get the Cart configuration.
        //
        static::$config = Config::get('lara-cart::cart');

        // Check if we have the Wishlist contents on the session.
        //
        if ( $wishlist_contents = Session::get( static::$config['wishlist_session'] ) ):
            static::$wishlist_contents = $wishlist_contents;

        // We don't have any cart session, set some base values.
        //
        else:
            static::$wishlist_contents['cart_total']  = 0;
            static::$wishlist_contents['total_items'] = 0;
        endif;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: add()
     * --------------------------------------------------------------------------
     *
     * Insert items into the wishlist.
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

        // We only update the wishlist when we insert data into it..
        //
        $save_wishlist = false;

        // Single item.
        //
        if ( isset($items['id']) ):
            // Try to add the item to the wishlist.
            //
            if ( $rowid = static::_insert($items) ):
                $save_wishlist = true;
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
                    // Try to add the item to the wishlist.
                    //
                    if ( static::_insert($item) ):
                        $save_wishlist = true;
                    endif;
                endif;
            endforeach;
        endif;

        // Update the wishlist if the insert was successful.
        //
        if ( $save_wishlist ):
            // Update the wishlist.
            //
            self::update_wishlist();

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
            self::update_wishlist();

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
     * Function: total_items()
     * --------------------------------------------------------------------------
     *
     * Returns the total of items in the wishlist.
     *
     * @access    public
     * @return    integer
     */
    public static function total_items()
    {
        return static::$wishlist_contents['total_items'];
    }


    /**
     * --------------------------------------------------------------------------
     * Function: contents()
     * --------------------------------------------------------------------------
     *
     * Returns the wishlist contents.
     *
     * @access    public
     * @return    array
     */
    public static function contents()
    {
        // Get the wishlist contents.
        //
        $wishlist = static::$wishlist_contents;

        // Remove these so they don't create a problem when showing the wishlist table.
        //
        unset( $wishlist['total_items'] );
        unset( $wishlist['cart_total'] );

        // Return the wishlist contents.
        //
        return $wishlist;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: _insert()
     * --------------------------------------------------------------------------
     *
     * Insert an item into the wishlist.
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
        if ( ! isset($items['id']) or ! isset($items['price']) or ! isset($items['name']) ):
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
        $rowid = md5( $items['id'] );

        // Let's unset this first, just to make sure our index contains only the data from this submission.
        //
        unset( static::$wishlist_contents[ $rowid ] );

        // Create a new index with our new row ID.
        //
        static::$wishlist_contents[ $rowid ]['rowid'] = $rowid;

        // And add the new items to the wishlist array.
        //
        foreach ( $items as $key => $val ):
            static::$wishlist_contents[ $rowid ][ $key ] = $val;
        endforeach;

        // Item added with success.
        //
        return $rowid;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: update_wishlist()
     * --------------------------------------------------------------------------
     *
     * Updates the wishlist session.
     *
     * @access      private
     * @return    boolean
     */
    private static function update_wishlist()
    {
        // Unset these so our total can be calculated correctly below.
        //
        unset( static::$wishlist_contents['total_items'] );

        // Initiate the needed counters.
        //
        $total = 0;
        $items = 0;

        // Loop through the wishlist items.
        //
        foreach ( static::$wishlist_contents as $rowid => $item ):
            // Make sure the array contains the proper indexes.
            //
            if ( ! is_array($item) or ! isset($item['price']) ):
                continue;
            endif;

            // Calculations...
            //
            $items += $item['qty'];
        endforeach;

        // Set the wishlist total and total items.
        //
        static::$wishlist_contents['total_items'] = $items;

        // Is our wishlist empty?
        //
        if ( count(static::$wishlist_contents) <= 1 ):
            // If so we delete it from the session
            //
            static::destroy();

            // Nothing more to do here...
            //
            return false;
        endif;

        // Update the cart session data.
        //
        Session::put( static::$config['wishlist_session'], static::$wishlist_contents );

        // Success.
        //
        return true;
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
        static::$wishlist_contents = array(
            'total_items' => 0
        );

        // Remove the session.
        //
        Session::forget( static::$config['wishlist_session'] );
    }
}