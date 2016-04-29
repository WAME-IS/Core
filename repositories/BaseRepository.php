<?php

namespace Wame\Core\Repositories;

use Nette\DI\Container;
use Nette\Utils\DateTime;
use Kdyby\Doctrine\EntityManager;
use h4kuna\Gettext\GettextSetup;
use Nette\Security\User;

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
	
//	/** @var \Kdyby\Doctrine\EntityRepository */
//	private $repo;

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
		
		if($entityName) {
			$this->entity = $this->entityManager->getRepository($entityName);
		}
		
//		dump($this->entity->findAll()); exit;
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
	
//	protected function getRepo() {
//		if (!$this->repo) {
////			$entityBuilder = new EntityBuilder($this->name);
////			$class = $entityBuilder->getClass();
//			
////			$class = $this->entityHandler->getBuilder($this->name)->getClass();
//			$this->repo = $this->entityManager->getRepository('Wame\CategoryModule\Entities\CategoryEntity');
//		}
//		return $this->repo;
//	}
//
//	public function select($alias = NULL) {
//		$this->getRepo()->select($alias);
//	}
//	
//	public function createNativeQuery($sql, \Doctrine\ORM\Query\ResultSetMapping $rsm) {
//		$this->getRepo()->createNativeQuery($sql, $rsm);
//	}
//
//	public function createQuery($dql = NULL) {
//		$this->getRepo()->createQuery($dql);
//	}
//
//	public function createQueryBuilder($alias = NULL, $indexBy = NULL) {
//		$this->getRepo()->createQueryBuilder($alias, $indexBy);
//	}
	
	
//	public function find($id) {
//		$criteria = ['id' => $id];
//		
//		return $this->getRepo()->findOneBy($criteria);
//	}
//	
//	public function findBy($criteria = [], $orderBy = null, $length = null, $offset = null)
//	{
//		return $this->getRepo()->findBy($criteria, $orderBy, $length, $offset);
//	}
	
	/**
	 * Get one article by criteria
	 * 
	 * @param array $criteria
	 * @return ArticleEntity
	 */
	public function get($criteria = [])
	{
		return $this->entity->findOneBy($criteria);
	}
	
	/**
	 * Get all articles by criteria
	 * 
	 * @param array $criteria
	 * @return ArticleEntity
	 */
	public function find($criteria = [], $orderBy = null, $limit = null, $offset = null)
	{
		$articleEntity = $this->entity->findBy($criteria, $orderBy, $limit, $offset);

		return $articleEntity;
	}
	
	/**
	 * Return count of articles
	 * 
	 * @param array $criteria	criteria
	 * @return integer			count
	 */
	public function countBy($criteria = [])
	{
		return $this->entity->countBy($criteria);
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

}