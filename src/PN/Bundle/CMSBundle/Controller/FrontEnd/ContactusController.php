<?php

namespace PN\Bundle\CMSBundle\Controller\FrontEnd;

use PN\Bundle\ServiceBundle\Lib\Mailer;
use PN\Utils\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PN\Utils\Validate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * contactus controller.
 *
 * @Route("contactus")
 */
class ContactusController extends Controller
{

    /**
     * Contactus form.
     *
     * @Route("/", name="fe_contact")
     * @Method("GET")
     */
    public function contactAction()
    {

        $em = $this->getDoctrine()->getManager();

        $contactPage = $em->getRepository('SeoBundle:SeoPage')->find(11);

        return $this->render('cms/frontEnd/contactUs/contact.html.twig',['contactPage'=>$contactPage]);
    }

    /**
     * submit Form
     *
     * @Route("/submit", name="fe_contact_submit")
     * @Method("POST")
     */
    public function contactSubmitAction(Request $request)
    {


        $name = $request->get('name');
        $email = $request->get('email');
        $subject = $request->get('subject');
        $msg = $request->get('message');

        $error = array();

        $reCaptcha = new ReCaptcha();
        $reCaptchaValidate = $reCaptcha->verifyResponse();
        if ($reCaptchaValidate->success == False) {
            $message = "valid Captcha";
            array_push($error, $message);
        }

        if (!Validate::not_null($name)) {
            array_push($error, 'Name');
        }
        if (!Validate::not_null($email)) {
            array_push($error, 'Email');
        }
        if (Validate::not_null($email) AND !Validate::email($email)) {
            array_push($error, 'Valid Email');
        }
        if (!Validate::not_null($subject)) {
            array_push($error, 'Subject');
        }
        if (!Validate::not_null($msg)) {
            array_push($error, 'Message');
        }

        if (count($error) > 0) {
            $return = 'You must enter ';
            for ($i = 0; $i < count($error); $i++) {
                if (count($error) == $i + 1) {
                    $return .= $error[$i];
                } else {
                    if (count($error) == $i + 2) {
                        $return .= $error[$i] . ' and ';
                    } else {
                        $return .= $error[$i] . ', ';
                    }
                }
            }
            $this->addFlash('error', $return);

            return $this->redirectToRoute('fe_contact');
        }
        // send to Admin
        $messageAdmin = Mailer::newInstance()
            ->setSubject('Contact us | creality')
            ->setFrom(\AppKernel::fromEmail)
            ->setTo(\AppKernel::adminEmail)
            ->setBody(
                $this->renderView(
                    'cms/frontEnd/contactUs/adminEmail.html.twig', array(
                        'name' => $name,
                        'email' => $email,
                        'subject' => $subject,
                        'message' => $msg,
                    )
                )
                , 'text/html');
        $messageAdmin->send();

        $this->addFlash("success", "Message Sent Successfully");

        return $this->redirectToRoute('fe_contact');

    }

}
