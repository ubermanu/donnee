<?php

namespace Ubermanu\Donnee;

/**
 * Class Donnee
 * @package Ubermanu\Donnee
 */
class Donnee implements DonneeInterface
{
    /**
     * @var string
     */
    protected string $db;

    /**
     * Donnee constructor.
     * @param string $db
     */
    public function __construct(string $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function get(int $id): mixed
    {
        if ($id <= 0) {
            return null;
        }

        $cmd = sprintf("sed '%dq;d' %s", $id, $this->db);

        try {
            $res = exec($cmd);
            if ($res) {
                return $this->decode($res);
            }
        } catch (\Exception $e) {
            throw new Exception(sprintf("Can't get data from the db, line: %d", $id), 1624042588, $e);
        }

        return null;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function insert(mixed $data): int
    {
        $cmd = sprintf("echo %s >> %s", escapeshellarg($this->encode($data)), $this->db);

        try {
            exec($cmd);
        } catch (\Exception $e) {
            throw new Exception("Can't insert data into the db", 1624042500, $e);
        }

        return $this->count();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(int $id, mixed $data): bool
    {
        $cmd = sprintf("sed -i '%ds/.*/%s/' %s", $id, addcslashes($this->encode($data), '\\/'), $this->db);

        try {
            exec($cmd);
        } catch (\Exception $e) {
            throw new Exception(sprintf("Can't update the db, line: %d", $id), 1624042470, $e);
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $cmd = sprintf("sed -i '%ds/.*//' %s", $id, $this->db);

        try {
            exec($cmd);
        } catch (\Exception $e) {
            throw new Exception(sprintf("Can't delete the line: %d", $id), 1624042470, $e);
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function count(): int
    {
        $cmd = sprintf("sed -n '$=' %s || echo 0", $this->db);

        try {
            $res = exec($cmd);
        } catch (\Exception $e) {
            throw new Exception("Can't get the line count of the db", 1624042370, $e);
        }

        return intval($res);
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function encode(mixed $data): string
    {
        return serialize($data);
    }

    /**
     * @param string $row
     * @return mixed
     */
    protected function decode(string $row): mixed
    {
        return unserialize($row);
    }
}
