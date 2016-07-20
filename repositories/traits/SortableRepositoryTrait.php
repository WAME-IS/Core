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

}
