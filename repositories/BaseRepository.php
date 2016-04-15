<?php

namespace Wame\Core\Repositories;

use Nette\DI\Container;
use Nette\Utils\DateTime;
use Kdyby\Doctrine\EntityManager;
use h4kuna\Gettext\GettextSetup;
use Nette\Security\User;

class BaseRepository extends \Nette\Object
{
	/** @var Container */
	public $container;
	
	/** @var EntityManager */
	public $entityManager;

	/** @var string */
	public $lang;

	/** @var User */
	public $user;

	public function __construct(
		Container $container, 
		EntityManager $entityManager,
		GettextSetup $translator,
		User $user
	) {
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->lang = $translator->getLanguage();
		$this->user = $user;
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

	public function select($alias = NULL) {
		return $alias;
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