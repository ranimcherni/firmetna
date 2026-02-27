<?php

namespace App\Repository;

use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publication>
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    /** @return Publication[] */
    public function findAllOrderByDateDesc(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByDateDescQuery(): \Doctrine\ORM\Query
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.dateCreation', 'DESC')
            ->getQuery();
    }

    public function findWithCommentaires(int $id): ?Publication
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.commentaires', 'c')
            ->addSelect('c')
            ->leftJoin('c.auteur', 'ca')
            ->addSelect('ca')
            ->leftJoin('c.reponses', 'r')
            ->addSelect('r')
            ->leftJoin('r.auteur', 'ra')
            ->addSelect('ra')
            ->leftJoin('p.likes', 'l')
            ->addSelect('l')
            ->leftJoin('l.user', 'lu')
            ->addSelect('lu')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchQuery(string $search = null, string $type = null, string $sort = 'date'): \Doctrine\ORM\Query
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.auteur', 'a')
            ->addSelect('a');

        if ($search) {
            $qb->andWhere('p.titre LIKE :search OR p.contenu LIKE :search OR a.nom LIKE :search OR a.prenom LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($type && in_array($type, [Publication::TYPE_IDEE, Publication::TYPE_PROBLEME])) {
            $qb->andWhere('p.type = :type')
                ->setParameter('type', $type);
        }

        if ($sort === 'popularite') {
            $qb->leftJoin('p.likes', 'l')
                ->addSelect('COUNT(l.id) as HIDDEN likesCount')
                ->groupBy('p.id', 'a.id')
                ->orderBy('likesCount', 'DESC')
                ->addOrderBy('p.dateCreation', 'DESC');
        } elseif ($sort === 'commentaires') {
            $qb->leftJoin('p.commentaires', 'c')
                ->addSelect('COUNT(c.id) as HIDDEN commentsCount')
                ->groupBy('p.id', 'a.id')
                ->orderBy('commentsCount', 'DESC')
                ->addOrderBy('p.dateCreation', 'DESC');
        } else {
            $qb->orderBy('p.dateCreation', 'DESC');
        }

        return $qb->getQuery();
    }

    /**
     * @return array Returns an array of most liked publications
     */
    public function findMostLiked(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->select('p as publication', 'COUNT(l.id) as likesCount')
            ->leftJoin('p.likes', 'l')
            ->groupBy('p.id')
            ->orderBy('likesCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array Returns an array of most commented publications
     */
    public function findMostCommented(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->select('p as publication', 'COUNT(c.id) as commentsCount')
            ->leftJoin('p.commentaires', 'c')
            ->groupBy('p.id')
            ->orderBy('commentsCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
