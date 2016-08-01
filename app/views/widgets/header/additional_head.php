<?php if (!defined("AT_ROOT")) die('!!!'); ?>
  <?php if($this->get_option( 'custom_js' ) != '' ) { ?>
  	<script type='text/javascript'>
	/* <![CDATA[ */
	<?php echo stripslashes( $this->get_option( 'custom_js' ) ); ?>
	/* ]]> */
	</script>
  <?php } ?>
  <?php if($this->get_option( 'custom_css' ) != '' ) { ?>
  	<style type="text/css" media="screen">
	<?php echo stripslashes( $this->get_option( 'custom_css' ) ); ?>
	</style>
  <?php } ?>
  <?php if($this->get_option( 'google_analytics' ) != '' ) { ?>
	<script type="text/javascript">var _gaq = _gaq || [];_gaq.push(['_setAccount', '<?php echo stripslashes( $this->get_option( 'google_analytics' ) ); ?>']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();</script>
  <?php } ?>
  <?php if($this->get_option( 'custom_header_code' ) != '' ) { ?>
	<?php echo stripslashes( $this->get_option( 'custom_header_code' ) ); ?>
  <?php } ?>
