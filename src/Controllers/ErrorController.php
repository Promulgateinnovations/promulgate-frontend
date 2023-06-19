<?php


namespace Promulgate\Controllers;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ErrorController
 *
 * @package Promulgate\Controllers
 */
class ErrorController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * 404 Page
	 *
	 * @return string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
	public function showNotFound()
	{
		$this->setViewData('error.html',
			[
				'error_type'               => '404',
				'page_title'               => "404 Not Found",
				'hide_side_menu' => true,
			]
		);

		return $this->view();
	}


	public function processAjax()
	{
	}


	protected function setWebContext()
	{
		// TODO:
	}

}