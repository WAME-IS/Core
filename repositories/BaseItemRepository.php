<?php

namespace Wame\Core\Repositories;

use Wame\Core\Repositories\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

abstract class BaseItemRepository extends BaseRepository
{
    /** @var BaseRegister */
	protected $register;
    
    
    public function __construct($entityClass)
    {
        parent::__construct($entityClass);
    }
    
    
    /**
     * Get items
     * 
     * @param string $type          type
     * @param integer $entityId     entity id
     * @param string $order         order
     * @return BaseEntity[]
     */
    public function findEntities($type = null, $entityId = null, $order = null)
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        $qb->select('e')
			->from($this->getItemClassName(), 'ie')
			->innerJoin($this->register->getByName($type)->getClassName(), 'e', Join::WITH, 'ie.item_id = e.id')
            ->innerJoin($this->getClassName(), 'i', Join::WITH, "ie.{$this->getAlias()} = i");
//			->andWhere($qb->expr()->eq('e.type', $type));
        
        if($entityId) {
            $qb->andWhere($qb->expr()->eq("i." . $this->getAlias(), $entityId));
        }
        
        if($order) {
            $qb->orderBy('ie.id', $order);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Get entities
     * 
     * @param string $type      type
     * @param integer $itemId   item id
     * @return BaseEntity[]
     */
    public function findItems($type, $itemId = null)
    {
        $qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('i')
			->from($this->getItemClassName(), 'ie')
			->leftJoin($this->getClassName(), 'i', Join::WITH, "ie.{$this->getAlias()} = i")
            ->andWhere('i.type = :type')->setParameter('type', $type);
		
		if($itemId) {
            $qb->andWhere($qb->expr()->eq("ie.item_id", $itemId));
		}

		return $qb->getQuery()->getResult();
    }
    
    /**
     * Find item entities
     * 
     * @param string $type      type
     * @param integer $itemId   item id 
     * @return BaseEntity[]
     */
    public function findItemEntities($type, $itemId = null)
    {
        $qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('e')
		   ->from($this->getItemClassName(), 'i')
		   ->leftJoin($this->getClassName(), 'e', Join::WITH, "i.{$this->getAlias()} = e");
		
		if($itemId) {
			$qb->where('i.item_id = ' . $itemId);
		}
		
		return $qb->getQuery()->getResult();
    }
    
    
    /**
     * Generate pairs
     * 
     * @param BaseEntity[] $array   entities
     * @return array
     */
    protected function generatePairs($array)
    {
        $arr = [];
		
		foreach($array as $a) {
            // TODO: treba langs[] ?
			$arr[$a->id] = $a->langs[$this->lang]->title;
		}
		
		return $arr;
    }
    
    
    /**
     * Get alias
     * 
     * @return string
     */
    abstract protected function getAlias();
    
    /**
     * Get class name
     * 
     * @return string
     */
    abstract protected function getClassName();
    
    /**
     * Get item class name
     * 
     * @return string
     */
    abstract protected function getItemClassName();
    
}
