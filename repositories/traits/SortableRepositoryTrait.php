<?php

namespace Wame\Core\Repositories\Traits;


trait SortableRepositoryTrait 
{
	/**
	 * Resort items
	 * 
	 * @param array $criteria	criteria
	 * @param numeric $factor	factor
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
	 * @param array $criteria	criteria
	 * @param array $order	order
	 * @return int	Returns index of next
	 */
	public function getNextSort($criteria = [], $order = ['sort' => 'DESC']) 
    {
		$get = $this->get($criteria, $order);

        if ($get) {
            return $get->sort + 1;
        } else {
            return 1;
        }
    }
    
    
    /**
     * Move before
     * 
     * @param integer $itemId   item id
     * @param integer $nextId   next id
     */
    public function moveBefore($itemId, $nextId)
    {
        $item = $this->get(['id' => $itemId]);
        $next = $this->get(['id' => $nextId]);
        
        $item->sort = $next->sort;
        
        $higher = $this->find(['sort >=' => $item->sort, 'position' => $item->position, 'id !=' => $item->id]);
        
        foreach($higher as $l) {
            $l->sort++;
        }
    }
    
    /**
     * Move after
     * 
     * @param integer $itemId   item id
     * @param integer $prevId   prev id
     */
    public function moveAfter($itemId, $prevId)
    {
        $item = $this->get(['id' => $itemId]);
        $prev = $this->get(['id' => $prevId]);
        
        $item->sort = $prev->sort + 1;
        
        $higher = $this->find(['sort >' => $item->sort, 'position' => $item->position, 'id !=' => $item->id]);
        
        foreach($higher as $l) {
            $l->sort++;
        }
    }
    
    /**
     * Move
     * 
     * @param integer $itemId   item id
     * @param integer $prevId   prev id
     * @param integer $nextId   next id
     */
    public function move($itemId, $prevId, $nextId)
    {
        if($nextId) {
            $this->moveBefore($itemId, $nextId);
        } elseif ($prevId) {
            $this->moveAfter($itemId, $prevId);
        }
    }

}
