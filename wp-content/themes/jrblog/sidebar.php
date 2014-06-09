<?php
/**
 * @version   2.0.1 May 28, 2014
 * @author    JaidynReiman http://www.jrconway.net
 * @copyright Copyright (C) 2014 JaidynReiman
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
?>

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<div id="secondary" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div><!-- #secondary -->
	<?php endif; ?>