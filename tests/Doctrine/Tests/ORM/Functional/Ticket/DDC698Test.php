<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\Tests\OrmFunctionalTestCase;
use Exception;

use function strtolower;

class DDC698Test extends OrmFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->_schemaTool->createSchema(
                [
                    $this->_em->getClassMetadata(DDC698Role::class),
                    $this->_em->getClassMetadata(DDC698Privilege::class),
                ]
            );
        } catch (Exception $e) {
        }
    }

    public function testTicket(): void
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p', 'r')
           ->from(__NAMESPACE__ . '\DDC698Privilege', 'p')
           ->leftJoin('p.roles', 'r');

        $sql = $qb->getQuery()->getSQL();

        $this->assertEquals(
            strtolower('SELECT p0_.privilegeID AS privilegeID_0, p0_.name AS name_1, r1_.roleID AS roleID_2, r1_.name AS name_3, r1_.shortName AS shortName_4 FROM Privileges p0_ LEFT JOIN RolePrivileges r2_ ON p0_.privilegeID = r2_.privilegeID LEFT JOIN Roles r1_ ON r1_.roleID = r2_.roleID'),
            strtolower($sql)
        );
    }
}

/**
 * @Table(name="Roles")
 * @Entity
 */
class DDC698Role
{
    /**
     *  @Id @Column(name="roleID", type="integer")
     *  @GeneratedValue(strategy="AUTO")
     */
    protected $roleID;

    /**
     * @var string
     * @Column(name="name", type="string", length=45)
     */
    protected $name;

    /**
     * @var string
     * @Column(name="shortName", type="string", length=45)
     */
    protected $shortName;



    /**
     * @ManyToMany(targetEntity="DDC698Privilege", inversedBy="roles")
     * @JoinTable(name="RolePrivileges",
     *     joinColumns={@JoinColumn(name="roleID", referencedColumnName="roleID")},
     *     inverseJoinColumns={@JoinColumn(name="privilegeID", referencedColumnName="privilegeID")}
     * )
     */
    protected $privilege;
}


/**
 * @Table(name="Privileges")
 * @Entity()
 */
class DDC698Privilege
{
    /**
     *  @Id  @Column(name="privilegeID", type="integer")
     *  @GeneratedValue(strategy="AUTO")
     */
    protected $privilegeID;

    /**
     * @var string
     * @Column(name="name", type="string", length=45)
     */
    protected $name;

    /** @ManyToMany(targetEntity="DDC698Role", mappedBy="privilege") */
    protected $roles;
}
