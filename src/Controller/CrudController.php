<?php

namespace Prezent\CrudBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Prezent\CrudBundle\CrudEvents;
use Prezent\CrudBundle\Event\CrudEvent;
use Prezent\CrudBundle\Event\PostFlushEvent;
use Prezent\CrudBundle\Event\PreFlushEvent;
use Prezent\CrudBundle\Event\PreSubmitEvent;
use Prezent\CrudBundle\Event\ValidationFailedEvent;
use Prezent\CrudBundle\Model\Configuration;
use Prezent\CrudBundle\Templating\TemplateGuesser;
use Prezent\Grid\Grid;
use Prezent\Grid\GridFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Base crud controller
 *
 * @author Sander Marechal
 */
abstract class CrudController extends AbstractController
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var TemplateGuesser
     */
    private $templateGuesser;

    /**
     * Constructor
     *
     * @param TemplateGuesser $templateGuesser
     */
    public function __construct(ManagerRegistry $doctrine, TemplateGuesser $templateGuesser)
    {
        $this->doctrine = $doctrine;
        $this->templateGuesser = $templateGuesser;
    }

    /**
     * List objects
     *
     * @Route("/")
     * @return Response
     */
    public function indexAction(Request $request, GridFactory $gridFactory)
    {
        $configuration = $this->getConfiguration($request);

        if (!$configuration->getGridType()) {
            throw new \RuntimeException('You must set the gridType on the CRUD configuration');
        }

        $sortField = $request->get('sort_by', $configuration->getDefaultSortField());
        $sortOrder = $request->get('sort_order', $configuration->getDefaultSortOrder());

        // Ensure that the correct field is active
        $request->query->set('sort_by', $sortField);
        $request->query->set('sort_order', $sortOrder);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getRepository()->createQueryBuilder('o');

        // If no specific entity alias is given, link the sort field to the main entity
        if (!strpos($sortField, '.') && !(strpos($sortField, '_') === 0)) {
            $sortField = sprintf('o.%s', $sortField);
        }
        $queryBuilder->addOrderBy($sortField, $sortOrder);

        $this->configureListCriteria($request, $queryBuilder);

        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage($request->get('resultsPerPage', $configuration->getResultsPerPage()));
        $pager->setCurrentPage($request->get('page', 1));

        /** @var Grid $grid */
        $grid = $gridFactory->createGrid(
            $configuration->getGridType(),
            $configuration->getGridOptions()
        );

        return $this->render($this->getTemplate($request, 'index'), array_merge([
            'base_template' => $this->getTemplate($request, 'base'),
            'config' => $configuration,
            'grid'   => $grid->createView(),
            'pager'  => $pager,
        ], $configuration->getTemplateVariables()));
    }

    /**
     * Add a new object
     *
     * @Route("/add")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request, LoggerInterface $logger)
    {
        $configuration = $this->getConfiguration($request);
        $dispatcher = $configuration->getEventDispatcher();
        $om = $this->getObjectManager();

        if (!$configuration->getFormType()) {
            throw new \RuntimeException('You must set the formType on the CRUD configuration');
        }

        $object = $this->newInstance($request);
        $form = $this->createForm($configuration->getFormType(), $object, $configuration->getFormOptions());

        $event = new PreSubmitEvent($configuration, $request, $object, $form);
        $dispatcher->dispatch($event, CrudEvents::PRE_SUBMIT);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $object = $form->getData();
                $om->persist($object);

                $event = new PreFlushEvent($configuration, $request, $object, $form);
                $dispatcher->dispatch($event, CrudEvents::PRE_FLUSH);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }
            }

            if ($form->isValid()) { // Check again, the PreFlushEvent may have added errors
                $event = new PostFlushEvent($configuration, $request, $object, $form);

                try {
                    $om->flush();
                    $this->addFlash('success', sprintf('flash.%s.add.success', $configuration->getName()));
                } catch (\Exception $e) {
                    $event->setException($e);
                    $logger->error($e->getMessage(), ['exception' => $e]);
                    $this->addFlash('error', sprintf('flash.%s.add.error', $configuration->getName()));
                }

                $dispatcher->dispatch($event, CrudEvents::POST_FLUSH);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectToRoute(
                    $configuration->getRoutePrefix() . 'index',
                    $configuration->getRouteParameters()
                );
            } else {
                $event = new ValidationFailedEvent($configuration, $request, $object, $form);
                $dispatcher->dispatch($event, CrudEvents::VALIDATION_FAILED);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }
            }
        }

        return $this->render($this->getTemplate($request, 'add'), array_merge([
            'base_template' => $this->getTemplate($request, 'base'),
            'config' => $configuration,
            'form'   => $form->createView(),
            'object' => $object,
        ], $configuration->getTemplateVariables()));
    }

    /**
     * Edit an object
     *
     * @Route("/edit/{id}")
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function editAction(Request $request, LoggerInterface $logger, $id)
    {
        $configuration = $this->getConfiguration($request);
        $dispatcher = $configuration->getEventDispatcher();
        $om = $this->getObjectManager();

        if (!$configuration->getFormType()) {
            throw new \RuntimeException('You must set the formType on the CRUD configuration');
        }

        $object = $this->findObject($request, $id);
        $form = $this->createForm($configuration->getFormType(), $object, $configuration->getFormOptions());

        $event = new PreSubmitEvent($configuration, $request, $object, $form);
        $dispatcher->dispatch($event, CrudEvents::PRE_SUBMIT);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $om->persist($object);

                $event = new PreFlushEvent($configuration, $request, $object, $form);
                $dispatcher->dispatch($event, CrudEvents::PRE_FLUSH);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }
            }

            if ($form->isValid()) { // Check again, the PreFlushEvent may have added errors
                $event = new PostFlushEvent($configuration, $request, $object, $form);

                try {
                    $om->flush();
                    $this->addFlash('success', sprintf('flash.%s.edit.success', $configuration->getName()));
                } catch (\Exception $e) {
                    $event->setException($e);
                    $logger->error($e->getMessage(), ['exception' => $e]);
                    $this->addFlash('error', sprintf('flash.%s.edit.error', $configuration->getName()));
                }

                $dispatcher->dispatch($event, CrudEvents::POST_FLUSH);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectToRoute(
                    $configuration->getRoutePrefix() . 'index',
                    $configuration->getRouteParameters()
                );
            } else {
                $event = new ValidationFailedEvent($configuration, $request, $object, $form);
                $dispatcher->dispatch($event, CrudEvents::VALIDATION_FAILED);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }
            }
        }

        return $this->render($this->getTemplate($request, 'edit'), array_merge([
            'base_template' => $this->getTemplate($request, 'base'),
            'config' => $configuration,
            'form'   => $form->createView(),
            'object' => $object,
        ], $configuration->getTemplateVariables()));
    }

    /**
     * Delete an object
     *
     * @Route("/delete/{id}")
     * @param string $id
     * @return Response
     */
    public function deleteAction(Request $request, LoggerInterface $logger, $id)
    {
        $configuration = $this->getConfiguration($request);
        $dispatcher = $configuration->getEventDispatcher();
        $object = $this->findObject($request, $id);

        $event = new PreFlushEvent($configuration, $request, $object);
        $dispatcher->dispatch($event, CrudEvents::PRE_FLUSH);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        $event = new PostFlushEvent($configuration, $request, $object);
        $om = $this->getObjectManager();
        $om->remove($object);

        try {
            $om->flush();
            $this->addFlash('success', sprintf('flash.%s.delete.success', $configuration->getName()));
        } catch (\Exception $e) {
            $event->setException($e);
            $logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', sprintf('flash.%s.delete.error', $configuration->getName()));
        }

        $dispatcher->dispatch($event, CrudEvents::POST_FLUSH);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        return $this->redirectToRoute(
            $configuration->getRoutePrefix() . 'index',
            $configuration->getRouteParameters()
        );
    }

    /**
     * Set the configuration
     *
     * @param Request $request
     * @param Configuration $config
     * @return void
     */
    protected function configure(Request $request, Configuration $configuration)
    {
    }

    /**
     * Get the configuration
     *
     * @param Request $request
     */
    protected function getConfiguration(Request $request = null)
    {
        if (!$this->configuration) {
            if (!$request) {
                throw new \RuntimeException('The first time getConfiguration() is called, you must pass a Request.');
            }

            $this->configuration = new Configuration($request);
            $this->configure($request, $this->configuration);
            $this->configuration->validate();
        }

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
     * @throws NotFoundHttpException
     */
    protected function findObject(Request $request, $id)
    {
        $configuration = $this->getConfiguration($request);

        if (!($object = $this->getRepository()->find($id))) {
            throw $this->createNotFoundException(
                sprintf('Object %s(%s) not found', $configuration->getEntityClass(), $id)
            );
        }

        return $object;
    }

    /**
     * Configure list criteria
     *
     * @param Request $request
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    protected function configureListCriteria(Request $request, QueryBuilder $queryBuilder)
    {
    }

    /**
     * Get the template for an action
     *
     * @param Request $request
     * @param string $action
     * @return string
     */
    protected function getTemplate(Request $request, $action)
    {
        $templates = $this->templateGuesser->guessTemplateNames([$this, $action], $request);

        foreach ($templates as $template) {
            if ($this->container->get('twig')->getLoader()->exists($template)) {
                return $template;
            }
        }

        return array_shift($templates); // This ensures a proper error message about a missing template
    }

    /**
     * Get the object manager for the configured class
     *
     * @param string $class
     * @return ObjectManager
     */
    protected function getObjectManager($class = null)
    {
        return $this->doctrine->getManagerForClass($class ?: $this->getConfiguration()->getEntityClass());
    }

    /**
     * Get the repository for the configured class
     *
     * @param string $class
     * @return ObjectRepository
     */
    protected function getRepository($class = null)
    {
        return $this->getObjectManager($class)->getRepository($class ?: $this->getConfiguration()->getEntityClass());
    }
}
