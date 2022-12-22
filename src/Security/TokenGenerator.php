<?php

namespace App\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class TokenGenerator extends AbstractIdGenerator
{
    public function generate(EntityManager $em, $entity)
    {
        $entityClass = $em->getClassMetadata(get_class($entity))->getName();

        $attempt = 100;
        while ($attempt--) {
            $id = sha1(random_bytes(32));
            $item = $em->find($entityClass, $id);
            if (!$item) {
                foreach ($em->getUnitOfWork()->getScheduledEntityInsertions() as $_entity) {
                    if (is_subclass_of($_entity, $entityClass) && $_entity->getId() === $id) {
                        continue 2;
                    }
                }

                return $id;
            }
        }

        throw new \Exception('Failed all attempts to generate unique token');
    }
}
