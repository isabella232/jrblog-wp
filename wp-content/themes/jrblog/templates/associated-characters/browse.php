<?php

$items = get_posts(array(
    'numberposts' => -1,
    'orderby'     => 'title',
    'order'       => 'ASC',
    'post_type'   => 'character',
    'post_status' => array('draft', 'publish')
));

foreach ($items as $item) { ?>
	
	{{#id_is_<?php echo $item->ID; ?>}}
	<div class="associated-characters-bebop-ui-item-title">
		<?php echo $item->post_title; ?>
	</div>
	{{/id_is_<?php echo $item->ID; ?>}}

<?php }