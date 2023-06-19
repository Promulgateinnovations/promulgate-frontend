<?php

namespace Promulgate\Traits;


trait Twig
{

	protected
		$Twig;


	public function Twig()
	{
		static $object;

		if(!is_null($this->Twig) && !is_a($this->Twig, 'Twig')) {

			throw new \Exception('Object is not of QueryMapper or parent class');

		} elseif(is_null($this->Twig) && !is_null($object)) {
			$this->Twig = $object;
		}

		if(!is_a($this->Twig, 'Twig')) {
			$this->Twig = new \Twig();
		}

		if($object !== $this->Twig) {
			$object = $this->Twig;
		}

		return $this->Twig;
	}

}