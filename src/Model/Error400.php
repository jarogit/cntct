<?php

namespace App\Model;

use JMS\Serializer\Annotation as JMS;

class Error400
{
    const VALIDATION = 'validation-error';
	const ENTITY_NOT_FOUND = 'entity-not-found';

	/**
	 * @JMS\Type("string")
	 */
	private $code;

	final public function __construct(string $code)
    {
		$codes = (new \ReflectionClass(__CLASS__))->getConstants();
		if (!in_array($code, $codes)) {
			throw new \Exception(
				"Unknown error code {$code}. Please use constants of " . __CLASS__
			);
		}

		$this->code = $code;
	}

	public function getCode()
    {
		return $this->code;
	}
}
