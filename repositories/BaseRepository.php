<?php

namespace Wame\Core\Repositories;

use h4kuna\Gettext\GettextSetup;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\DI\Container;
use Nette\Object;
use Nette\Security\User;

interface IRepository
{
    
}

class BaseRepository extends Object implements IRepository
{

    /**
     * Event called when entity is created
     * 
     * Parameters of event:
     * \Nette\Forms\Form $form
     * array $values
     * \Wame\Core\Entities\BaseEntity $entity
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
    protected $entity;
    
    /** @var string */
    protected $entityClass;

    
    public function __construct($entityClass)
    {
        if(!is_string($entityClass)) {
            throw new \Nette\InvalidArgumentException('Argument must be an instance of string');
        }
        
        $this->entityClass = $entityClass;
    }
    
    
    public function injectRepository(Container $container, EntityManager $entityManager, GettextSetup $translator, User $user, \Wame\Core\Registers\RepositoryRegister $repositoryRegister)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->lang = $translator->getLanguage();
        $this->user = $user;
        $this->entity = $this->entityManager->getRepository($this->entityClass);
        
        // register repository |
        $repositoryRegister->add($this, $this->entityClass);
    }

    /**
     * Get table prefix
     * 
     * @return string	Returns prefix
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
     * @return BaseEntity		entity
     */
    public function get($criteria = [], $orderBy = [])
    {
        return $this->entity->findOneBy($criteria, $orderBy);
    }

    /**
     * Get all entries by criteria
     * 
     * @param array $criteria	criteria
     * @param array $orderBy	order by
     * @param string $limit		limit
     * @param string $offset	offset
     */
    public function find($criteria = [], $orderBy = [], $limit = null, $offset = null)
    {
        return $this->entity->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get all entries in pairs
     * 
     * @param Array $criteria	criteria
     * @param String $value		value
     * @param Array $orderBy	order by
     * @param String $key		key
     * @return Array			entries
     */
    public function findPairs($criteria = [], $value = null, $orderBy = [], $key = 'id')
    {
        return $this->entity->findPairs($criteria, $value, $orderBy, $key);
    }

    /**
     * Get all entries in pairs
     * 
     * @param Array $criteria	criteria
     * @param String $key		key
     * @return Array			entries
     */
    public function findAssoc($criteria = [], $key = 'id')
    {
        return $this->entity->findAssoc($criteria, $key);
    }

    /**
     * Return count of entities
     * 
     * @param array $criteria	criteria
     * @return integer			count
     */
    public function countBy($criteria = [])
    {
        return $this->entity->countBy($criteria);
    }

    /**
     * Get rows by key
     * 
     * @param array $criteria
     * @param string $key
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
	 * @param type $criteria	criteria
	 */
	public function remove($criteria = [])
	{
		$entities = $this->find($criteria);
		
		foreach($entities as $entity) {
			$this->entityManager->remove($entity);
		}
		
		return true;
	}
	
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
        if(!$this->entity) {
            return null;
        }
        return $this->entity->getClassName();
    }
    
    /**
     * Get new entity
     * 
     * @return \Wame\Core\Repositories\entityName
     */
    public function getNewEntity()
    {
        $entityName = $this->getEntityName();
        
        return new $entityName();
    }
    
}
