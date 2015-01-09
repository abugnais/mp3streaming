<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Music;

class DefaultController extends Controller
{
    /**
     * @Route("/upload")
     *
     */
    public function uploadAction(Request $request) {
        $music  =   new Music();
        $form   =   $this->createFormBuilder($music)
            ->add('name')
            ->add('file')
            ->add('submit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($music);
            $em->flush();
            return $this->redirect($this->generateUrl('app_default_list'));
        }
        return $this->render(
            'default/upload.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/show/{id}")
     */
    public function showAction($id) {
        $music  =   $this->getDoctrine()
                        ->getRepository('AppBundle\Entity\Music')
                        ->find($id);
        return $this->render(
            'default/show.html.twig',
            array('music' => $music)
        );
    }

    /**
     * @Route("/list")
     * @Route("/")
     */
    public function listAction() {
        $musicList  =   $this->getDoctrine()
            ->getRepository('AppBundle\Entity\Music')
            ->findAll();
        return $this->render(
            'default/list.html.twig',
            array('musicList'=>$musicList)
        );
    }
}
