<?php

namespace Wame\Core\Events;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Nette\Security\User;
use Wame\Core\Doctrine\Filters\SoftdeletableFilter;
use Wame\Core\Entities\BaseEntity;
use Wame\Core\Registers\RepositoryRegister;

/**
 * Class PrePersistListener
 *
 * @package Wame\Core\Events
 */
class PrePersistListener implements \Kdyby\Events\Subscriber
{
    /** @var User */
    private $user;
    
    /** @var RepositoryRegister */
    private $repositoryRegister;
    
    /** @var SoftdeletableFilter */
    private $softdeletableFilter;
    
    
    /**
     * PrePersistListener constructor.
     *
     * @param User $user    user
     */
    public function __construct(User $user, RepositoryRegister $repositoryRegister, SoftdeletableFilter $softdeletableFilter)
    {
        $this->user = $user;
        $this->repositoryRegister = $repositoryRegister;
        $this->softdeletableFilter = $softdeletableFilter;
    }
    

    /** {@inheritdoc} */
    public function getSubscribedEvents()
    {
        return [\Kdyby\Doctrine\Events::prePersist];
    }

    /**
     * Pre persist
     *
     * @param LifecycleEventArgs $lifecycleEventArgs
     */
    public function prePersist(LifecycleEventArgs $lifecycleEventArgs)
    {
        $entity = $lifecycleEventArgs->getEntity();
        
        $this->setCreateDate($entity);
        $this->setEditDate($entity);
        $this->setCreateUser($entity);
        $this->setEditUser($entity);
        
        $this->setSlug($entity);
    }
    
    
    /**
     * Set createDate
     * 
     * @param BaseEntity $entity    entity
     */
    private function setCreateDate($entity)
    {
        if(property_exists($entity, 'createDate')) {
            if(!$entity->getCreateDate()) {
                $entity->setCreateDate();
            }
        }
    }
    
    /**
     * Set editDate
     *
     * @param BaseEntity $entity    entity
     */
    private function setEditDate($entity)
    {
        if(property_exists($entity, 'editDate')) {
            if(!$entity->getEditDate()) {
                $entity->setEditDate();
            }
        }
    }
    
    /**
     * Set createUser
     * 
     * @param BaseEntity $entity    entity
     */
    private function setCreateUser($entity)
    {
        if(property_exists($entity, 'createUser')) {
            if(!$entity->getCreateUser()) {
                $entity->setCreateUser($this->user->getEntity());
            }
        }
    }
    
    /**
     * Set editUser
     * 
     * @param BaseEntity $entity    entity
     */
    private function setEditUser($entity)
    {
        if(property_exists($entity, 'editUser')) {
            if(!$entity->getEditUser()) {
                $entity->setEditUser($this->user->getEntity());
            }
        }
    }
    
    /**
     * Set slug
     * 
     * @param type $entity
     */
    private function setSlug($entity)
    {
        if(property_exists($entity, 'slug')) {
            $slugOrigin = $entity->getSlug();
            
            if($slugOrigin) {
                $repository = $this->repositoryRegister->getByName($entity->getClassName());
                $this->softdeletableFilter->isDisabled();
                $entitiesWithSameSlug = $repository->find(['slug LIKE' => $slugOrigin . '%', 'id !=' => $entity->getId()], ['id' => 'DESC']);
                $this->softdeletableFilter->isEnabled();

                if(!empty($entitiesWithSameSlug)) {
                    $postfixes = [];

                    foreach($entitiesWithSameSlug as $ewss) {
                        $r = preg_match_all("/$slugOrigin-?(\d+)$/", $ewss->getSlug(), $matches);

                        if($r>0) {
                            $postfixes[] = $matches[count($matches)-1][0];
                        }
                    }

                    sort($postfixes);

                    $oldPostfix = end($postfixes);
                    $postfix = is_numeric($oldPostfix) ? ($oldPostfix + 1) : 2;
                    $entity->setSlug($entity->getSlug() . '-' . $postfix);
                }
            }
        }
    }
    
}
