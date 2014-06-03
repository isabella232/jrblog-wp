<?php
/**
 * @version   2.0.1 May 28, 2014
 * @author    JaidynReiman http://www.jrconway.net
 * @copyright Copyright (C) 2014 JaidynReiman
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

global $gantry;
?>

<form role="search" method="get" id="searchform" class="form-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search' ); ?>" value="<?php echo wp_kses( get_search_query(), null ); ?>" />
    <input type="submit" class="btn btn-primary" id="searchsubmit" value="<?php esc_attr_e( 'Search' ); ?>" />
</form>