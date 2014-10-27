<?php

$items = get_posts(array(
    'numberposts' => -1,
    'orderby'     => 'title',
    'order'       => 'ASC',
    'post_type'   => 'character',
    'post_status' => array('draft', 'publish')
));

?>

<select name="id" id="" style="max-width:100%">
	<?php foreach ($items as $item) { ?>
		
		<option{{#id_is_<?php echo $item->ID; ?>}} selected{{/id_is_<?php echo $item->ID; ?>}} value="<?php echo $item->ID; ?>">
			<?php echo $item->post_title; ?>
		</option>

	<?php } ?>
</select>

<textarea name="description" style="max-width:100%" rows="4">{{description}}</textarea>