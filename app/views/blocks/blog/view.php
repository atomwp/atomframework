<?php if (!defined("AT_ROOT")) die('!!!'); ?>
<div class="at-full-width">
	<?php do_action('atom_after_container_open'); ?>

	<div class="at-full-width-inner">
		<?php atom_get_blog(AT_Common::getMod('blog_list_type')); ?>
	</div>

	<?php do_action('atom_before_container_close'); ?>
</div>
