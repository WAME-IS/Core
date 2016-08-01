<?php

namespace Wame\Core\Repositories;

use h4kuna\Gettext\GettextSetup;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Events\EventArgsList;
use Kdyby\Events\EventManager;
use Nette\DI\Container;
use Nette\Object;
use Nette\Security\User;
use Wame\Core\Event\RepositoryEntitySetEvent;

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

    
    public function __construct(
        Container $container, EntityManager $entityManager, GettextSetup $translator, User $user, $entityClass = null
    ) {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->lang = $translator->getLanguage();
        $this->user = $user;

        if ($entityClass) {
            $this->setEntityClass($entityClass);
        }
    }

    public function setEntityClass($entityClass)
    {
        $en = 'Wame\\Core\\Repositories\\BaseRepository::onEntitynNameSet';
        $eventtManager = $this->container->getByType(EventManager::class);
        if ($eventtManager->hasListeners($en)) {
            $event = new RepositoryEntitySetEvent($entityClass);
            $eventtManager->dispatchEvent($en, new EventArgsList([$event]));
            $this->entity = $this->entityManager->getRepository($event->getEntityName());
        } else {
            $this->entity = $this->entityManager->getRepository($entityClass);
        }
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
	
    public function createQueryBuilder($alias = null)
    {
        return $this->entity->createQueryBuilder($alias);
    }
    
    public function getEntityName()
    {
        return $this->entity->getClassName();
    }
    
}
