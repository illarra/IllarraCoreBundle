<?php

namespace Illarra\CoreBundle\Controller\Admin;

use Illarra\CoreBundle\Controller\Admin\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends BaseController
{
    protected $label     = 'user.menu';
    protected $namespace = '\Illarra\CoreBundle';
    protected $entity    = 'IllarraCoreBundle:User';
    protected $baseRoute = 'admin_illarra_user';

    protected function getListRow()
    {
        return function ($entity) {
            return [
                'name' => $entity->getUsername(),
            ];
        };
    }

    /**
     * @Route("/{page}", name="admin_illarra_user_index", defaults={"page" = 1}, requirements={"page" = "\d+"})
     * @Method("GET")
     */
    public function indexAction($page)
    {
        return parent::indexAction($page);
    }
    
    /**
     * @Route("/{id}/show", name="admin_illarra_user_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        return parent::showAction($id);
    }
    
    /**
     * @Route("/create", name="admin_illarra_user_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        return parent::createAction($request);
    }
    
    /**
     * @Route("/new", name="admin_illarra_user_new")
     * @Method("GET")
     */
    public function newAction()
    {
        return parent::newAction();
    }
    
    /**
     * @Route("/{id}/edit", name="admin_illarra_user_edit")
     * @Method("GET")
     */
    public function editAction($id)
    {
        return parent::editAction($id);
    }
    
    /**
     * @Route("/{id}/update", name="admin_illarra_user_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        return parent::updateAction($request, $id);
    }
    
    /**
     * @Route("/{id}/delete", name="admin_illarra_user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::deleteAction($request, $id);
    }
}

