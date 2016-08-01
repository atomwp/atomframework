<?php
if (!defined("AT_ROOT")) die('!!!');

class AT_validation_rules {

	public $rules = array(

		'registration' => array(
			array(
				'field'   => 'name',
				'label'   => 'username',
				'rules'   => 'strip_tags|trim|min_length[3]|max_length[50]|required'
			),
			array(
				'field'   => 'email',
				'label'   => 'email',
				'rules'   => 'valid_email|strip_tags|trim|max_length[70]|email_exist|required'
			),
			array(
				'field'   => 'password',
				'label'   => 'password',
				'rules'   => 'required|min_length[5]'
			),
			array(
				'field'   => 'password_again',
				'label'   => 'repeat password',
				'rules'   => 'matches[password]|required'
			),
		),
		'recovery_password' => array(
			array(
				'field'   => 'email',
				'label'   => 'email',
				'rules'   => 'valid_email|strip_tags|trim|max_length[70]|required'
			),
		),
		'recovery_password_form' => array(
			array(
				'field'   => 'new_password',
				'label'   => 'password',
				'rules'   => 'required|min_length[5]'
			),
			array(
				'field'   => 'password_again',
				'label'   => 'repeat password',
				'rules'   => 'matches[new_password]|required'
			),
		),
		'login' => array(
			array(
				'field'   => 'email',
				'label'   => 'email',
				'rules'   => 'valid_email|strip_tags|trim|max_length[70]|required'
			),
			array(
				'field'   => 'password',
				'label'   => 'password',
				'rules'   => 'required|min_length[5]'
			),
		),
		'add_offer' => array(
			array(
				'field'   => 'fullname',
				'label'   => 'fullname',
				'rules'   => 'strip_tags|trim|min_length[3]|max_length[50]|required'
			),
			array(
				'field'   => 'email',
				'label'   => 'email',
				'rules'   => 'valid_email|strip_tags|trim|max_length[70]|required'
			),
			array(
				'field'   => 'offer_details',
				'label'   => 'offer details',
				'rules'   => 'strip_tags|trim'
			),
		),
		'settings_user' => array(
			array(
				'field'   => 'name',
				'label'   => 'name',
				'rules'   => 'strip_tags|trim|min_length[3]|max_length[50]|required'
			),
			array(
				'field'   => 'phone_1',
				'label'   => 'phone 1',
				'rules'   => 'strip_tags|trim|min_length[6]|max_length[15]'
			),
			array(
				'field'   => 'phone_2',
				'label'   => 'phone 2',
				'rules'   => 'strip_tags|trim|min_length[6]|max_length[15]'
			),
			// array(
			// 	'field'   => 'hide_number_ads',
			// 	'label'   => 'hide number of my ads',
			// 	'rules'   => 'checkbox'
			// ),
		),
		'settings_dealer' => array(
			array(
				'field'   => 'name',
				'label'   => 'dealer name',
				'rules'   => 'strip_tags|trim|min_length[3]|max_length[50]|required'
			),
			array(
				'field'   => 'about',
				'label'   => 'dealer about',
				'rules'   => 'strip_tags|trim'
			),
			array(
				'field'   => 'layout',
				'label'   => 'dealer layout',
				'rules'   => 'strip_tags|trim|required'
			),
			array(
				'field'   => 'per_page',
				'label'   => 'dealer per page',
				'rules'   => 'min_length[1]|max_length[2]|numeric|trim|required'
			),
		),
		'affiliate' => array(
			array(
				'field'   => 'name',
				'label'   => 'Affiliate name',
				'rules'   => 'strip_tags|trim|min_length[3]|max_length[50]|required'
			),
			array(
				'field'   => 'email',
				'label'   => 'Affiliate email',
				'rules'   => 'valid_email|strip_tags|trim|max_length[70]|required'
			),
			array(
				'field'   => 'phone_1',
				'label'   => 'Affiliate phone 1',
				'rules'   => 'strip_tags|trim|min_length[6]|max_length[15]'
			),
			array(
				'field'   => 'phone_2',
				'label'   => 'Affiliate phone 2',
				'rules'   => 'strip_tags|trim|min_length[6]|max_length[15]'
			),
			array(
				'field'   => 'region_id',
				'label'   => 'Affiliate region',
				'rules'   => 'numeric|trim'
			),
			array(
				'field'   => 'adress',
				'label'   => 'Affiliate adress',
				'rules'   => 'strip_tags|trim'
			),
			array(
				'field'   => 'schedule[monday]',
				'label'   => 'Schedule monday',
				'rules'   => 'strip_tags|trim'
			),
			array(
				'field'   => 'schedule[tuesday]',
				'label'   => 'Schedule tuesday',
				'rules'   => 'strip_tags|trim'
			),
			array(
				'field'   => 'schedule[wednesday]',
				'label'   => 'Schedule wednesday',
				'rules'   => 'strip_tags|trim'
			),
			array(
				'field'   => 'schedule[thursday]',
				'label'   => 'Schedule thursday',
				'rules'   => 'strip_tags|trim'
			),array(
				'field'   => 'schedule[friday]',
				'label'   => 'Schedule friday',
				'rules'   => 'strip_tags|trim'
			),
			array(
				'field'   => 'schedule[saturday]',
				'label'   => 'Schedule saturday',
				'rules'   => 'strip_tags|trim'
			),
			array(
				'field'   => 'schedule[sunday]',
				'label'   => 'Schedule sunday',
				'rules'   => 'strip_tags|trim'
			),
		),
	);

}
