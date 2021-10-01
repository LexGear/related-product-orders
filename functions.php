<?php
	/*
	Plugin Name: Related Product Orders
	Plugin URI:
	description: View related orders on product backend page.
	Version: 0.1
	Author: Luke Molnar
	Author URI:
	License: GPL2
	*/

function wporg_add_custom_box()
{
	$screens = [ 'product' ];
	foreach ( $screens as $screen ) {
		add_meta_box(
			'related-product-orders',
			'Related Product Orders',
			'rpo_box_html',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'wporg_add_custom_box' );

function rpo_box_html( $post )
{
	// Get all orders.
	$orders = wc_get_orders(
		array(
			"limit" => -1
		)
	);

	// Get all product IDs.
	$product_ids[] = $post->ID;
	$children[] = get_children( $post->ID );
	foreach ( $children as $child )
	{
		foreach ( $child as $var )
			$product_ids[] = $var->ID;
	}
	?>

	<style>
		.rpo-table
		{
			width: 100%;
			border-radius: 5px;
		}
		.rpo-table td, .rpo-table th
		{
			border: solid 1px rgb( 220, 220, 220 );
			padding: 5px 10px;
			border-radius: 5px;
			text-align: center;
		}
	</style>

	<table class="rpo-table">
		<tr>
			<th>Order#</th>
			<th>Date / Time</th>
			<th>Status</th>
			<th>Product / Variation ID</th>
			<th>Qty</th>
			<th>Subtotal</th>
		</tr>
		<?php foreach( $orders as $order ):  ?>
			<?php $items = $order->get_items(); ?>
			<?php foreach( $items as $item ):  ?>
				<?php
				$item_id = $item->get_data()["product_id"];
				$item_variation_id = $item->get_data()["variation_id"];
				$item_qty = $item->get_data()["quantity"];
				$item_subtotal = $item->get_data()["subtotal"];
				// var_dump( $item->get_data() );
				if ( in_array( $item_id, $product_ids ) ): ?>
				<tr>
					<td>
						<a target="_blank" href="/wp-admin/post.php?post=<?php echo $order->get_id(); ?>&action=edit"><?php echo $order->get_id(); ?></a>
					</td>
					<td>
						<?php
						$date_created = $order->get_date_created();
						if( ! empty( $date_created) )
							echo "<time>" . $date_created->date("F j, Y, g:i:s A") . "</time>";
						?>
					</td>
					<td>
						<?php echo $order->get_status(); ?>
					</td>
					<td>
						<?php
						echo $item_id;
						if ( $item_variation_id != 0 )
							echo " / " . $item_variation_id;
						?>
					</td>
					<td>
						<?php echo $item_qty; ?>
					</td>
					<td>
						<?php echo "$" . $item_subtotal; ?>
					</td>
				</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>

	<?php
}