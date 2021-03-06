<?php

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View; // Utilisation de la vue de FOSRestBundle
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Synchro;
use AppBundle\Entity\Visite;
use AppBundle\Form\VisiteType;
use AppBundle\Form\SynchroType;
use AppBundle\Form\EtapeType;
use AppBundle\Form\PointVenteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\CredentialsType;
use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use AppBundle\Form\ProspectType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
/**
 * CommandeClient controller.
 */
class MobileController extends Controller
{

    /**
     * Lists all Produit entities.
     *@Rest\View()
     */
    public function saveProspectsAction(Request $request)
    {
      $data=$array();
      $form = $this->createFormBuilder($data) 
      ->add('prospects',CollectionType::class, array('entry_type'=> ProspectType::class,'allow_add' => true))
      ->add('user', EntityType::class, array('class' => 'AppBundle:Client'))
      ->getForm(); 
       $form->submit($request->request->all(),true); 
       if ($form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          foreach ($data['prospects'] as $key => $prospect) {
              $em->persist($prospect);
            }
            $em->flush();
          return ['success'=>true];
       }
        return  $form;
    }


private function getConnectedUser(){
    return $this->get('security.token_storage')->getToken()->getUser();
}
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * 
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm( CredentialsType::class, $credentials);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return $form;
        }
         $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Client')->findOneByUsername($credentials->getLogin());

        if (!$user) { // L'utilisateur n'existe pas
            return $this->invalidCredentials();
        }
       /** $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) { // Le mot de passe n'est pas correct
            return $this->invalidCredentials();
        }*/
        $authToken=AuthToken::create($user);
        $em->persist($authToken);
        $em->flush();
        return $authToken;
    }

//apk

public function apkAction()
{
  $request = $this->get('request');
    $path = $this->get('kernel')->getRootDir(). "/../web/home/apk/siat.apk";
    $content = file_get_contents($path);
    $response = new Response();
    //set headers
    $response->headers->set('Content-Type', 'mime/type');
    $response->headers->set('Content-Disposition', 'attachment;filename="siatMobile.1.2.apk"');
    $response->setContent($content);
    return $response;
 }
}
