<?php

namespace Wame\Core\Events;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Nette\Security\User;
use Wame\Core\Entities\BaseEntity;

/**
 * Class PrePersistListener
 *
 * @package Wame\Core\Events
 */
class PrePersistListener implements \Kdyby\Events\Subscriber
{
    /**
     * PrePersistListener constructor.
     *
     * @param User $user    user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
    
}
