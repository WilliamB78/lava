<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 23/04/2018
 * Time: 14:22
 */

namespace App\EvenSuscriber;


use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class EncryptedPasswordSuscriber implements EventSubscriber
{
    /** @var UserPasswordEncoder $passwordEncoder */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        $this->encodeUserPassword($entity);

        if ($entity->getRoles()[0] == "ROLE_UTILISATEUR") {
            $this->addRole($entity);
        }
        if ($entity->getRoles()[0] == "ROLE_SECRETARY") {
            $this->addSecretaryRole($entity);
        }
        if ($entity->getRoles()[0] == "ROLE_ADMIN") {
            $this->addAdminRole($entity);
        }

    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        $this->encodeUserPassword($entity);
        // necessary to force the update to see the change
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    public function encodeUserPassword(User $user){

        if (!$user->getPassword()) {
            return;
        }

        $encoded = $this->passwordEncoder->encodePassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($encoded);
    }

    /**
     * @param User $user
     */
    public function addUserRole(User $user){
        $user->addRole("ROLE_CAN_DO_BOOKING");
        $user->addRole("ROLE_CAN_SEE_CALENDAR");
    }

    /**
     * @param User $user
     */
    public function addSecretaryRole(User $user){
        $user->addRole("ROLE_CAN_EDIT_ROOM");
        $user->addRole("ROLE_CAN_SEE_CALENDAR");
    }

    /**
     * @param User $user
     */
    public function addAdminRole(User $user){
        $user->addRole("ROLE_CAN_ADD_ROOM");
        $user->addRole("ROLE_CAN_REMOVE_ROOM");
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }
}