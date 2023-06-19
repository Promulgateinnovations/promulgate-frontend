<?php


namespace Promulgate\Controllers;

use Josantonius\Session\Session;
use Promulgate\Core\Config;
use Promulgate\Core\GoogleAPIClient;
use Promulgate\Models\IndexModel;
use Promulgate\Models\AgencyModel;


/**
 * Class IndexController
 *
 * @package Promulgate\Controllers
 */
class IndexController extends BaseController
{

	private $indexModel;
	private $agencyModel;


	public function __construct()
	{
		parent::__construct();
		$this->indexModel = new IndexModel();
		$this->agencyModel = new AgencyModel();
	}


	public function showHome()
	{
		$this->setViewData('home.html',
			[
				'page_title' => "Home",
			]
		);

	}

	
	public function showHomePage()
	{

		$agencyId   = Session::get('agency', 'id');
		$organiztionId = Session::get('organization', 'id');
		$userId = Session::get('user', 'id');
 		$list_of_organizations = [];

		$all_organizations = $this->agencyModel->getListOfOrganizations($agencyId, $userId)['body'];

		if(getValue('status', $all_organizations) == 'success') {
			$list_of_organizations = $all_organizations['data'];
		};

		$this->setViewData('home_page.html',
			[
				'form_action'            => url('agency_ajax'),
				'organizations_list' => $list_of_organizations,
				'page_title' => "showHomePage",
				'hide_side_menu' => true,
				'current_organizationId'=> $organiztionId,
			]
		);

	}


	public function showLogin()
	{
		$this->setViewData('login.html', [
				'form_action'            => url('login_ajax'),
				'page_title'             => "Login",
				'hide_side_menu'         => true,
				'plugins_google'         => true,
				'GOOGLE_OAUTH_CLIENT_ID' => env('GOOGLE_OAUTH_CLIENT_ID'),
			]
		);

	}


	public function showLogout()
	{
		Session::destroy('user');
		Session::destroy('organization');
		Session::destroy('agency');


		Session::set('REDIRECT_MESSAGES', [
			[
				'type'    => 'success',
				'message' => 'You are logged out',
			],
		]);

		session_write_close();
		redirect(url('user_login', []));

	}


	public function showPrivacy()
	{

		$this->setViewData('privacy_policy.html', [
				'page_title'     => "Privacy Policy",
				'hide_side_menu' => true,
			]
		);

	}
	
	public function showTerms()
	{

		$this->setViewData('terms_and_conditions.html', [
				'page_title'     => "Terms & Conditions",
				'hide_side_menu' => true,
			]
		);

	}

	public function processAjax()
	{
		$all_input                = input()->all();
		$all_input['form_source'] = $all_input['form_source'] ?? "";

		switch ($all_input['form_source']) {

			case 'login' :
				$this->processLogin($all_input);
				break;

			default:
				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 100,
						'message' => 'Invalid data',
					],
				]);
				break;
		}
	}


	protected function setWebContext()
	{
		// TODO: Implement setWebContext() method.
	}


	private function processLogin($all_input)
	{

		$login_type    = $all_input['login_type'] ?? false;
		$process_login = false;
		$message_type  = 'toast'; // Show toast message OR error in the form
		$user_details  = [];

		// If login type is supported
		if(isset(Config::LOGIN_TYPES[$login_type])) {

			$login_provider = $all_input['login_provider'] ?? false;

			switch ($login_provider) {

				case Config::LOGIN_TYPES['normal']['self'] :

					$email        = trim($all_input['email']);
					$user_details = $provider_data = [
						'email'    => $email,
						'password' => $all_input['password'],
					];

					$message_type  = false;
					$process_login = true;
					break;

				case Config::LOGIN_TYPES['social']['google'] :

					$GoogleAPIClient = new GoogleAPIClient();
					$provider_data   = $all_input['provider_data'];

					if($provider_data['clientId'] == $GoogleAPIClient->client_id) {

						$user_details_from_google = $GoogleAPIClient->verifyUserCredentialsValidToken($provider_data['credential']);

						if($user_details_from_google) {

							$process_login = true;
							$user_details  = [
								'email' => $user_details_from_google['email'],
								'name'  => $user_details_from_google['name'],
							];

						} else {
							$message = "Could not get correct data from Google";
						}

					} else {
						$message = "Google Client details are wrong";
					}
					break;

			}

			if($process_login) {

				$user_login_data = $this->indexModel->login($user_details['email'], $user_details)['body'];

				if(getValue('status', $user_login_data) != 'success') {

					response()->json([
						'status' => false,
						'error'  => [
							'code'         => 100,
							'message'      => $message ?? 'Login could not be processed',
							'message_type' => $message_type,
						],
					]);

				} else {

					$user_login_data = $user_login_data['data'];

					$email_name = explode('@', $user_details['email'])[0];

					$role_id   = $user_login_data['role'][0]['roleRoleId'] ?? 0;
					$role_name = $role_id ? array_flip(Config::USER_ROLES)[$role_id] : "";
					Session::set('user', [
						'id'       => $user_login_data['userId'],
						'email'    => $user_details['email'],
						'name'     => ucwords($email_name),
						'username' => strtolower($email_name),
						'role'     => $role_name,
					]);

					if($user_login_data['orgId']) {
						Session::set('organization', [
							'id' => $user_login_data['orgId'],
						]);
					}
					if($user_login_data['agencyId']) {
						Session::set('agency', [
							'id' => $user_login_data['agencyId'],
						]);
					}

					response()->json([
						'status' => true,
						'data'   =>
							[
								'message' => 'You are successfully logged in',
								'extra'   => [
									'next_screen' => url('homepage'),
								],
							],
					]);

				}

			} else {

				response()->json([
					'status' => false,
					'error'  => [
						'code'         => 100,
						'message'      => $message ?? 'Login could not be processed',
						'message_type' => $message_type,
					],
				]);
			}

		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'         => 100,
					'message'      => 'Login is not supported',
					'message_type' => $message_type,
				],
			]);

		}

	}
}