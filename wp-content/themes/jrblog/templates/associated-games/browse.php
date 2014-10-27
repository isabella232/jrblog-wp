<?php

$items = get_posts(array(
    'numberposts' => -1,
    'orderby'     => 'title',
    'order'       => 'ASC',
    'post_type'   => 'game',
    'post_status' => array('draft', 'publish')
));

foreach ($items as $item) { ?>
	
	{{#id_is_<?php echo $item->ID; ?>}}
	<div class="associated-games-bebop-ui-item-title">
		<?php echo $item->post_title; ?>
	</div>
	{{/id_is_<?php echo $item->ID; ?>}}

<?php }