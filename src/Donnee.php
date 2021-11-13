<?php

namespace Donnee;

class Donnee
{
    /**
     * @var string
     */
    protected string $db;

    /**
     * @param string $db
     */
    public function __construct(string $db)
    {
        $this->db = $db;
    }

    /**
     * Get the content of a stored line.
     *
     * @param int $id
     * @return mixed
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
     * Insert data at the end of the file.
     * Returns the latest inserted line number.
     *
     * @param mixed $data
     * @return int
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
     * Insert multiple rows at the end of the file.
     *
     * @param array $rows
     * @throws Exception
     */
    public function insertMany(array $rows): void
    {
        $echo = '';

        foreach ($rows as $row) {
            $echo .= sprintf("echo %s ; ", escapeshellarg($this->encode($row)));
        }

        $cmd = sprintf("(%s) >> %s", rtrim($echo, '; '), $this->db);

        try {
            exec($cmd);
        } catch (\Exception $e) {
            throw new Exception("Can't insert data into the db", 1624042500, $e);
        }
    }

    /**
     * Update the data at a specific line.
     *
     * @param int $id
     * @param mixed $data
     * @return bool
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
     * Remove a line from the file.
     *
     * @param int $id
     * @return bool
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
     * Returns the number of lines in the db.
     *
     * @return int
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
