<?php

namespace Wame\Core\Repositories;

use Nette\DI\Container;
use Nette\Utils\DateTime;
use Nette\Security\User;
use Kdyby\Doctrine\EntityManager;
use h4kuna\Gettext\GettextSetup;

use Wame\UserModule\Entities\UserEntity;
use Wame\Utils\Discriminator\Annotation;


interface ICrud
{
	public function create($values);
	public function update($id, $values); // TODO: prerobit na entitu, values bude uz vlozene do entity vo formulari
	public function delete($criteria, $status);
}


class BaseRepository extends \Nette\Object /*implements \Kdyby\Persistence\Queryable*/
{
	/** @var array */
	public $onCreate = [];
	
	/** @var array */
	public $onRead = [];
	
	/** @var array */
	public $onUpdate = [];
	
	/** @var array */
	public $onDelete = [];
	
	/** @var Container */
	public $container;
	
	/** @var EntityManager */
	public $entityManager;

	/** @var string */
	public $lang;

	/** @var User */
	public $user;
	
	/** @var string */
	private $name;
	
	/** Entity */
	public $entity;
	
	/** UserEntity */
	public $yourUserEntity = null;

	
	public function __construct(
		Container $container, 
		EntityManager $entityManager,
		GettextSetup $translator,
		User $user,
		$entityName = null
	) {
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->lang = $translator->getLanguage();
		$this->user = $user;
		
		$this->name = $entityName;
		
		if ($entityName) {
			$this->entity = $this->entityManager->getRepository($entityName);
		}
		
		if ($this->user->isLoggedIn()) {
			$this->yourUserEntity = $this->entityManager->getRepository(UserEntity::class)->findOneBy(['id' => $this->user->id]);
		}
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
	 * Get class name from namespace
	 * 
	 * @param string $namespace
	 * @return string
	 */
	public function getClassName($namespace)
	{
		$reflect = new \ReflectionClass($namespace);
		
		return $reflect->getShortName();
	}

	
	/**
	 * Get one entity by criteria
	 * 
	 * @param array $criteria
	 * @param array $orderBy
	 */
	public function get($criteria = [], $orderBy = [])
	{
		return $this->entity->findOneBy($criteria, $orderBy);
	}
	
	
	/**
	 * Get all entries by criteria
	 * 
	 * @param array $criteria
	 * @param array $orderBy
	 * @param string $limit
	 * @param string $offset
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
	 * Remove entities
	 * 
	 * @param type $criteria	
	 */
	public function remove($criteria = [])
	{
		$entities = $this->find($criteria);
		
		foreach($entities as $entity) {
			$this->entityManager->remove($entity);
		}
	}
	
	
	/**
	 * Format string date to DateTime for Doctrine entity
	 * 
	 * @param DateTime $date
	 * @param string $format
	 * @return DateTime
	 */
	public function formatDate($date, $format = 'Y-m-d H:i:s')
	{
		if ($date == 'now') {
			return new DateTime('now');
		} else {
			return new DateTime(date($format, strtotime($date)));
		}
	}
	
	
	/**
	 * Resort items
	 * 
	 * @param array $criteria
	 * @param numeric $factor
	 */
	public function resort($criteria = [], $factor = 1)
	{
		$items = $this->find($criteria, ['sort']);

		if (count($items) > 0) {
			$i = 1;
			
			foreach ($items as $item) {
				if ($factor > 0) {
					$item->setSort($item->getSort() + $factor);
				} elseif ($factor < 0) {
					$item->setSort($item->getSort() - $factor);
				} elseif ($factor == 0) {
					$item->setSort($i++);
				}
			}
		}
		
		$this->entityManager->flush();
	}
	
	
	/**
	 * Get next sort
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function getNextSort($criteria = [])
	{
		$count = $this->countBy($criteria);
		
		return $count + 1;
	}

}