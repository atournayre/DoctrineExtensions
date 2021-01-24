<?php

namespace DoctrineExtensions\Tests\Query\Mysql;

use DoctrineExtensions\Tests\Query\MysqlTestCase;

class CoalesceDate extends MysqlTestCase
{
    public function testCoalesceDateSimple()
    {
        $this->assertDqlProducesSql(
            'SELECT COALESCE_DATE(d.created, d.created) FROM DoctrineExtensions\Tests\Entities\Date d',
            'SELECT COALESCE(d0_.created, d0_.created) AS sclr_0 FROM Date d0_'
        );
    }

    public function testCoalesceDateWtihSubSelect()
    {
        $this->assertDqlProducesSql(
            'SELECT COALESCE_DATE(d.created, (SELECT bp.created FROM DoctrineExtensions\Tests\Entities\BlogPost bp WHERE bp.id = 1)) FROM DoctrineExtensions\Tests\Entities\Date d',
            'SELECT COALESCE(d0_.created, (SELECT bp1_.created AS dctrn__2 FROM BlogPost bp1_ WHERE bp1_.id = 1)) AS sclr_0 FROM Date d0_'
        );
    }
}
