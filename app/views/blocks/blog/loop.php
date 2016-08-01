<?php if (!defined("AT_ROOT")) die('!!!'); ?>
<div class="at-full-width">
	<?php do_action('atom_after_container_open'); ?>

	<div class="at-full-width-inner">

<?php if (!defined("AT_ROOT")) die('!!!'); ?>
<section class="portfolio">
	<div class="container">
		<?php if ( is_page_template() ) echo the_content(); ?>
		<?php 
		$terms = array();

		if ( count( $terms ) > 0 ) {
			$out = '<ul class="portfolio-list list-inline ">
					<li ' . ($term_active == '' ? 'class="active"' : '' ) . '><a href="' . get_post_type_archive_link( 'portfolio' ) . '">' .esc_html__( 'All', 'atom' ) . '</a></li>';
			foreach ($terms as $key => $term) {
				$term = sanitize_term( $term, 'portfolio_category' );
				$term_link = get_term_link( $term, 'portfolio_category' );
				if ( is_wp_error( $term_link ) ) {
			        continue;
			    }
				$out .= '<li ' . ( $term_active == $term->slug ? 'class="active"' : '' ) . '><a href="' . esc_url( $term_link ) .'">' . $term->name . '</a></li>';
			}
			$out .= '</ul>';
			echo $out;
		}
?>
	<div id="container-portfolio">
		<?php echo $content; ?>
	</div>
		<?php if( isset( $block['pagination'] ) ) echo $block['pagination']; ?>
	</div>
</section>


		<?php atom_get_blog(AT_Common::getMod('blog_list_type')); ?>
	</div>

	<?php do_action('atom_before_container_close'); ?>
</div>
