<?php
/**
 * Woo ReferCheck - Functions
 *
 * @package  Woo_ReferCheck
 * @category Functions
 * @author   Mark Kathmann
 */

// exit if this file is accessed directly
if ( !defined('ABSPATH') ) {
    exit;
}

/**
 * Display the custom WCRC fields on every product edit page
 */
function woo_refercheck_add_custom_general_fields() {
    global $woocommerce, $post;

    // read the current referrers list
    $referrers = get_post_meta( $post->ID, '_woo_refercheck_referrers', true );

    // convert to array
    $ref_array = json_decode( $referrers );

    // build the value of the textarea
    $theval = '';
    if ( count( $ref_array ) > 0 ) {
        $i = 0;
        foreach ( $ref_array as $item ) {
            $theval .= $item;

            if ( $i != count( $ref_array ) - 1 ) {
                $theval .= PHP_EOL;
            }

            $i++;
        }
    }

    // start the HTML output for the custom fields
    echo '<div class="options_group">';

    // create and output the checkbox
    woocommerce_wp_checkbox(
        array(
            'id'          => '_woo_refercheck_checkbox',
            'label'       => __( 'Limit to referrer(s)', 'woo-refercheck' ),
            'description' => __( 'Select if the display of this product should be limited to the specified referrer(s).', 'woo-refercheck' ),
        )
    );

    // create and output the text field
    woocommerce_wp_textarea_input(
        array(
            'id'          => '_woo_refercheck_referrers',
            'label'       => __( 'Allowed referrer(s)', 'woo-refercheck' ),
            'placeholder' => '',
            'desc_tip'    => 'true',
            'description' => __( 'Add the full HTTP addresseses of allowed referrer(s) for this product, one per line.', 'woo-refercheck' ),
            'value'       => $theval,
        )
    );

    woocommerce_wp_text_input(
        array(
            'id'          => '_woo_refercheck_target',
            'label'       => __( 'Redirect to', 'woo-refercheck' ),
            'placeholder' => 'http://',
            'desc_tip'    => 'true',
            'description' => __( 'Enter the absolute URL to redirect to when this product is accessed form an unallowed referer.', 'woo-refercheck' )
        )
    );

    // complete the HTML output
    echo '</div>';
}

/**
 * Save the custom WCRC fields with the product
 *
 * @param int $product_id
 */
function woo_refercheck_add_custom_general_fields_save( $product_id ) {
    // read the checkbox
    $wcrc_checkbox = isset( $_POST['_woo_refercheck_checkbox'] ) ? 'yes' : 'no';

    // write the checkbox in the product's meta data
    update_post_meta( $product_id, '_woo_refercheck_checkbox', $wcrc_checkbox );

    // read the referrers textarea
    $wcrc_referrers = $_POST['_woo_refercheck_referrers'];

    // explode to array
    $wcrc_array = explode( PHP_EOL, $wcrc_referrers );

    // clean up the elements
    if ( count( $wcrc_array ) > 0 ) {
        $i = 0;
        foreach ( $wcrc_array as $item ) {
            $wcrc_array[$i] = trim( $item );
            $i++;
        }

        $rdata = json_encode( $wcrc_array );
    } else {
        $rdata = '';
    }

    // write the referrers in the product's meta data (if filled in)
    if( !empty( $wcrc_referrers ) ) {
        update_post_meta( $product_id, '_woo_refercheck_referrers', $rdata );
    }

    // read the redirect target text field
    $wcrc_target = $_POST['_woo_refercheck_target'];

    if( !empty( $wcrc_target ) ) {
        update_post_meta( $product_id, '_woo_refercheck_target', $wcrc_target );
    }
}

/**
 * Check the product referrers settings and redirect if needed
 */
function woo_refercheck_product_filter() {
    global $post;

    // read the product's checkbox status
    $checkbox = get_post_meta( $post->ID, '_woo_refercheck_checkbox', true );

    // check if any further action needs to be taken
    if ( $checkbox == 'yes' ) {
        // read the current referrer
        $referrer = wp_get_referer();

        // read the product's list of referrers
        $referrers = get_post_meta( $post->ID, '_woo_refercheck_referrers', true );

        // convert to array
        $ref_array = json_decode( $referrers );

        // set the result marker
        $okay = false;

        // check the list of referrers against the current referrer
        if ( count( $ref_array ) > 0 ) {
            foreach ( $ref_array as $url ) {
                if ( $url == $referrer || $url . '/' == $referrer || $url == $referrer . '/' ) {
                    $okay = true;
                }
            }
        }

        // any luck with the checked referrers?
        if ( !$okay ) {
            // bad referrer, read the redirect URL
            $url = get_post_meta( $post->ID, '_woo_refercheck_target', true );

            // is the URL filled in?
            if ( !isset( $url ) || strlen( trim ( $url ) ) == 0 ) {
                // no URL, use home
                $url = home_url();
            }

            // redirect the visitor
            wp_redirect( $url );
            exit();
        }
    }
}
