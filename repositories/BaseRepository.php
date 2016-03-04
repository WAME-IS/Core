<?php

namespace Wame\Core\Repositories;

class BaseRepository extends \Nette\Object implements \Kdyby\Persistence\Queryable 
{
	/** @var \Nette\DI\Container */
	private $container;
	
	/** @var string */
	private $name;

	/** @var \Kdyby\Doctrine\EntityRepository */
	private $repo;

	public function __construct(\Nette\DI\Container $container, $name) 
	{
		$this->container = $container;
		$this->name = $this->prefix . $name;
	}
	
	protected function getRepo() 
	{
		if (!$this->repo) {
			$class = $this->entityHandler->getBuilder($this->name)->getClass();
			$this->repo = $this->em->getRepository($class);
		}
		
		return $this->repo;
	}

	public function getPrefix()
	{
		return $this->container->parameters['database']['prefix'];
	}

	public function getName() 
	{
		return $this->name;
	}

	public function getClass() 
	{
		return $this->entityHandler->getBuilder($this->name)->getClass();
	}

	public function save($entity) 
	{
		$this->em->persist($entity);
		$this->em->flush($entity);
	}

	public function flush($entity = null) 
	{
		$this->em->persist($entity);
		$this->em->flush($entity);
	}
	
	public function createQuery($dql = NULL) 
	{
		$this->getRepo()->createQuery($dql);
	}
	
	public function createNativeQuery($sql, \Doctrine\ORM\Query\ResultSetMapping $rsm) 
	{
		$this->getRepo()->createNativeQuery($sql, $rsm);
	}
	
	/**
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	public function createQueryBuilder($alias = NULL, $indexBy = NULL) 
	{
		return $this->getRepo()->createQueryBuilder($alias, $indexBy);
	}
	
}