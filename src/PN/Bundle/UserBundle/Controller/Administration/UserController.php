<?php

namespace PN\Bundle\UserBundle\Controller\Administration;

use PN\Bundle\MediaBundle\Form\SingleImageType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use PN\Utils\Validate;
use PN\Bundle\UserBundle\Entity\User;

/**
 * User controller.
 *
 * @Route("/")
 */
class UserController extends Controller
{


    /**
     * Deletes a Supplier entity.
     *
     * @Route("/change-state/{id}", name="user_change_state")
     * @Method("POST")
     */
    public function changeStateAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $userManager = $this->get('fos_user.user_manager');

        if ($user->isEnabled()) {
            $user->setEnabled(FALSE);
        } else {
            $user->setEnabled(TRUE);
        }

        $userManager->updateUser($user);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * Deletes a Merchant entity.
     *
     * @Route("/delete/{id}", name="user_delete")
     * @Method("Delete")
     */
    public function deleteAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $rand = substr(md5(microtime()),rand(0,26),5);
        $user->setEmail($user->getEmail().'-del-'.$rand);
        $user->setEnabled(FALSE);
        $userName = $this->get('user')->getUserName();
        $user->setDeletedBy($userName);
        $user->setDeleted(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($user);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));

    }

    /**
     * Displays a form to edit an existing Person entity.
     *
     * @Route("/{id}/details", name="user_details")
     * @Method("GET")
     * @Template()
     */
    public function userDetailsAction(User $user)
    {

        return array(
            'entity' => $user,
        );
    }

    /**
     * Deletes a Supplier entity.
     *
     * @Route("/login-as/{id}", requirements={"id" = "\d+"}, name="user_login_as_user")
     * @Method("GET")
     */
    public function loginAsUserAction(Request $request, User $user)
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$user->isEnabled()) {
            $request->getSession()->getFlashBag()->add('error', "this user is blocked, so you can't login with this account");
            return $this->redirect($request->headers->get('referer'));
        }

        $securityContext = $this->get('security.token_storage');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        $securityContext->setToken($token);

        $session = $this->get('session');
        $session->set('_security_' . 'main', serialize($token));

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('fos_user_profile_show');
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or $this->get('security.authorization_checker')->isGranted('ROLE_SEO') or $this->get('security.authorization_checker')->isGranted('ROLE_IMAGE_GALLERY')) {
            return $this->redirectToRoute('dashboard');
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('fe_home');
        }
        return $this->redirectToRoute('fe_home');
    }

}
