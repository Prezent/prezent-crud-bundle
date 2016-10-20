<?php

namespace Prezent\CrudBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Prezent\CrudBundle\Model\Configuration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base crud controller
 *
 * @author Sander Marechal
 */
abstract class CrudController extends Controller
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configuration = new Configuration($this);
        $this->configure($this->configuration);
        $this->configuration->validate();
    }

    /**
     * List objects
     *
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $sortField = $request->get('sort_by', $this->configuration->getDefaultSortField());
        $sortOrder = $request->get('sort_order', $this->configuration->getDefaultSortOrder());

        // Ensure that the correct field is active
        $request->query->set('sort_by', $sortField);
        $request->query->set('sort_order', $sortOrder);

        $queryBuilder = $this->getRepository()->createQueryBuilder('o');
        $queryBuilder->addOrderBy('o.' . $sortField, $sortOrder);

        $this->configureListCriteria($queryBuilder);

        $pager = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
        $pager->setCurrentPage($request->get('page', 1));

        $grid = $this->get('grid_factory')->createGrid(
            $this->configuration->getGridType(),
            $this->configuration->getGridOptions()
        );

        return $this->render($this->getTemplate($request, 'index'), [
            'config' => $this->configuration,
            'grid'   => $grid->createView(),
            'pager'  => $pager,
        ]);
    }

    /**
     * Add a new object
     *
     * @Route("/add")
     */
    public function addAction(Request $request)
    {
        $om = $this->getObjectManager();

        $form = $this->createForm(
            $this->configuration->getFormType(),
            $this->newInstance($request),
            $this->configuration->getFormOptions()
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $om->persist($form->getData());

            try {
                $om->flush();
                $this->addFlash('success', sprintf('flash.%s.add.success', $this->configuration->getName()));
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('flash.%s.add.error', $this->configuration->getName()));
            }

            return $this->redirectToRoute($this->configuration->getRoutePrefix() . 'index');
        }

        return $this->render($this->getTemplate($request, 'add'), [
            'config' => $this->configuration,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * Edit an object
     *
     * @Route("/edit/{id}")
     */
    public function editAction(Request $request, $id)
    {
        $om = $this->getObjectManager();

        $form = $this->createForm(
            $this->configuration->getFormType(),
            $this->findObject($id),
            $this->configuration->getFormOptions()
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $om->persist($form->getData());

            try {
                $om->flush();
                $this->addFlash('success', sprintf('flash.%s.edit.success', $this->configuration->getName()));
            } catch (\Exception $e) {
                $this->addFlash('error', sprintf('flash.%s.edit.error', $this->configuration->getName()));
            }

            return $this->redirectToRoute($this->configuration->getRoutePrefix() . 'index');
        }

        return $this->render($this->getTemplate($request, 'edit'), [
            'config' => $this->configuration,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * Delete an object
     *
     * @Route("/delete/{id}")
     */
    public function deleteAction($id)
    {
        $object = $this->findObject($id);

        $om = $this->getObjectManager();
        $om->remove($object);

        try {
            $em->flush();
            $this->addFlash('success', sprintf('flash.%s.delete.success', $this->configuration->getName()));
        } catch (\Exception $e) {
            $this->addFlash('error', sprintf('flash.%s.delete.error', $this->configuration->getName()));
        }

        return $this->redirectToRoute($this->configuration->getRoutePrefix() . 'index');
    }

    /**
     * Set the configuration
     *
     * @param Configuration $config
     * @return void
     */
    protected function configure(Configuration $config)
    {
    }

    /**
     * Get the configuration
     *
     * @return Configuration
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Generate new entity instance
     *
     * @param Request $request
     * @return object
     */
    protected function newInstance(Request $request)
    {
        return null;
    }

    /**
     * Find an object by ID
     *
     * @param mixed $id
     * @return object
     * @throws NotFoundException
     */
    protected function findObject($id)
    {
        if (!($object = $this->getRepository()->find($id))) {
            throw $this->createNotFoundException(
                sprintf('Object %s(%s) not found', $this->configuration->getEntityClass(), $id)
            );
        }

        return $object;
    }

    /**
     * Configure list criteria
     *
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    protected function configureListCriteria(QueryBuilder $queryBuilder)
    {
    }

    /**
     * Get the template for an action
     *
     * @param string $action
     * @return string
     */
    protected function getTemplate(Request $request, $action)
    {
        $templates = $this->get('prezent_crud.template_guesser')->guessTemplateNames([$this, $action], $request);

        foreach ($templates as $template) {
            if ($this->get('templating')->exists($template)) {
                return $template;
            }
        }

        return array_shift($templates); // This ensures a proper error message about a missing template
    }

    /**
     * Get the object manager for the configured class
     *
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->getDoctrine()->getManagerForClass($this->configuration->getEntityClass());
    }

    /**
     * Get the repository for the configured class
     *
     * @return ObjectRepository
     */
    protected function getRepository()
    {
        return $this->getObjectManager()->getRepository($this->configuration->getEntityClass());
    }
}
