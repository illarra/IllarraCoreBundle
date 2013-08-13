<?php

namespace Illarra\CoreBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class BaseController extends Controller
{
    use \Illarra\CoreBundle\Traits\Controller\CoreConfiguration;

    protected $label     = '';
    protected $namespace = '';
    protected $entity    = '';
    protected $baseRoute = '';
    protected $filter    = '';
    protected $order     = [null, null];

    protected function getListRow()
    {
        return function ($entity) {
            return [];
        };
    }

    protected function getListOrder()
    {
        return [];
    }

    protected function getEntityName()
    {
        return explode(':', $this->entity)[1];
    }

    protected function getTemplatesFolder()
    {
        $tpl = explode(':', $this->entity);

        return $tpl[0] . ':Admin/' . $tpl[1];
    }

    protected function getTemplateName($page)
    {
        return $this->getTemplatesFolder() . ':' . $page . '.html.twig';
    }

    protected function getNewEntity()
    {
        $class = $this->namespace . "\Entity\\" . $this->getEntityName();
        return new $class();
    }

    protected function getNewType()
    {
        $class = $this->namespace . "\Form\\" . $this->getEntityName() . "Type";
        return new $class();
    }
    
    protected function getRouteName($page)
    {
        return $this->baseRoute . '_' . $page;
    }

    protected function findEntityById($id) 
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->entity)->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Unable to find %s entity.', $this->getEntityName()));
        }

        return $entity;
    }

    public function indexAction($page)
    {
        $request = $this->get('request');

        // initialize a query builder
        $qb = $this->get('doctrine.orm.entity_manager')
            ->getRepository($this->entity)
            ->createQueryBuilder('e');

        // ORDERING
        $order_cbs     = $this->getListOrder();
        $order_columns = array_keys($order_cbs);
        $order_column  = $request->query->get('order_column', '');
        $order_dir     = strtoupper($request->query->get('order_dir', 'ASC'));

        if (empty($order_column)) {
            $order_column = $this->order[0];
            $order_dir    = $this->order[1];
        }

        if (!empty($order_column)) {
            if ($order_dir != 'ASC' && $order_dir != 'DESC') {
                $order_dir = 'ASC';
            }

            if (in_array($order_column, $order_columns)) {
                $order_cb = $order_cbs[$order_column];

                if (is_callable($order_cb)) {
                    $order_cb($qb, $order_dir);
                } else {
                    $qb->orderBy('e.' . $order_column, $order_dir);
                }
            }
        }

        // FILTER
        $isFiltered = false;

        if (!empty($this->filter)) {
            $filterClass = $this->namespace . $this->filter;
            $filter      = $this->createForm(new $filterClass, null, array(
                'action' => $this->generateUrl($this->baseRoute . '_index'),
                'method' => 'GET',
            ));

            // http://stackoverflow.com/questions/9078754/symfony-2-form-extra-fields
            $filter_data     = $request->query->all();
            $filter_children = $filter->all();
            $filter_data     = array_intersect_key($filter_data, $filter_children);

            $filter->submit($filter_data);

            if (!empty($filter_data) && $filter->isValid()) {
                // build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filter, $qb);
                $isFiltered = true;
            }
        }

        // Pager fanta
        $adapter   = new DoctrineORMAdapter($qb);
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage(20);

        // check pages
        if ($page < 1) {
            return $this->redirect($this->generateUrl($this->getRouteName('index')));
        }  

        if ($page > $paginator->getNbPages()) {
            return $this->redirect($this->generateUrl($this->getRouteName('index'), [
                'page' => $paginator->getNbPages()
            ]));
        }

        $paginator->setCurrentPage($page);

        // Columns definition
        $em     = $this->getDoctrine()->getManager();
        $rowCb  = $this->getListRow();
        $fields = $em->getClassMetadata($this->entity)->getFieldNames();

        $columns = array_keys($rowCb($this->getNewEntity()));

        /*
        // Check fields exist
        foreach ($columns as $col) {
            if (!in_array($col, $fields)) {
                throw new \Exception("Field '$col' in getListRow does not exist for entity: " . $this->getEntityName(), 1);
            }
        }*/

        // Add ID column
        if (in_array('id', $fields)) {
            array_unshift($columns, 'id');
        }

        $listMapper = new ListMapper($this->getListRow());

        return $this->render(
            $this->getTemplateName('index'),
            [
                'entity_label'      => $this->label,
                'filter'            => !empty($this->filter) ? $filter->createView() : false,
                'is_filtered'       => $isFiltered,
                'page'              => $paginator->getCurrentPage(),
                'pages'             => $paginator->getNbPages(),
                'entities'          => $paginator->getCurrentPageResults(),
                'entities_per_page' => $paginator->getMaxPerPage(),
                'entities_count'    => $paginator->getNbResults(),
                'base_route'        => $this->baseRoute,
                'list_mapper'       => $listMapper,
                'columns'           => $columns,
                'order_columns'     => $order_columns,
                'order_column'      => $order_column,
                'order_dir'         => $order_dir,
            ]
        );
    }

    public function showAction($id)
    {
        $entity = $this->findEntityById($id);

        return $this->render(
            $this->getTemplateName('show'),
            [
                'entity_label' => $this->label,
                'entity'       => $entity,
                'delete_form'  => $this->createDeleteForm($id)->createView(),
                'base_route'   => $this->baseRoute,
            ]  
        );
    }

    public function createAction(Request $request)
    {
        $entity = $this->getNewEntity();
        
        $form = $this->createForm($this->getNewType(), $entity, ['cascade_validation' => true]);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl($this->getRouteName('index')));
        }

        return $this->render(
            $this->getTemplateName('edit'),
            [
                'entity_label' => $this->label,
                'entity'       => $entity,
                'form'         => $form->createView(),
                'base_route'   => $this->baseRoute,
            ]  
        );
    }

    public function newAction()
    {
        $entity     = $this->getNewEntity();
        $reflection = new \ReflectionClass($entity); 
        $traits     = $reflection->getTraitNames();

        // Init translatable Trait
        if (in_array('Knp\DoctrineBehaviors\Model\Translatable\Translatable', $traits)) {
            foreach ($this->getAvailableLocales() as $locale) {
                $entity->translate($locale);
            }

            $entity->mergeNewTranslations();
        }
        
        $form = $this->createForm($this->getNewType(), $entity);

        return $this->render(
            $this->getTemplateName('edit'),
            [
                'entity_label'     => $this->label,
                'entity'           => $entity,
                'form'             => $form->createView(),
                'base_route'       => $this->baseRoute,
                'templates_folder' => $this->getTemplatesFolder(),
            ]  
        );
    }

    public function editAction($id)
    {
        $entity   = $this->findEntityById($id);
        $editForm = $this->createForm($this->getNewType(), $entity);
        
        return $this->render(
            $this->getTemplateName('edit'),
            [
                'entity_label'     => $this->label,
                'entity'           => $entity,
                'form'             => $editForm->createView(),
                'delete_form'      => $this->createDeleteForm($id)->createView(),
                'base_route'       => $this->baseRoute,
                'templates_folder' => $this->getTemplatesFolder(),
            ]  
        );
    }

    public function updateAction(Request $request, $id)
    {
        $entity = $this->findEntityById($id);

        $editForm = $this->createForm($this->getNewType(), $entity, ['cascade_validation' => true]);
        $editForm->bind($request);
        
        if ($editForm->isValid()) {
            $entity->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl($this->getRouteName('edit'), ['id' => $id]));
        }
        
        return $this->render(
            $this->getTemplateName('edit'),
            [
                'entity_label' => $this->label,
                'entity'       => $entity,
                'form'         => $this->createDeleteForm($id)->createView(),
                'delete_form'  => $deleteForm->createView(),
                'base_route'   => $this->baseRoute,
            ]  
        );
    }

    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        
        if ($form->isValid()) {
            $entity = $this->findEntityById($id);
            $em     = $this->getDoctrine()->getManager();

            $em->remove($entity);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl($this->getRouteName('index')));
    }

    /**
     * @param  mixed $id
     * @return Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
