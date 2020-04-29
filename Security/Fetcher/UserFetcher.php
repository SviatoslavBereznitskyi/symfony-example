<?php

declare(strict_types=1);

namespace App\Security\Fetcher;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Statement;

/**
 * Class UserFetcher
 */
class UserFetcher
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * UserFetcher constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $email
     *
     * @return AuthView|null
     */
    public function findForAuthByEmail(string $email): ?AuthView
    {
        $select = [
            'u.id',
            'u.email',
            'u.password_hash',
            'u.role',
            'u.status',
        ];

        /** @var Statement $stmt */
        $stmt = $this->connection->createQueryBuilder()
            ->select(...$select)
            ->from('auth_users', 'u')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);

        /** @var AuthView|false $result */
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @param string $network
     * @param string $identity
     *
     * @return AuthView|null
     */
    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {
        $select = [
            'u.id',
            'u.email',
            'u.password_hash',
            'u.role',
            'u.status',
        ];

        /** @var Statement $stmt */
        $stmt = $this->connection->createQueryBuilder()
            ->select(...$select)
            ->from('auth_users', 'u')
            ->innerJoin('u', 'auth_user_networks', 'n', 'n.user_id = u.id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);

        /** @var AuthView|false $result */
        $result = $stmt->fetch();

        return $result ?: null;
    }
}
