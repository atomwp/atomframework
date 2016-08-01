<?php $this->add_script( 'jquery.countdown', 'assets/js/jquery/jquery.countdown.js', array( 'jquery' ) ); ?>
<?php $this->add_script( 'underconstruction', 'assets/js/underconstruction.js', array( 'jquery', THEME_PREFIX . 'jquery.countdown' ) ); ?>
<!-- wrap  start -->
<div id="wrap">
	<div class="w1">
		<!-- header  start -->
			<header id="header">
				<div class="container">
					<strong class="logo logo-dark">
						<a href="#">
							<img src="<?php  echo AT_Common::static_url('assets/images/front/logo-dark.png'); ?>" alt="image" width="132" height="176" />
						</a>
					</strong>
				</div>
			</header>
		<!-- header  end 
		     main  start -->
		<div id="main">
			<div class="container">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<!--  construction start -->
						<section class="construction">
							<h1><?php esc_html_e( 'We are currently under construction', 'atom' ); ?></h1>
							<?php if( count( $counter_options ) > 0 ) { ?>
							<?php $this->add_localize_script( 'underconstruction', 'counter_options', $counter_options ); ?>
								<span class="text-time"><?php esc_html_e( 'Our estimated time before launch:', 'atom' ); ?></span>
								<ul id="counter" class="list-calendar list-inline">
									<li><span class="text-number"><?php echo $counter_options['days'] ?></span> <?php esc_html_e( 'days', 'atom' ); ?></li>
									<li><span class="text-number"><?php echo $counter_options['hours'] ?></span> <?php esc_html_e( 'hours', 'atom' ); ?></li>
									<li><span class="text-number"><?php echo $counter_options['minutes'] ?></span> <?php esc_html_e( 'minutes', 'atom' ); ?></li>
									<li><span class="text-number"><?php echo $counter_options['seconds'] ?></span> <?php esc_html_e( 'seconds', 'atom' ); ?></li>
								</ul>
							<?php } ?>
							<div class="row">
								<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
									<div class="form-subscribe">
										<form action="#">
											<fieldset>
												<ul class="tools tools-gray pull-left">
													<?php
													foreach ($this->get_option( 'sociable', array() ) as $key => $item) {
														$sociable_link = ( !empty( $item['link'] ) ) ? $item['link'] : '#';
														echo '<li><a href="' . esc_url( $sociable_link ) . '"><span class="' . esc_attr($item['icon']) . '"></span></a></li>';
													}
													?>
												</ul>
												<div class="field-text field-middle field-blue pull-left">
													<input type="email" class="form-control add_subscribe_input" placeholder="Enter your email to subscribe" />
												</div>
												<div class="hold-btn pull-left">
													<button class="button add_subscribe_submit" type="submit"><?php esc_html_e( 'subscribe', 'atom' ); ?></button>
												</div>
											</fieldset>
										</form>
									</div>
								</div>
							</div>
						</section>
						<!--  construction end -->
					</div>
				</div>
			</div>
		</div>
		<!-- main  end -->
	</div>
</div>
<!-- wrap  end -->
