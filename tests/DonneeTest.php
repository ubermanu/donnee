<?php

use Donnee\Donnee;
use PHPUnit\Framework\TestCase;

final class DonneeTest extends TestCase
{
    /**
     * @var Donnee
     */
    protected Donnee $db;

    /**
     * Create Donnee instance and file
     */
    public function setUp(): void
    {
        touch('db-test.txt');
        $this->db = new Donnee('db-test.txt');
    }

    /**
     * Delete temp file
     */
    public function tearDown(): void
    {
        unlink('db-test.txt');
    }

    /**
     * @covers
     * @throws \Donnee\Exception
     */
    public function testCanInsert(): void
    {
        $this->db->insert(true);
        $this->db->insert(null);
        $this->db->insert(156);
        $this->db->insert('AbCdEfG_-=');

        $this->assertEquals(4, $this->db->count());
    }

    /**
     * @covers
     * @throws \Donnee\Exception
     */
    public function testCanInsertComplexData(): void
    {
        $obj = new stdClass();
        $obj->prop = 'value';

        $arr = [];
        $arr['prop'] = 'value';

        $this->db->insert($obj);
        $this->db->insert($arr);

        $this->assertEquals(2, $this->db->count());
        $this->assertEquals($obj, $this->db->get(1));
        $this->assertEquals($arr, $this->db->get(2));
    }

    /**
     * @covers
     * @throws \Donnee\Exception
     */
    public function testCanGet(): void
    {
        $this->db->insert(110);
        $this->db->insert(220);
        $this->db->insert(330);

        $this->assertEquals(3, $this->db->count());
        $this->assertEquals(220, $this->db->get(2));
        $this->assertEquals(330, $this->db->get(3));
        $this->assertEquals(110, $this->db->get(1));
    }

    /**
     * @covers
     * @throws \Donnee\Exception
     */
    public function testCanUpdate(): void
    {
        $this->db->insert(true);
        $this->db->update(1, 654);

        $this->assertEquals(1, $this->db->count());
        $this->assertEquals(654, $this->db->get(1));
    }

    /**
     * @covers
     * @throws \Donnee\Exception
     */
    public function testUnknownLineReturnsNull(): void
    {
        $this->db->insert('test');
        $this->assertEquals(null, $this->db->get(999999999999999));
    }

    /**
     * @covers
     * @throws \Donnee\Exception
     */
    public function testCanDelete(): void
    {
        $this->db->insert('1');
        $this->db->insert('2');
        $this->db->insert('3');

        $this->assertEquals(3, $this->db->count());

        $this->db->delete(1);

        $this->assertEquals(2, $this->db->count());
        $this->assertEquals(null, $this->db->get(1));
        $this->assertEquals('2', $this->db->get(2));
        $this->assertEquals('3', $this->db->get(3));
    }

    /**
     * @covers
     * @throws \Donnee\Exception
     */
    public function testInsertSpecialCharacters(): void
    {
        $special = 'éà@/\\%~-=<>>';
        $this->db->insert($special);
        $this->assertEquals($special, $this->db->get(1));
    }
}
