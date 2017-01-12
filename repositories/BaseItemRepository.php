<?php

namespace Wame\Core\Repositories;

use Wame\Core\Entities\BaseEntity;
use Wame\Core\Registers\BaseRegister;
use Doctrine\ORM\Query\Expr\Join;

abstract class BaseItemRepository extends BaseRepository
{
    /** @var BaseRegister */
	protected $register;
    
    
    /**
     * Find entities
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
     * Find items
     * 
     * @param string $type      type
     * @param integer $itemId   item id
     * @return BaseEntity[]
     */
    public function findItems($type = null, $itemId = null, $main = null)
    {
        $qb = $this->entityManager->createQueryBuilder();
		
		$qb->select('i')
			->from($this->getItemClassName(), 'ie')
			->leftJoin($this->getClassName(), 'i', Join::WITH, "ie.{$this->getAlias()} = i");
            
        if($type) {
            $qb->andWhere('i.type = :type')->setParameter('type', $type);
        }
		
		if($itemId) {
            $qb->andWhere($qb->expr()->eq("ie.item_id", $itemId));
		}
        
        if($main !== null) {
            $qb->andWhere($qb->expr()->eq("ie.main", $main));
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
