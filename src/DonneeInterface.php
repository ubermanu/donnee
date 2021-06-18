<?php

namespace Ubermanu\Donnee;

/**
 * Interface DonneeInterface
 * @package Ubermanu\Donnee
 */
interface DonneeInterface
{
    /**
     * Get the content of a stored line.
     *
     * @param int $id
     * @return mixed
     */
    public function get(int $id): mixed;

    /**
     * Insert data at the end of the file.
     * Returns the latest inserted line number.
     *
     * @param mixed $data
     * @return int
     */
    public function insert(mixed $data): int;

    /**
     * Update the data at a specific line.
     *
     * @param int $id
     * @param mixed $data
     * @return bool
     */
    public function update(int $id, mixed $data): bool;

    /**
     * Returns the number of lines in the db.
     *
     * @return int
     */
    public function count(): int;
}
