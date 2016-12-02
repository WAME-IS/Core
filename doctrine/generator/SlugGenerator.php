<?php

namespace Wame\Core\Doctrine\Generator;

use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\EntityManager;
use Kdyby\Doctrine\Entities\BaseEntity;
use Wame\Utils\Strings;


class SlugGenerator extends AbstractIdGenerator
{
    public function generate(EntityManager $em, $entity)
    {
        $classMetadata = $em->getClassMetadata(get_class($entity));

        $entityName = $classMetadata->getName();
        $repository = $em->getRepository($entityName);
        $parentEntityColumns = $this->getParentEntityColumns($entity, $classMetadata, $entityName);
        $slug = Strings::webalize($entity->getSlug());
        $lang = $entity->getLang();

        $attempt = 1;
        $maxAttempt = 999;

        while (true) {
            $newSlug = $slug;

            if ($attempt > 1) {
                $newSlug .= '-' . $attempt;
            }

            $find = $repository->findOneBy(array_replace(['slug' => $newSlug, 'lang' => $lang], $parentEntityColumns));

            if ($find == null) {
                \Tracy\Debugger::barDump($newSlug);
                return $newSlug;
            }

            $attempt++;

            if ($attempt > $maxAttempt) {
                throw new \Exception('SlugGenerator worked hardly, but failed to generate unique ID');
            }
        }
    }


    /**
     * Get parent entity name
     *
     * @param string $entityName
     * @return string
     */
    private function getParentEntityName($entityName)
    {
        return str_replace('Lang', '', $entityName);
    }


    /**
     * Get parent entity column name
     *
     * @param BaseEntity $entity
     * @param string $parentEntityName
     * @return string
     */
    private function getParentEntityColumns($entity, $classMetadata, $entityName)
    {
        $parentEntityName = $this->getParentEntityName($entityName);
        $associations = $classMetadata->getAssociationsByTargetClass($parentEntityName);

        $return = [];

        foreach ($associations as $name => $association) {
            $parentEntity = $entity->$name;

            if ($parentEntity->getId()) {
                $return[$name . ' !='] = $parentEntity->$name;
            }
        }

        return $return;
    }

}
