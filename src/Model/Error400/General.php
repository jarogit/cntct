<?php

namespace App\Model\Error400;

use JMS\Serializer\Annotation as JMS;

class General extends \App\Model\Error400 {

	/**
	 * @JMS\Type("string")
	 */
	public $message;
}
