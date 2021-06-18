<?php

use PHPUnit\Framework\TestCase;
use Ubermanu\Donnee\Donnee;

/**
 * Class DonneeTest
 */
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
     * @throws \Ubermanu\Donnee\Exception
     */
    public function testCanInsert(): void
    {
        $this->db->insert(true);
        $this->db->insert(null);
        $this->db->insert(156);
        $this->db->insert('AbCdEfG_-=');

        $this->assertEquals($this->db->count(), 4);
    }

    /**
     * @covers
     * @throws \Ubermanu\Donnee\Exception
     */
    public function testCanInsertComplexData(): void
    {
        $obj = new stdClass();
        $obj->prop = 'value';

        $arr = [];
        $arr['prop'] = 'value';

        $this->db->insert($obj);
        $this->db->insert($arr);

        $this->assertEquals($this->db->count(), 2);
        $this->assertEquals($this->db->get(1), $obj);
        $this->assertEquals($this->db->get(2), $arr);
    }

    /**
     * @covers
     * @throws \Ubermanu\Donnee\Exception
     */
    public function testCanGet(): void
    {
        $this->db->insert(110);
        $this->db->insert(220);
        $this->db->insert(330);

        $this->assertEquals($this->db->count(), 3);
        $this->assertEquals($this->db->get(2), 220);
        $this->assertEquals($this->db->get(3), 330);
        $this->assertEquals($this->db->get(1), 110);
    }

    /**
     * @covers
     * @throws \Ubermanu\Donnee\Exception
     */
    public function testCanUpdate(): void
    {
        $this->db->insert(true);
        $this->db->update(1, 654);

        $this->assertEquals($this->db->count(), 1);
        $this->assertEquals($this->db->get(1), 654);
    }

    /**
     * @covers
     * @throws \Ubermanu\Donnee\Exception
     */
    public function testUnknownLineReturnsNull(): void
    {
        $this->db->insert('test');
        $this->assertEquals($this->db->get(999999999999999), null);
    }

    /**
     * @covers
     * @throws \Ubermanu\Donnee\Exception
     */
    public function testCanDelete(): void
    {
        $this->db->insert('1');
        $this->db->insert('2');
        $this->db->insert('3');

        $this->assertEquals($this->db->count(), 3);

        $this->db->delete(1);

        $this->assertEquals($this->db->count(), 3);
        $this->assertEquals($this->db->get(1), null);
        $this->assertEquals($this->db->get(2), '2');
        $this->assertEquals($this->db->get(3), '3');
    }
}
