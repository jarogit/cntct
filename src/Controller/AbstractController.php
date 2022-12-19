<?php

namespace App\Controller;

use App\DTO\RepositoryPage;
use App\Model\Error400;
use App\Service\Logger;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractController extends BaseAbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private Logger $logger
    ) {
    }

    protected function jsonResponse(
        $data = null, array $groups = null, array $extra = null
    ): Response {
        $context = $this->getSerializerContext();
        if ($groups) {
            $context->setGroups($groups);
        }

        if (is_null($data)) {
            return new JsonResponse();
        } else {
            if ($extra) {
                $data = array_merge(['data' => $data], $extra);
            }
            $response = new Response(
                $this->serializer->serialize($data, 'json', $context)
            );
            $response->headers->set('Content-Type', 'application/json');
            return $response;
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

        $response = new Response(
            $this->serializer->serialize($model, 'json', $this->getSerializerContext()),
            400
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    protected function jsonErrorResponse($code, string $message = ''): Response {
        $model = new Error400\General($code);
        $model->message = $message;

        $this->logger->logError400($model);

        $response = new Response(
            $this->serializer->serialize($model, 'json', $this->getSerializerContext()),
            400
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function getSerializerContext() {
        return (new SerializationContext)->setSerializeNull(true);
    }

    protected function submitForm(Request $request, $formClass, $entity)
    {
        $input = (array)json_decode($request->getContent(), true);

        /** @var Form */
        $form = $this->createForm($formClass, $entity);
        $form->submit($input, false);

        if ($form->isValid()) {
            $errorGroups = [
                'form' => $this->validator->validate($form->getIterator()),
                'entity' => $this->validator->validate($entity),
            ];
            foreach ($errorGroups as $groupName => $errors) {
                foreach ($errors as $error) {
                    $fieldName = $error->getPropertyPath();
                    if ($groupName == 'form') {
                        $fieldName = preg_replace('~\[(.*?)\].*~', '$1', $fieldName);
                    }
                    if ($form->has($fieldName)) {
                        $form->get($fieldName)->addError(
                            new FormError($error->getMessage())
                        );
                    } else {
                        $form->addError(
                            new FormError($fieldName . ': ' . $error->getMessage())
                        );
                    }
                }
            }
        }

        return $form;
    }
}
