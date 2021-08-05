<?php

namespace App\Controller;

use App\Dto\SearchRequestDTO;
use App\Entity\News;
use App\Mapper\RequestMapper;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/news", name="news_", defaults={"_format"="json"})
 */
class NewsController extends AbstractController
{
    /**
     * Accepts GET-params:
     * limit - number, for pagination
     * offset - number, for pagination, works only with limit
     * order_by - string, field name to order by: title or publishedAt
     * order_dir - string, direction of ordering, works only with order_by
     * date_start - date (Y-m-d H:M:S), start date for filtering
     * date_end - date (Y-m-d H:M:S), end date for filtering
     * ids - list of ids to search, separated by ","
     *
     * @Route("/", methods={"GET"}, name="list")
     */
    public function List( Request $request, ValidatorInterface $validator, NewsRepository $repo ): Response
    {

        $params = RequestMapper::SearchRequestToDTO($request->query->all());

        $errors = $validator->validate($params, null, [SearchRequestDTO::GROUP_LIST]);

        if (count($errors)) {
            return $this->json([
                'status' => "Error",
                'message' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $entities = $repo->findWithParams($params);

        return $this->json($entities);
    }

    /**
     * Accepts GET-params:
     * date_start - date (Y-m-d H:M:S), start date for filtering
     * date_end - date (Y-m-d H:M:S), end date for filtering
     *
     * @Route("/counts_by_dates", methods={"GET"}, name="counts_by_dates")
     */
    public function CountsByDates(Request $request, ValidatorInterface $validator, NewsRepository $repo): Response
    {
        $params = RequestMapper::SearchRequestToDTO($request->query->all());

        $errors = $validator->validate($params, null, [SearchRequestDTO::GROUP_COUNTS]);

        if (count($errors)) {
            return $this->json([
                'status' => "Error",
                'message' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $counts = $repo->findCountsByDates($params->date_start, $params->date_end);

        return $this->json($counts);
    }

    /**
     * @Route("/", name="news", methods={"POST"}, name="add")
     */
    public function Add(
        ValidatorInterface $validator,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em
    ): Response
    {
        $entity = $serializer->deserialize($request->getContent(),News::class,"json");
        $errors = $validator->validate($entity);

        if (count($errors)) {
            return $this->json([
                'status' => "Error",
                'message' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }
        $em->persist($entity);
        $em->flush();

        return $this->json([
            'status' => "OK",
            'message' => 'Created successfully'
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     */
    public function Show(NewsRepository $repo, int $id): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('The id does not exist in the database');
        }

        return $this->json($entity);
    }

    /**
     * Can be used with partial objects. It is not part of a standard, but it is good to have instead of PATCH.
     * @Route("/{id}", methods={"PUT"}, defaults={"_format"="json"}, name="edit")
     */
    public function Edit(
        ValidatorInterface $validator,
        Request $request,
        EntityManagerInterface $em,
        NewsRepository $repo,
        SerializerInterface $serializer,
        int $id
    ): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('The id does not exist in the database');
        }

        $serializer->deserialize(
            $request->getContent(),
            News::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $entity]
        );

        $errors = $validator->validate($entity);
        if (count($errors)) {
            return $this->json([
                'status' => "Error",
                'message' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($entity);
        $em->flush();

        return $this->json([
            'status' => "OK",
            'message' => 'Updated successfully'
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete"))
     */
    public function Delete(EntityManagerInterface $em, NewsRepository $repo, int $id): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('The id does not exist in the database');
        }

        $em->remove($entity);
        $em->flush();

        return $this->json([
            'status' => "OK",
            'message' => 'Deleted successfully'
        ]);
    }
}
