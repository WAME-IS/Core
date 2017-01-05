<?php

namespace Wame\Core\Repositories;

use h4kuna\Gettext\GettextSetup;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryBuilder;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Nette\Object;
use Nette\Security\User;
use Wame\Core\Entities\BaseEntity;
use Wame\Core\Registers\RepositoryRegister;


interface IRepository { }


class BaseRepository extends Object implements IRepository
{
    /** @var integer */
    const STATUS_DELETED = 0;

    /** @var integer */
    const STATUS_ENABLED = 1;

    /** @var integer */
    const STATUS_DISABLED = 2;


    /**
     * Event called when entity is created
     *
     * Parameters of event:
     * \Nette\Forms\Form $form
     * array $values
     * BaseEntity $entity
     *
     * @var callable[]
     */
    public $onCreate = [];

    /**
     * Event called when entity is being read
     *
     * Parameters of event:
     * int $id
     *
     * @var callable[]
     */
    public $onRead = [];

    /**
     * Event called when entity is updated
     *
     * Parameters of event:
     * int $id
     *
     * @var callable[]
     */
    public $onUpdate = [];

    /**
     * Event called when entity is deleted
     *
     * Parameters of event:
     * int $id
     *
     * @var callable[]
     */
    public $onDelete = [];

    /** @var Container */
    public $container;

    /** @var EntityManager */
    public $entityManager;

    /** @var string */
    public $lang;

    /** @var User */
    public $user;

    /** @var EntityRepository */
    public $entity;

    /** @var string */
    protected $entityClass;


    /**
     * BaseRepository constructor.
     *
     * @param string $entityClass  entity class
     */
    public function __construct($entityClass)
    {
        if (!is_string($entityClass)) {
            throw new InvalidArgumentException('Argument must be an instance of string');
        }

        $this->entityClass = $entityClass;
    }


    /**
     * Inject repository
     *
     * @param Container $container                      container
     * @param EntityManager $entityManager              entity manager
     * @param GettextSetup $translator                  transtor
     * @param User $user                                user
     * @param RepositoryRegister $repositoryRegister    repository register
     */
    public function injectRepository(Container $container, EntityManager $entityManager, GettextSetup $translator, User $user, RepositoryRegister $repositoryRegister)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->lang = $translator->getLanguage();
        $this->user = $user;
        $this->entity = $this->entityManager->getRepository($this->entityClass);

        // register repository
        $repositoryRegister->add($this, $this->entityClass);
    }


    /**
     * Get table prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->container->parameters['database']['prefix'];
    }


    /**
     * Get one entity by criteria
     *
     * @param array $criteria	criteria
     * @param array $orderBy	order by
     * @return BaseEntity
     */
    public function get($criteria = [], $orderBy = [])
    {
        return $this->entity->findOneBy($criteria, $orderBy);
    }


    /**
     * Get all entries by criteria
     *
     * @param array $criteria   criteria
     * @param array $orderBy    order by
     * @param string $limit     limit
     * @param string $offset    offset
     * @return array
     */
    public function find($criteria = [], $orderBy = [], $limit = null, $offset = null)
    {
        return $this->entity->findBy($criteria, $orderBy, $limit, $offset);
    }


    /**
     * Get all entries in pairs
     *
     * @param array $criteria	criteria
     * @param string $value		value
     * @param array $orderBy	order by
     * @param string $key		key
     * @return array
     */
    public function findPairs($criteria = [], $value = null, $orderBy = [], $key = 'id')
    {
        return $this->entity->findPairs($criteria, $value, $orderBy, $key);
    }


    /**
     * Get all entries in pairs
     *
     * @param array $criteria	criteria
     * @param String $key		key
     * @return array
     */
    public function findAssoc($criteria = [], $key = 'id')
    {
        return $this->entity->findAssoc($criteria, $key);
    }


    /**
     * Return count of entities
     *
     * @param array $criteria	criteria
     * @return integer
     */
    public function countBy($criteria = [])
    {
        return $this->entity->countBy($criteria);
    }


    /**
     * Get rows by key
     *
     * @param array $criteria   criteria
     * @param string $key       key
     * @return array
     */
    public function getList($criteria = [], $key = 'id')
    {
        $return = [];

        $rows = $this->find($criteria);

        foreach ($rows as $row) {
            $return[$row->$key] = $row;
        }

        return $return;
    }


    /**
     * Remove entities
     *
     * @param array $criteria
     * @return bool
     */
	public function remove($criteria = [])
	{
		$entities = $this->find($criteria);

		foreach($entities as $entity) {
			$this->entityManager->remove($entity);
		}

		return true;
	}


    /**
     * Create query builder
     *
     * @param string $alias
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias = 'a')
    {
        return $this->entity->createQueryBuilder($alias);
    }


    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        if (!$this->entity) {
            return null;
        }

        return $this->entity->getClassName();
    }


    /**
     * Get new entity
     *
     * @return mixed
     */
    public function getNewEntity()
    {
        $entityName = $this->getEntityName();

        return new $entityName();
    }

}
