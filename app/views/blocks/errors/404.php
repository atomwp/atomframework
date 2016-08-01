<?php if (!defined("AT_ROOT")) die('!!!'); ?>
<section class="not-found">
	<div class="at-container">
	<?php do_action('atom_after_container_open'); ?>
		<div class="at-container-inner at-404-page">
			<div class="at-page-not-found">
				<h2>
					<?php if(AT_Common::getMod('404_title')){
						echo esc_html(AT_Common::getMod('404_title'));
					}
					else{
						esc_html_e('Page you are looking is not found', 'atom' );
					} ?>
				</h2>
				<p>
					<?php if(AT_Common::getMod('404_text')){
						echo esc_html(AT_Common::getMod('404_text'));
					}
					else{
						esc_html_e('The page you are looking for does not exist. It may have been moved, or removed altogether. Perhaps you can return back to the site\'s homepage and see if you can find what you are looking for.', 'atom' );
					} ?>
				</p>
			</div>
		</div>
		<?php do_action('atom_before_container_close'); ?>
	</div>
</section>
