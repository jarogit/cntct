<?php

namespace App\Service;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private ValidatorInterface $validator
    ) {
    }

    public function validateForm(string $formClass, array $data, $entity): Form
    {
        $form = $this->formFactory->create($formClass, $entity);
        $form->submit($data, false);
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
