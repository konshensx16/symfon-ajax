<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Form\WishlistType;
use App\Repository\WishlistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wishlist")
 */
class WishlistController extends Controller
{
    /**
     * @Route("/", name="wishlist_index", methods="GET")
     */
    public function index(WishlistRepository $wishlistRepository): Response
    {
        return $this->render('wishlist/index.html.twig', ['wishlists' => $wishlistRepository->findAll()]);
    }

    /**
     * @Route("/new", name="wishlist_new", methods="GET|POST")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $wishlist = new Wishlist();
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            dump($request->request);
//            $em->persist($wishlist);
//            $em->flush();

            return $this->redirectToRoute('wishlist_index');
        }

        return $this->render('wishlist/new.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="wishlist_show", methods="GET")
     */
    public function show(Wishlist $wishlist): Response
    {
        return $this->render('wishlist/show.html.twig', ['wishlist' => $wishlist]);
    }

    /**
     * @Route("/{id}/edit", name="wishlist_edit", methods="GET|POST")
     */
    public function edit(Request $request, Wishlist $wishlist): Response
    {
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('wishlist_edit', ['id' => $wishlist->getId()]);
        }

        return $this->render('wishlist/edit.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="wishlist_delete", methods="DELETE")
     */
    public function delete(Request $request, Wishlist $wishlist): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wishlist->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($wishlist);
            $em->flush();
        }

        return $this->redirectToRoute('wishlist_index');
    }
}
