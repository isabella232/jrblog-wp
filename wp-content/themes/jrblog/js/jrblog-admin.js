/**
 * jrBlog Admin Javascript
 *
 * This contains all Javascript functions for the jrBlog admin.
 *
 * @package WordPress
 * @subpackage jrBlog
 * @since jrBlog 1.9.4
 */
jQuery(function() {
    // Chosen select
    jQuery('.chosen-select').chosen({
        allow_single_deselect: true,
        placeholder_text_multiple: "Select one or more items"
    });
});