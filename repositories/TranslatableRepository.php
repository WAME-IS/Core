<?php

namespace Wame\Core\Repositories;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\AbstractQuery;

abstract class TranslatableRepository extends BaseRepository
{

    /**
     * Get one article by criteria
     * 
     * @param array $criteria
     * @param array $orderBy
     */
    public function get($criteria = [], $orderBy = [])
    {
        $qb = $this->entity->createQueryBuilder('a');

        if (!isset($criteria['lang'])) {
            $criteria['lang'] = $this->lang;
        }

        $qb->whereCriteria($this->autoPrefixParams($criteria))
            ->autoJoinOrderBy($this->autoPrefixParams($orderBy));

        try {
            $entity = $qb->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();

            $entity->setCurrentLang($this->lang);
            return $entity;
        } catch (NoResultException $e) {
            return null;
        }
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
        $qb = $this->entity->createQueryBuilder('a');

        if (!isset($criteria['lang'])) {
            $criteria['lang'] = $this->lang;
        }

        $qb->whereCriteria($this->autoPrefixParams($criteria));
        if ($orderBy) {
            $qb->autoJoinOrderBy($this->autoPrefixParams($orderBy));
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }

        $result = $qb->getQuery()
            ->getResult();

        foreach ($result as $entity) {
            $entity->setCurrentLang($this->lang);
        }
        return $result;
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
    public function findPairs($criteria = [], $value = null, $orderBy = [], $key = NULL)
    {

        if (!$key) {
            $key = $this->entity->getClassMetadata()->getSingleIdentifierFieldName();
        }

        $query = $this->entity->createQueryBuilder('e')
            ->whereCriteria($this->autoPrefixParams($criteria))
            ->select("e.$value", "e.$key")
            ->resetDQLPart('from')->from($this->entity->getClassName(), 'e', 'e.' . $key)
            ->autoJoinOrderBy($this->autoPrefixParams((array) $orderBy))
            ->getQuery();

        return array_map(function ($row) {
            $entity = reset($row);
            // ? $entity->setCurrentLang($this->lang);
            return $entity;
        }, $query->getResult(AbstractQuery::HYDRATE_ARRAY));
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
        $qb = $this->entity->createQueryBuilder('e')
                ->whereCriteria($this->autoPrefixParams($criteria))
                ->resetDQLPart('from')->from($this->entity->getClassName(), 'e', 'e.' . $key);

        $result = $qb->getQuery()->getResult();
        foreach ($result as $entity) {
            $entity->setCurrentLang($this->lang);
        }
        return $result;
    }

    /**
     * Return count of articles
     * 
     * @param array $criteria	criteria
     * @return integer			count
     */
    public function countBy($criteria = [])
    {
        return (int) $this->entity->createQueryBuilder('e')
                ->whereCriteria($this->autoPrefixParams($criteria))
                ->select('COUNT(e)')
                ->getQuery()->getSingleScalarResult();
    }

    /**
     * Can be used to automaticly add correct prefix to language fields.
     * 
     * @param array $params
     * @return array
     */
    private function autoPrefixParams($params)
    {
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

    private function autoPrefixParamsAssoc(&$params, $key, $assoc)
    {
        $meta = $this->entityManager->getClassMetadata($assoc['targetEntity']);
        if (array_key_exists($key, $meta->columnNames)) {
            $this->autoPrefixParamsRename($params, $key, $assoc['fieldName'] . '.' . $key);
        }
    }

    private function autoPrefixParamsRename(&$params, $oldKey, $newKey)
    {
        $tmp = $params[$oldKey];
        unset($params[$oldKey]);
        $params[$newKey] = $tmp;
    }
}
