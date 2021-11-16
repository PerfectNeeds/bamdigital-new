<?php

namespace PN\Bundle\UserBundle\Controller\Administration;

use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use PN\Bundle\UserBundle\Entity\User;
use PN\Bundle\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use PN\Utils\Validate;

/**
 * User controller.
 *
 * @Route("/admin")
 */
class AdminController extends Controller
{

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/", name="admin_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('user/admin/admin/index.html.twig');

    }


    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="admin_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $user->setEnabled(TRUE);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $role = $request->request->get('role');
            switch ($role) {
                case 1:
                    $role = User::ROLE_ADMIN;
                    break;
                case 2:
                    $role = User::ROLE_SEO;
                    break;
                case 3:
                    $role = User::ROLE_IMAGE_GALLERY;
                    break;
                default:
                    $role=User::ROLE_ADMIN;
            }
            $user->addRole($role);
            $userName = $this->get('user')->getUserName();
            $user->setCreator($userName);
            $user->setModifiedBy($userName);
            $userManager->updateUser($user);

            $this->addFlash('success', 'Successfully saved');
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('user/admin/admin/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing admin entity.
     *
     * @Route("/{id}/edit", name="admin_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userManager = $this->get('fos_user.user_manager');
        $roles=$user->getRoles();

        $form = $this->createForm(UserType::class, $user);
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($roles as  $role){
                $user->removeRole($role);
            }
            $role = $request->request->get('role');
            switch ($role) {
                case 1:
                    $role = User::ROLE_ADMIN;
                    break;
                case 2:
                    $role = User::ROLE_SEO;
                    break;
                case 3:
                    $role = User::ROLE_IMAGE_GALLERY;
                    break;
                default:
                    $role=User::ROLE_ADMIN;
            }
            $user->addRole($role);

            $userName = $this->get('user')->getUserName();
            $user->setModifiedBy($userName);
            $userManager->updateUser($user);

            $this->addFlash('success', 'Successfully updated');
            return $this->redirectToRoute('admin_edit', array('id' => $user->getId()));

        }

        return $this->render('user/admin/admin/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Lists all Category entities.
     *
     * @Route("/data/table", defaults={"_format": "json"}, name="admin_datatable")
     * @Method("GET")
     */
    public function dataTableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $srch = $request->query->get("search");
        $start = $request->query->get("start");
        $length = $request->query->get("length");
        $ordr = $request->query->get("order");

        $search = new \stdClass;
        $search->deleted = 0;
        $search->string = $srch['value'];
        $search->ordr = $ordr[0];
        $search->role = 'ROLE_ADMIN,ROLE_SEO,ROLE_IMAGE_GALLERY';

        $count = $em->getRepository('UserBundle:User')->filter($search, TRUE);
        $admins = $em->getRepository('UserBundle:User')->filter($search, FALSE, $start, $length);

        return $this->render("user/admin/admin/datatable.json.twig", array(
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "admins" => $admins,
            )
        );
    }


}
