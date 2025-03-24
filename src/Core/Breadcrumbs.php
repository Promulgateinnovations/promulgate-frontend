<?php


namespace Promulgate\Core;

class Breadcrumbs
{

	private
		/**
		 * Associative array [key] => [url, icon, title]
		 *
		 * @var array
		 */
		$breadcrumbs = array();


	public function __construct()
	{
		$this->addHomeLink();
	}


	/**
	 * Adding default home link to the beginning of the breadcrumbs
	 *
	 * @return $this
	 */
	private function addHomeLink(): self
	{
		array_unshift($this->breadcrumbs, [
			'url'   => url('/homepage'),
			'icon'  => 'fa fa-home',
			'title' => 'Home',
		]);

		return $this;
	}


	/**
	 * Adding a breadcrumb set to the end of the current breadcrumb list.
	 *
	 * @param array  $breadcrumb
	 *
	 * @return $this
	 */
	public function add(array $breadcrumb): self
	{
		// Title OR Icon is mandatory
		if(!isset($breadcrumb['title']) && !isset($breadcrumb['icon'])) {
			return $this;
		}

		// URL is optional but as a fallback we consider home
		if(!isset($breadcrumb['url'])) {
			 $breadcrumb['url'] =false;
		}

		if(!isset($breadcrumb['icon'])) {
			$breadcrumb['icon'] = false;
		}

		if(!isset($breadcrumb['title'])) {
			$breadcrumb['title'] = "";
		}

		$breadcrumb['title'] = ucwords($breadcrumb['title']);

		$this->breadcrumbs[] = $breadcrumb;
		return $this;
	}


	/**
	 * @return array
	 */
	public function get(): array
	{
		return $this->breadcrumbs;
	}
}