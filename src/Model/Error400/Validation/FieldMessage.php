<?php

namespace App\Model\Error400\Validation;

use JMS\Serializer\Annotation as JMS;

class FieldMessage {

	/**
	 * @JMS\Type("string")
	 */
	public $field;

	/**
	 * @JMS\Type("string")
	 */
	public $message;
}
