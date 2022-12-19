<?php

namespace App\Model;

use JMS\Serializer\Annotation as JMS;

class Exception500 {

	/**
	 * @JMS\Type("string")
	 */
	public $message;
}
