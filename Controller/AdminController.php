<?php

namespace Illarra\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdminController extends Controller
{   
    /**
     * @Route("/login", name="illarra_admin_login")
     * @Template()
     */
    public function loginAction()
    {
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        return $this->render(
            $this->container->getParameter('illarra_core.admin.templates.login'),
            [
                'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            ]
        );
    }
    
    /**
     * @Route("/login_check", name="illarra_admin_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }
    
    /**
     * @Route("/logout", name="illarra_admin_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
    
    /**
     * @Route("/logs", name="illarra_admin_logs")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function logsAction()
    {
        // Check /logs folder contents
        return array();
    }
    
    /**
     * @Route("/", name="illarra_admin_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * @Route("/analytics", name="illarra_admin_analytics")
     * @Template()
     */
    public function analyticsAction()
    {
        return array();
    }
    
    /**
     * @Route("/contact", name="illarra_admin_contact")
     * @Template()
     */
    public function contactAction(Request $request)
    {
        return array();
    }
    
    /**
     * @Route("/toggle", name="illarra_admin_toggle")
     * @Method({"POST"})
     */
    public function toggleAction(Request $request)
    {
        $id     = $request->request->get('id');
        $class  = $request->request->get('namespace');
        $field  = $request->request->get('field');
        $setter = 'set' . $field;
        $getter = 'get' . $field;
        
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Entity not found for id ' . $id
            );
        }

        $entity->$setter(!$entity->$getter());

        $em->persist($entity);
        $em->flush();
        
        return new JsonResponse(['id' => $id, 'value' => $entity->$getter()]);
    }
    
    /**
     * @Route("/sort", name="illarra_admin_sort")
     * @Method({"POST"})
     */
    public function sortAction(Request $request)
    {
        $class  = $request->request->get('namespace');
        $id     = $request->request->get('id');
        $prevId = $request->request->get('prevId');
        $sort   = 0;

        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository($class);
        $entity     = $repository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Entity not found for id ' . $id
            );
        }

        if (!empty($prevId)) {
            $previous = $repository->find($prevId);

            if (!$previous) {
                throw $this->createNotFoundException(
                    '"Previous" entity not found for id ' . $prevId
                );
            }

            $sort = $previous->getSort() + 1;
        }
        

        $entity->setSort($sort);
        $repository->reorderEntity($entity);
        
        $em->persist($entity);
        $em->flush();
        
        return new JsonResponse(['id' => $id, 'sort' => $sort]);
    }
    
    /**
     * @Route("/403", name="illarra_admin_error_403")
     * @Template()
     */
    public function error403Action(Request $request)
    {
        return array();
    }
}
