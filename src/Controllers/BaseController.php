<?php

namespace Promulgate\Controllers;


use Josantonius\Session\Session;
use Promulgate\Core\Breadcrumbs;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class BaseController
 *
 * @package Promulgate\Controllers
 */
abstract class BaseController
{

	/**
	 * Inside view if we have module wise view then
	 *
	 * @var string
	 */
	protected $view_sub_directory_path;
	/**
	 * @var Breadcrumbs
	 */
	protected $Breadcrumbs;
	protected $context;
	private   $Twig;
	private   $template_file;
	private   $template_placeholders;


	/**
	 * BaseController constructor.
	 *
	 * @param array  $params
	 */
	public function __construct($params = [])
	{
		$this->setContext($params);
		$this->prepareContextEnvironment();
	}


	// Sets context based on request


	/**
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public function __destruct()
	{

		if($this->context === 'WEB') {
			// For $request->setRewriteCallback('ErrorController@showNotFound');
			// We are getting 2 times same output
			if($this->template_file != 'error.html') {
				print $this->view();
			}
		}
		exit;
	}


	abstract protected function setWebContext();


	/**
	 * @param $template_name
	 * @param $template_placeholders
	 */
	protected function setViewData($template_name, $template_placeholders): void
	{
		$this->setTemplateFile($template_name);
		$this->setTemplatePlaceholders($template_placeholders);

	}


	/**
	 * @return string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	protected function view(): string
	{
		$all_placeholders                  = $this->getAllViewPlaceholders();
		$all_placeholders['notifications'] = json_encode($all_placeholders['notifications']);
		$base_template                     = $all_placeholders['base_template'] ?? "index.html";

		return $this->Twig->render($base_template, $all_placeholders);
	}


	/**
	 * @return mixed
	 */
	abstract protected function processAjax();


	private function setContext(array $params = [])
	{
		$has_custom_context = isset($params['context']) ? $params['context'] : false;

		$this->context = !$has_custom_context
			? ((request()->isAjax() && input()->all(['from_ajax'])['from_ajax'] == true)
				? 'AJAX'
				: 'WEB')
			: $has_custom_context;
	}


	private function prepareContextEnvironment()
	{
		switch ($this->context) {

			case 'WEB' :

				// Initialise template engine
				$twig_loader = new FilesystemLoader(VIEWS_BASE_DIR);
				$this->Twig  = new Environment($twig_loader);

				// Add Global placeholders
				$this->Twig->addGlobal('site_name', env('SITE_NAME'));
				$this->Twig->addGlobal('site_version', md5(env('SITE_VERSION')));
				$this->Twig->addGlobal('css_path', env('CSS_PATH'));
				$this->Twig->addGlobal('js_path', env('JS_PATH'));
				$this->Twig->addGlobal('images_path', env('IMAGES_PATH'));
				$this->Twig->addGlobal('current_url_name', get_loaded_route_name());
				$this->Twig->addGlobal('page_title', env('SITE_NAME'));
				$this->Twig->addGlobal('meta_robots', "noindex, nofollow");
				$this->Twig->addGlobal('form_action', url()->getPath());
				$this->Twig->addGlobal('logged_in_user', Session::get('user'));
				$this->Twig->addGlobal('user_organization', Session::get('organization'));
				$this->Twig->addGlobal('user_agency', Session::get('agency'));
				$this->Twig->addGlobal('hide_side_menu', false);

				$this->Twig->addFilter(new TwigFilter('unescape', array(
					$this, function($value)
					{
						return html_entity_decode($value);
					},
				)));

				// Add functions needed for accessing them in all templates
				$url_function = new TwigFunction('url', function(?string $name = NULL, $parameters = NULL, ?array $getParams = NULL)
				{
					return url($name, $parameters, $getParams);
				});

				// Add functions needed for accessing them in all templates
				$absolute_url_function = new TwigFunction('absolute_url', function(?string $name = NULL, $parameters = NULL, ?array $getParams = NULL)
				{
					return url($name, $parameters, $getParams)->getAbsoluteUrl();
				});

				// Add functions needed for accessing them in all templates
				$date_function = new TwigFunction('get_date', function(?string $date = NULL, string $type)
				{
					return $date ? get_date($date, $type) : "";
				});

				$get_session_function = new TwigFunction('get_session', function(?string $key = NULL, string $second_key = NULL)
				{
					return Session::get($key, $second_key);
				});

				$this->Twig->addFunction($url_function);
				$this->Twig->addFunction($absolute_url_function);
				$this->Twig->addFunction($date_function);
				$this->Twig->addFunction($get_session_function);

				# is Use title filter instead seems its working
				//$this->Twig->addFilter(new \Twig\TwigFilter('ucwords', 'ucwords'));
				$this->Twig->addFilter(new TwigFilter('getFirstCharactersFromString', 'getFirstCharactersFromString'));

				// Default Views folder that's why empty
				$this->view_sub_directory_path = "";
				$this->Breadcrumbs             = new Breadcrumbs();

				$this->setWebContext();
				break;

			case 'AJAX' :

				;
				break;
		}
	}


	/**
	 * @param string  $template_file_name
	 */
	private function setTemplateFile(string $template_file_name): void
	{
		$this->template_file = $template_file_name;
	}


	/**
	 * @param array  $template_placeholders
	 */
	private function setTemplatePlaceholders(array $template_placeholders): void
	{
		$this->template_placeholders = $template_placeholders ?: [];
	}


	private function getTemplatePlaceholders(): array
	{
		return $this->template_placeholders ?? [];
	}


	/**
	 * @return array
	 */
	private function getAllViewPlaceholders(): array
	{

		return array_merge(
			$this->getTemplatePlaceholders(),
			[
				'content_template_file'         => $this->view_sub_directory_path.$this->template_file,
				'content_template_placeholders' => $this->template_placeholders,
			] + [
				'breadcrumbs' => $this->Breadcrumbs->get(),
			] + [
				'notifications' => Session::pull('REDIRECT_MESSAGES'),
			]
			+ [
				'allowed_to_debug' => ALLOWED_TO_DEBUG,
			] + [
				'debug_api_response_errors' => api_errors([], true),
			] + [
				'debug_api_logs' => api_log(true),
			] + [
				'debug_session' => Session::get() ?? [],
			]
		);

	}
}
