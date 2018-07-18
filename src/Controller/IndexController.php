<?php
/**
 * Created by PhpStorm.
 * User: momus
 * Date: 7/12/18
 * Time: 8:59 PM
 */

namespace App\Controller;

use App\Entity\Users;
use App\Utils\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    /**
     * @param Request $request
     *
     * @Route("/", name="index", methods={"GET", "POST"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function start(Request $request, Logger $logger)
    {
        $user = new Users();

        $form = $this->createFormBuilder($user)
                     ->add('login', TextType::class, ['attr' => ['class' => 'form-control']])
                     ->add('password', PasswordType::class, ['attr' => ['class' => 'form-control']])
                     ->add('save', SubmitType::class, [
                         'label' => 'Dodaj',
                         'attr'  => ['class' => 'btn btn-primary mt-3']
                     ])
                     ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $request->isMethod('POST')) {

            if ( ! $logger->logInAs($user)) {
                $this->addFlash('warning',
                    'Błąd, podałeś błędne dane, nie mogę się zalogować');

                return $this->redirectToRoute('index');
            }
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Dane zapisane, teraz logowanie będzie odbywać się codziennie automatycznie');

            $this->redirectToRoute('index');
        }

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}