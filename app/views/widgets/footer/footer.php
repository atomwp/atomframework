<?php if (!defined("AT_ROOT")) die('!!!'); ?>
<?php AT_Notices::get_frontend_notice(); ?>

<?php atom_get_content_bottom_area(); ?>

</div> <!-- close div.content_inner -->
</div>  <!-- close div.content -->

<?php if (!isset($_REQUEST["ajax_req"]) || $_REQUEST["ajax_req"] != 'yes') { ?>
<footer <?php atom_class_attribute(atom_get_footer_classes(get_the_ID())); ?>>
	<div class="at-footer-inner clearfix">

		<?php

		if(AT_Common::getMod('show_footer_top') == 'yes') {
			atom_get_footer_top();
		}
		if(AT_Common::getMod('show_footer_bottom') == 'yes') {
			atom_get_footer_bottom();
		}
		?>

	</div>
</footer>
<?php } ?>

</div> <!-- close div.at-wrapper-inner  -->
</div> <!-- close div.at-wrapper -->
<?php wp_footer(); ?>
</body>
</html>
