<?php

namespace Interfaces;

use Silex\Application;

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
     * @param Application $app
     *
     * @return array
     */
    public function find(array $isbns, Application $app);
}
