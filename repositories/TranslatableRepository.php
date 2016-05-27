<?php

namespace Wame\Core\Repositories;

abstract class TranslatableRepository extends BaseRepository {

	/**
	 * Get one article by criteria
	 * 
	 * @param array $criteria
	 * @param array $orderBy
	 */
	public function get($criteria = [], $orderBy = []) {
		$qb = $this->entity->createQueryBuilder('a');

		if (!isset($criteria['lang'])) {
			$criteria['lang'] = $this->lang;
		}

		$qb->whereCriteria($this->autoPrefixParams($criteria))
				->autoJoinOrderBy($this->autoPrefixParams($orderBy));

		$entity = $qb->setMaxResults(1)
				->getQuery()
				->getSingleResult();

		if ($entity) {
			$entity->setCurrentLang($this->lang);
		}
		return $entity;
	}

	/**
	 * Get all entries by criteria
	 * 
	 * @param array $criteria
	 * @param array $orderBy
	 * @param string $limit
	 * @param string $offset
	 */
	public function find($criteria = [], $orderBy = [], $limit = null, $offset = null) {
		$articleEntity = $this->entity->findBy($criteria, $orderBy, $limit, $offset);

		return $articleEntity;
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
	public function findPairs($criteria = [], $value = null, $orderBy = [], $key = 'id') {
		return $this->entity->findPairs($criteria, $value, $orderBy, $key);
	}

	/**
	 * Get all entries in pairs
	 * 
	 * @param Array $criteria	criteria
	 * @param String $key		key
	 * @return Array			entries
	 */
	public function findAssoc($criteria = [], $key = 'id') {
		return $this->entity->findAssoc($criteria, $key);
	}

	/**
	 * Return count of articles
	 * 
	 * @param array $criteria	criteria
	 * @return integer			count
	 */
	public function countBy($criteria = []) {
		return $this->entity->countBy($criteria);
	}

	/**
	 * Remove entities
	 * 
	 * @param type $criteria	
	 */
	public function remove($criteria = []) {
		$entities = $this->find($criteria);

		foreach ($entities as $entity) {
			$this->entityManager->remove($entity);
		}
	}

	/**
	 * Can be used to automaticly add correct prefix to language fields.
	 * 
	 * @param array $params
	 * @return array
	 */
	private function autoPrefixParams($params) {
		if ($params && is_array($params)) {
			$meta = $this->entity->getClassMetadata();
			foreach (array_keys($params) as $key) {
				if (!array_key_exists($key, $meta->columnNames)) {
					//rename key if found in association
					$this->autoPrefixParamsAssoc($params, $key, $meta->associationMappings['langs']);
				}
			}
		}

		return $params;
	}

	private function autoPrefixParamsAssoc(&$params, $key, $assoc) {
		$meta = $this->entityManager->getClassMetadata($assoc['targetEntity']);
		if (array_key_exists($key, $meta->columnNames)) {
			$this->autoPrefixParamsRename($params, $key, $assoc['fieldName'] . '.' . $key);
		}
	}

	private function autoPrefixParamsRename(&$params, $oldKey, $newKey) {
		$tmp = $params[$oldKey];
		unset($params[$oldKey]);
		$params[$newKey] = $tmp;
	}
}
