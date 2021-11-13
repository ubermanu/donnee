<?php

namespace Donnee;

class Donnee
{
    /**
     * @var string
     */
    protected string $src;

    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->src = $filename;

        // Keep special characters when escaping shell commands
        // https://www.php.net/manual/en/function.escapeshellarg.php#99213
        setlocale(LC_CTYPE, 'en_US.UTF-8');
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

        $cmd = sprintf("sed '%dq;d' %s", $id, $this->src);

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
        $cmd = sprintf("echo %s >> %s", escapeshellarg($this->encode($data)), $this->src);

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
     * @return array
     * @throws Exception
     */
    public function insertMany(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $echo = '';
        $count = $this->count();

        foreach ($rows as $row) {
            $echo .= sprintf("echo %s ; ", escapeshellarg($this->encode($row)));
        }

        $cmd = sprintf("(%s) >> %s", rtrim($echo, '; '), $this->src);

        try {
            exec($cmd);
        } catch (\Exception $e) {
            throw new Exception("Can't insert data into the db", 1624042500, $e);
        }

        return range($count, $count + count($rows) - 1);
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
        $cmd = sprintf("sed -i '%ds/.*/%s/' %s", $id, addcslashes($this->encode($data), '\\/'), $this->src);

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
        $cmd = sprintf("sed -i '%ds/.*//' %s", $id, $this->src);

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
        $cmd = sprintf("cat %s | sed '/^\s*$/d' | wc -l", $this->src);

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
