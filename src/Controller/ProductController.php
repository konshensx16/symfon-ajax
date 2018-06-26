<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\Product;
use App\Form\FeedbackType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{

    /**
     * @Route("/", name="product_index", methods="GET")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', ['products' => $productRepository->findAll()]);
    }

    /**
     * @Route("/new", name="product_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/download", name="download_file", options={"expose"=true}, methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function downloadFile(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $file = new File($this->getParameter('uploads_dir') . 'large-image.jpg');
            return $this->file($file);
        }
        return new JsonResponse([
            'message' => 'Accessible via ana ajax request'
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"POST", "GET"}, options={"expose"=true})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function show(Request $request, Product $product): Response
    {
        // can i programatically set the product to the feedback with explicitely doing it in the controller??
        // probably can't but that's not an issue
        $feedback = new Feedback();
        // create the feedback form
        $form = $this->createForm(FeedbackType::class, $feedback);

        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            if ($request->isXmlHttpRequest())
            {
                $encoders = [
                    new JsonEncoder()
                ];

                $normalizers = [
                    (new ObjectNormalizer())
                        ->setCircularReferenceHandler(function ($object)
                    {
                        return $object->getName();
                    })
                    ->setIgnoredAttributes([
                        'product'
                    ])
                ];

                $serializer = new Serializer($normalizers, $encoders);
                // the feedback to the product
                $product->addFeedback($feedback);
                // save the feedback to the DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($feedback);
//                $em->flush();
                $data = $serializer->serialize($feedback, 'json');
//                var_dump($feedback);
                return new JsonResponse($data, 200, [], true);
            }
        }
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'feedback_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods="GET|POST")
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods="DELETE", options={"expose"=true})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($request->isXmlHttpRequest()) {
            if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction();

                $em->remove($product);
                $em->flush();

                $em->commit();
                return new JsonResponse([
                    'type' => 'success',
                    'message'   =>  'item was removed'
                ], 200);
            }
        }
        return new JsonResponse([
            'type'      => 'error',
            'message'   => 'This is only accesible in AJAX'
        ], 500);
    }

    /**
     * @Route("/getFeedback/{id}", name="getFeedback", methods={"GET"}, options={"expose"=true})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function getFeedbackForProduct(Request $request, int $id)
    {
        if ($request->isXmlHttpRequest()) {
            // fetch all feedback for a given product
            $em = $this->getDoctrine()->getManager();
            /**
             * @var Feedback $feedback
             */
            $feedback = $em->getRepository(Feedback::class)->findBy([
                'product' => $id
            ]);
            // serialize the data if any exisst
            if ($feedback) {
                $encoders = [
                    new JsonEncoder()
                ];
                $normalizers = [
                    (new ObjectNormalizer())->setIgnoredAttributes(['product'])
                ];
                $serializer = new Serializer($normalizers, $encoders);
                $data = $serializer->serialize($feedback, 'json');
                return new JsonResponse($data, 200, [], true);
            }
            return new JsonResponse("No feedback for this product yet, Be the first ");
        }
        return new JsonResponse("This function is only available in AJAX");
    }


}
