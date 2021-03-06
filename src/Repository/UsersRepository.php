<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\ORM\EntityRepository;

/**
 * UsersRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class UsersRepository extends EntityRepository
{
    public function findAllWithTriesOk()
    {
        return $this->getEntityManager()->createQueryBuilder()
                    ->select('u')
                    ->from(Users::class, 'u')
                    ->where('u.tries < :try')
                    ->setParameter('try', 3)
                    ->getQuery()
                    ->getResult();
    }
}
