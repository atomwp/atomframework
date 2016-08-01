<?php
if (!defined("AT_ROOT")) die('!!!');

class AT_MetaBox extends AT_Admin_Controller {
		
	protected $_block_id = 0;
	protected $_get_params = false;

	private $_meta_box;
	
	function __construct( $meta_box ) {
		if ( !is_admin() ) return;

		$this->view = new AT_View();
		$this->view->add_style( 'admin_style.css', AT_THEME_URI.'/assets/css/admin-ui.min.css');

		$this->view->add_script( 'theme-options', AT_ASSETS_URI.'/js/admin-ui.min.js', array('jquery'));
		// $this->view->add_script( 'admin-common', AT_THEME_URI.'/assets/js/common.js');
		$this->view->add_script( 'admin-options', AT_THEME_URI.'/assets/js/admin/options/options.js');
		$this->view->add_script( 'admin-options-metabox', AT_THEME_URI.'/assets/js/admin/options/metabox.js');
		parent::__construct();
		$this->_meta_box = $meta_box;
		
		add_action( 'admin_menu', array( &$this, 'add' ) );
		add_action( 'save_post', array( &$this, 'save' ) );
	}

	function add() {
		foreach ( $this->_meta_box['pages'] as $page ) {
			add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array( &$this, 'show' ), $page, $this->_meta_box['context'], $this->_meta_box['priority'] );
			add_filter('postbox_classes_page_at_templates_page_post', array( 'AT_Meta_Box', 'add_metabox_classes' ) );
			add_filter('postbox_classes_page_at_templates_page_news', array( 'AT_Meta_Box', 'add_metabox_classes' ) );
			add_filter('postbox_classes_page_at_templates_page_partners', array( 'AT_Meta_Box', 'add_metabox_classes' ) );
			add_filter('postbox_classes_page_at_templates_page_portfolio', array( 'AT_Meta_Box', 'add_metabox_classes' ) );
			add_filter('postbox_classes_page_at_templates_page_staff', array( 'AT_Meta_Box', 'add_metabox_classes' ) );
			add_filter('postbox_classes_page_at_templates_page_testimonials', array( 'AT_Meta_Box', 'add_metabox_classes' ) );
		}
	}

	public static function add_metabox_classes($classes) {
	    array_push($classes,' at_metabox_toggle at_hide_metabox');
	    return $classes;
	}

	function show() {
		global $post;
		foreach( $this->_meta_box['fields'] as $key => &$item ) {
			if (is_numeric($key)) continue;
			if ( !empty( $this->_group_name ) ) {
				$val = isset( $this->_group_item_values[$key] ) ? $this->_group_item_values[$key] : null;
			} else {
				$val = get_post_meta( $post->ID, $key, true );
			}
			// switch ( $key ) {
			// 	case '_model_id':
			// 		$manufacturer_id = $this->_meta_box['fields']['_manufacturer_id']['value'];
			// 		if ( !empty( $manufacturer_id ) && $manufacturer_id > 0 ) {
			// 			$reference_model = AT_Loader::get_instance()->model( 'reference_model' );
			// 			$item['items'] = $reference_model->get_data_for_options( 'get_models_by_manufacturer_id', $manufacturer_id );
			// 		}
			// 		unset( $manufacturer_id );
			// 		break;
			// 	case '_owner_id':
			// 		if ( !empty( $val ) ) {
			// 			$user_model = AT_Loader::get_instance()->model( 'user_model' );
			// 			$user_info = $user_model->get_user_by_id( $val );
			// 			if ($user_info) {
			// 				$item['title'] = $user_info['name'];
			// 				$item['description'] = $user_info['email'] . '<br/>' . ( !empty( $user_info['phone'] ) ? $user_info['phone'] . '<br/>' : '' ) . ( !empty( $user_info['phone_2'] ) ? $user_info['phone_2'] . '<br/>' : '' );
			// 			}
			// 		}
			// 		break;
			// 	case '_affiliate_id':
			// 		$owner_id = $this->_meta_box['fields']['_owner_id']['value'];
			// 		if ( !empty( $owner_id ) && $owner_id > 0 ) {
			// 			$user_model = AT_Loader::get_instance()->model( 'user_model' );
			// 			foreach ($user_model->get_dealer_affiliates( $owner_id ) as $key => $value) {
			// 				$item['items'][$value['id']] = $value['name'];
			// 			}
			// 		}
			// 		unset( $owner_id );
			// 		break;
			// }
			$item['value'] = empty( $val ) ? $item['default'] : $val;
		}

		$this->view->use_layout('content');
		$this->_parse_fields( $this->_meta_box['fields'] );
		$out = $this->view->render()->display( true );

		# Use nonce for verification
		$out .= '<input type="hidden" name="' . $this->_meta_box['id'] . '_meta_box_nonce" value="' . wp_create_nonce( basename(__FILE__) ) . '" />';
		
		echo $out;
	}
	
	function save( $post_id ) {
		# check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		if( empty( $_POST[$this->_meta_box['id'] . '_meta_box_nonce'] ) )
			return $post_id;
		
		# verify nonce
		if ( !wp_verify_nonce( $_POST[$this->_meta_box['id'] . '_meta_box_nonce'], basename(__FILE__) ) ) {
			return $post_id;
		}

		# check permisions
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		foreach( $this->_meta_box['fields'] as $key => &$item ) {
			if (is_numeric($key)) continue;
			if ( !empty( $this->_group_name ) ) {
				$val = isset( $this->_group_item_values[$key] ) ? $this->_group_item_values[$key] : null;
			} else {
				$val = get_post_meta( $post_id, $key, true );
			}
			$item['value'] = is_null( $val ) ? $item['default'] : $val;
			break;
		}

		if ( empty( $_POST[THEME_PREFIX . 'options'] ) ) {
			$_POST[THEME_PREFIX . 'options'] = array();
		}
		$save_values = $this->_save_fields( $this->_meta_box['fields'], $_POST[THEME_PREFIX . 'options'], true );

		foreach ($save_values as $key => $new) {
			if( $key == '_price' ) {
				$new = str_replace( array(',', ' '), array('', ''), $new );
			}
			$old = get_post_meta( $post_id, $key, true );
			if ( $new && $new != $old ) {
				update_post_meta( $post_id, $key, $new );
			//} elseif ('' == $new && $old) {
			} elseif (empty($new) && $old) {
				delete_post_meta( $post_id, $key, $old );
			}
		}
	}
	
}

?>
