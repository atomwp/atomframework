<?php
if (!defined("AT_ROOT")) die('!!!');
abstract class AT_Controller {

	public $view = null;
	public $load = null;
	public $core = null;

	public function __construct() {
		$this->core = AT_Core::get_instance();
		// Feature needed for theme customization
		/*		
		if ( !$this->core->get_option( 'theme_is_activated', false ) ) {
			
			// AT_Notices::set_frontend_notice(__( 'You almost ready to sell! Please click here to configure theme and complete installation steps.' , 'atom' ), $class = 'notice');
		}
		*/
		$this->uri = AT_Router::get_instance();

		if ( $this->uri->is_ajax_request() && isset( $_POST ) && isset( $_POST['additional_request'] ) ) {
			$this->_additional_request();
		}

		if ( $this->uri->get_method() != 'show_underconstruction' &&  $this->core->get_option( 'status_site', 'production' ) == 'underconstruction' ) {
			AT_Core::show_underconstruction();
		}

		$this->view = $this->core->view;
		$this->load = AT_Loader::get_instance();
		$this->session = AT_Session::get_instance();
		$this->registry = AT_Registry::get_instance();

		$this->load->library('breadcrumbs');
		$this->breadcrumbs = AT_Breadcrumbs::get_instance();

		$validation_rules = $this->load->helper('validation_rules', true);
		$this->validation = $this->load->library('form_validation', true, $validation_rules->rules);

		$this->session->sess_create( array() );

		//$this->validation->set_rules();

		// if ( AT_Common::is_user_logged()) {
		// 	$user_model = $this->load->model('user_model');
		// 	$this->registry->set( 'user_info', $user_model->get_user_by_id( AT_Common::get_logged_user_id() ) );
		// }
	}

	private function _additional_request() {

		switch ($_POST['additional_request']) {
			case 'add_subscribe':
				if ( !empty( $_POST['email'] ) ) {
					$subscribe_model = AT_Loader::get_instance()->model( 'subscribe_model' );
					$subscribe_model->add_subscribe( $_POST['email'] );
					$response = array(
						'message' =>esc_html__( 'Your email added successfuly.', 'atom' ),
						'status' => 'OK'
					);
				} else {
					$response = array(
						'message' =>esc_html__( 'Enter the mail!', 'atom' ),
						'status' => 'ERROR'
					);
				}
	 			$this->core->view->add_json( $response )->display();
				break;
			case 'contact_form':
				if (count($_POST)) {
					$form_session = AT_Session::get_instance()->userdata($_POST['at_form_id']);

			    		$validation = AT_Loader::get_instance()->library('form_validation', true);
					$validation->set_rules('at_contact_form_email', 'Email', 'required|valid_email');
					$validation->set_rules('at_contact_form_message', 'Message', 'required');
					if ( $form_session['captcha'] == true && $form_session['response'] != $_POST['at_contact_form_captcha'] ) {
						$validation->set_rules('at_contact_form_captcha', 'Captcha', 'required');
						AT_Session::get_instance()->unset_userdata($_POST['at_form_id']);
					}

					if ($validation->run() != FALSE) {
						$content_mail = '';
						if ( isset($_POST['at_contact_form_name'] ) ) {
							$content_mail .= '<br/><b>' .esc_html__( 'Name',  'atom' ) .  ':</b> ' . $_POST['at_contact_form_name'];
						}
						if ( isset($_POST['at_contact_form_phone'] ) ) {
							$content_mail .= '<br/><b>' .esc_html__( 'Phone',  'atom' ) .  ':</b> ' . $_POST['at_contact_form_phone'];
						}
						if ( isset($_POST['at_contact_form_subject'] ) ) {
							$content_mail .= '<br/><b>' .esc_html__( 'Subject',  'atom' ) .  ':</b> ' . $_POST['at_contact_form_subject'];
						}
						if ( isset($_POST['at_contact_form_address'] ) ) {
							$content_mail .= '<br/><b>' .esc_html__( 'Address',  'atom' ) .  ':</b> ' . $_POST['at_contact_form_address'];
						}

						$content_mail .= '<br/><b>' .esc_html__( 'Email',  'atom' ) .  ':</b> ' . $_POST['at_contact_form_email'] . '';
						$content_mail .= '<br/><b>' .esc_html__( 'Message',  'atom' ) .  ':</b><br/>' . nl2br ($_POST['at_contact_form_message'] ) . '';
							
						if ( isset( $_POST['at_form_id'] ) ) {
							$email = $form_session['recipient'];

							$mail_model = AT_Loader::get_instance()->model( 'mail_model' );
							$mail_model->send( 'template_mail_contact_form', $email, array( 'content' => $content_mail ));
						}
						$response = array(
							'message' =>esc_html__( 'Message sent successfuly. We will respond on it as soon as possible. Please note, somtime response may take few hours.', 'atom' ),
							'override' => $form_session['captcha'],
							'status' => 'OK'
						);
					} else {
						$errors = $validation->get_errors();
						foreach ($errors as $key => &$value) {
							$value = $value;
						}
						$response = array(
							'message' => implode(' ', $errors),
							'override' => false,
							'status' => 'ERROR'
						);
					}
					$this->core->view->add_json( $response )->display();
				}
				break;
		}
	}
}
?>
