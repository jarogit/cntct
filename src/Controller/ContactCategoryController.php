<?php

namespace App\Controller;

use App\Entity\ContactCategory;
use App\Form;
use App\Model\Error400;
use App\Model\Exception500;
use App\Repository\ContactCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ContactCategoryController extends AbstractController
{
    #[Route('/contact/category', name: 'contact_category_index', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Seznam skupin kontaktů",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=ContactCategory::class, groups={"default"}))
     *    )
     * )
     * @OA\Response(response=500, description="Exception", @Model(type=Exception500::class))
     */
    public function index(ContactCategoryRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->jsonResponse(
            ['total' => count($categories), 'data' => $categories],
            ['default']
        );
    }

    #[Route('/contact/category', name: 'contact_category_create', methods: ['POST'])]
    /**
     * @OA\RequestBody(@Model(type=Form\ContactCategoryForm::class))
     * @OA\Response(
     *      response=200,
     *      description="Successful",
     *      @Model(type=ContactCategory::class, groups={"default"})
     * )
     * @OA\Response(response=400, description="Error", @Model(type=Error400\General::class))
     * @OA\Response(response=500, description="Exception", @Model(type=Exception500::class))
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $category = new ContactCategory();
        $form = $this->submitForm($request, Form\ContactCategoryForm::class, $category);
        if (!$form->isValid()) {
            return $this->jsonFormErrorResponse($form);
        }

        $em->persist($category);
        $em->flush();

        return $this->jsonResponse($category, ['default']);
    }

    #[Route('/contact/category/{id}', name: 'contact_category_read', methods: ['GET'])]
    /**
     * @OA\Response(
     *      response=200,
     *      description="Successful",
     *      @Model(type=ContactCategory::class, groups={"default"})
     * )
     * @OA\Response(response=400, description="Error", @Model(type=Error400\General::class))
     * @OA\Response(response=500, description="Exception", @Model(type=Exception500::class))
     */
    public function read(int $id, ContactCategoryRepository $repository): Response
    {
        $category = $repository->find($id);
        if (!$category) {
            return $this->jsonErrorResponse(Error400::ENTITY_NOT_FOUND);
        }

        return $this->jsonResponse($category, ['default']);
    }

    #[Route('/contact/category/{id}', name: 'contact_category_update', methods: ['PUT'])]
    /**
     * @OA\RequestBody(@Model(type=Form\ContactCategoryForm::class))
     * @OA\Response(
     *      response=200,
     *      description="Successful",
     *      @Model(type=ContactCategory::class, groups={"default"})
     * )
     * @OA\Response(response=400, description="Error", @Model(type=Error400\General::class))
     * @OA\Response(response=500, description="Exception", @Model(type=Exception500::class))
     */
    public function update(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(ContactCategory::class)->find($id);
        if (!$category) {
            return $this->jsonErrorResponse(Error400::ENTITY_NOT_FOUND);
        }

        $form = $this->submitForm($request, Form\ContactCategoryForm::class, $category);
        if (!$form->isValid()) {
            return $this->jsonFormErrorResponse($form);
        }

        $em->flush();

        return $this->jsonResponse($category, ['default']);
    }

    #[Route('/contact/category/{id}', name: 'contact_category_delete', methods: ['DELETE'])]
    /**
     * @OA\Response(response=200, description="Successful")
     * @OA\Response(response=400, description="Error", @Model(type=Error400\General::class))
     * @OA\Response(response=500, description="Exception", @Model(type=Exception500::class))
     */
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(ContactCategory::class)->find($id);
        if ($category) {
            $em->remove($category);
            $em->flush();
        }

        return $this->json(null);
    }
}
