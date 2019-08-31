<?php

namespace Beryllium\Cache\Statistics\Tracker;

/**
 * Filecache statistics tracker implementation
 *
 * @package
 * @version $id$
 * @author Yaroslav Nechaev <mail@remper.ru>
 * @license See LICENSE.md
 */
class FilecacheStatisticsTracker implements StatisticsTrackerInterface
{
    private $hits = 0;
    private $misses = 0;
    private $path;

    /**
     * FilecacheStatisticsTracker constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;

        if (file_exists($this->getFilename())) {
            $this->initialize();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addHit(): void
    {
        $this->hits++;
    }

    /**
     * {@inheritDoc}
     */
    public function addMiss(): void
    {
        $this->misses++;
    }

    /**
     * Write statistics to cache directory
     */
    public function write(): bool
    {
        $stats = array('hits' => $this->hits, 'misses' => $this->misses);

        file_put_contents($this->getFilename(), serialize($stats));

        /** interface return type is bool in annotation. May be void? */
        return true;
    }

    /**
     * Initialize statistics from an existing file
     */
    private function initialize(): void
    {
        $fileContent = file_get_contents($this->getFilename());
        if (false === $fileContent) {
            return;
        }
        $stats = unserialize($fileContent);

        $this->hits = isset($stats['hits']) ? $stats['hits'] : 0;
        $this->misses = isset($stats['misses']) ? $stats['misses'] : 0;
    }

    /**
     * Get the filename from the provided path
     *
     * @return string
     */
    private function getFilename(): string
    {
        return $this->path . DIRECTORY_SEPARATOR . '__stats';
    }
}
