<?php
/**
 * View template for adding categories in admin context.
 *
 * @author Midori Kocak 2014
 *
 */
?>

<form action="<?= LINK_PREFIX ?>/Categories/Add" method="post">
	<div class="row">
		<div class="large-12 columns">
			<label>Title <input id="title" name="title" type="text"
				placeholder="Title" />
			</label>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<button type="submit">Submit</button>
		</div>
	</div>
</form>
