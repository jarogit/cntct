<?php

namespace App\Model\Error400;

use JMS\Serializer\Annotation as JMS;

class Validation extends \App\Model\Error400 {

	const EXTRA_FIELDS_MESSAGE = 'This form should not contain extra fields.';

	/**
	 * @JMS\Type("array<string>")
	 */
	public $messages = [];

	/**
	 * @JMS\Type("array<App\Model\Error400\Validation\FieldMessage>")
	 */
	public $fieldMessages = [];
}
