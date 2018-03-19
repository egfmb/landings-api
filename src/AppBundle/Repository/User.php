<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 7/07/16
 * Time: 12:09 PM
 */

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class User extends EntityRepository
{

    public function findByRole($role, array $criteria = array())
    {
        /** @var QueryBuilder $qBuilder */
        $qBuilder = $this->createQueryBuilder('u');
        $qBuilder->where($qBuilder->expr()->like('u.roles', ':role'))
            ->setParameter('role', "%$role%");

        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $qBuilder->andWhere("u.{$field} = :{$field}")
                    ->setParameter($field, $value);
            }
        }

        return $qBuilder->getQuery()
            ->getResult();
    }

    /**
     * @param   string  $role
     * @param   array   $criteria
     * @return  \AppBundle\Entity\User
     */
    public function findOneByRole($role, array $criteria = array())
    {
        /** @var QueryBuilder $qBuilder */
        $qBuilder = $this->createQueryBuilder('u');
        $qBuilder->where($qBuilder->expr()->like('u.roles', ':role'))
            ->setParameter('role', "%$role%")
            ->setMaxResults(1);

        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $qBuilder->andWhere("u.{$field} = :{$field}")
                    ->setParameter($field, $value);
            }
        }

        return $qBuilder->getQuery()->getOneOrNullResult();
    }
}
