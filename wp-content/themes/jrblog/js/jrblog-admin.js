/**
 * jrBlog Admin Javascript
 *
 * This contains all Javascript functions for the jrBlog admin.
 *
 * @package WordPress
 * @subpackage jrBlog
 * @since jrBlog 1.9.4
 */
;(function(window, document, undefined, $){

    // When DOM is ready...
    $(function(){

        // Chosen select
        $('.chosen-select').chosen({
            allow_single_deselect: true,
            placeholder_text_multiple: "Select one or more items"
        });

        if (typeof Bebop !== 'undefined') {

            if (typeof Bebop.List !== 'undefined') {

                // Posts, Pages, Characters, and Reviews: Related Game
                Bebop.List.addFormAction('addRelatedGame', function(event) {

                    var $selector = $(event.currentTarget).siblings('[bebop-list--formElId="selector"]');
                        itemId    = $selector.val();

                    if (!itemId) {

                        alert('You need to select a Game');
                    }

                    else {

                        this.addNewitem({
                            'id': itemId,
                            'view': 'browse'
                        });

                        $selector.val('');
                    }
                });

                // Posts, Pages, Games, and Reviews: Related Character
                Bebop.List.addFormAction('addRelatedCharacter', function(event) {

                    var $selector = $(event.currentTarget).siblings('[bebop-list--formElId="selector"]');
                        itemId    = $selector.val();

                    if (!itemId) {

                        alert('You need to select a Character');
                    }

                    else {

                        this.addNewitem({
                            'id': itemId,
                            'view': 'browse'
                        });

                        $selector.val('');
                    }
                });

            }

        }
    });

})(window, document, undefined, jQuery || $);