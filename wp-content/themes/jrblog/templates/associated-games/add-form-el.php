<?php

$items = get_posts(array(
    'numberposts' => -1,
    'orderby'     => 'title',
    'order'       => 'ASC',
    'post_type'   => 'game',
    'post_status' => array('draft', 'publish')
));

?>

<select bebop-list--formElId="selector" style="max-width:260px">
	<?php if ($items) { ?>

		<option value="">Choose a Game...</option>

		<?php foreach ($items as $item) { ?>

			<option value="<?php echo $item->ID; ?>">
				<?php echo $item->post_title; ?>
			</option>

		<?php }

	} else { ?>

		<option value="" disabled>No Games available</option>

	<?php } ?>
</select>

<button bebop-list--formElId="add" bebop-list--formAction="addRelatedGame" class="button button-primary">
	Add <span class="bebop-ui-icon-add"></span>
</button>