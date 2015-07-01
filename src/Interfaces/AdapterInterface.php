<?php

namespace Interfaces;

/**
 * @author recchia
 */
interface AdapterInterface
{
    /**
     * Adapter constructor.
     *
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * Find one book by ISBN.
     *
     * @param string $isbn
     *
     * @return array
     *
     * @throws BookNotFoundException
     */
    public function findOne($isbn);

    /**
     * Find books by ISBN.
     *
     * @param array $isbns
     *
     * @return array
     */
    public function find(array $isbns);
}
