<?php

namespace App\Controller;

use App\DTO\RepositoryPage;
use App\Model\Error400;
use App\Service\ApiResponseSerializer;
use App\Service\Logger;
use App\Service\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends BaseAbstractController
{
    public function __construct(
        private ApiResponseSerializer $serializer,
        private Logger $logger,
        private Validator $validator
    ) {
    }

    protected function jsonResponse($data = null, array $groups = null): Response
    {
        if (is_null($data)) {
            return new JsonResponse();
        } else {
            return $this->serializer->serialize($data, $groups);
        }
    }

    protected function jsonFormErrorResponse(FormInterface $form): Response
    {
        $model = new Error400\Validation(Error400::VALIDATION);
        foreach ($form->getErrors() as $error) {
            $message = $error->getMessage();
            if ($message == Error400\Validation::EXTRA_FIELDS_MESSAGE) {
                $message .= ' ' . str_replace('"', '',
                    $error->getMessageParameters()['{{ extra_fields }}']
                );
            }

            $model->messages[] = $message;
        }
        foreach ($form as $child) {
            foreach ($child->getErrors() as $error) {
                $fieldMsg = new Error400\Validation\FieldMessage();
                $fieldMsg->field = $child->getName();
                $fieldMsg->message = $error->getMessage();
                $model->fieldMessages[] = $fieldMsg;
            }
        }

        $this->logger->logError400($model);

        $response = $this->serializer->serialize($model);
        $response->setStatusCode(400);

        return $response;
    }

    protected function jsonErrorResponse($code, string $message = ''): Response
    {
        $model = new Error400\General($code);
        $model->message = $message;

        $this->logger->logError400($model);

        $response = $this->serializer->serialize($model);
        $response->setStatusCode(400);

        return $response;
    }

    protected function submitForm(Request $request, $formClass, $entity): Form
    {
        $input = (array)json_decode($request->getContent(), true);

        return $this->validator->validateForm($formClass, $input, $entity);
    }
}
