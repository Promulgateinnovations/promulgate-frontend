<?php

namespace Promulgate\Models;

use Promulgate\Core\Api;

/**
 * TODO: Implement once API is available
 * Class BaseModel
 *
 * @package Promulgate\Models
 */
class BaseModel extends Api
{

	/**
	 * BaseModel constructor.
	 */
	public function __construct()
	{
		parent::__construct([
			'api_source' => 'promulgate',
		]);
	}

}