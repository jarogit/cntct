<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Logger
{
	public function __construct(
        private LoggerInterface $systemLogger,
        private RequestStack $requestStack
    ) {
	}

	public function logError400(\App\Model\Error400 $error)
    {
		$this->systemLogger->debug(
            $error->getCode() . ' ' .
            json_encode(get_object_vars($error)) . "\n" .
            $this->requestStack->getCurrentRequest()
        );
	}

	public function logException(\Throwable $e)
    {
		$this->systemLogger->error(
            $e->getMessage() . "\n" .
            '## ' . $e->getFile() . "(" . $e->getLine() . ")\n" .
            $e->getTraceAsString()
        );
	}
}
