<?php

namespace Beryllium\Cache\Statistics\Tracker;

/**
 * Interface for tracking cache statistics
 *
 * @package
 * @version $id$
 * @author Jeremy Livingston <jeremyjlivingston@gmail.com>
 * @license See LICENSE.md
 */
interface StatisticsTrackerInterface
{
    /**
     * Add a hit to the tracker
     */
    public function addHit(): void;

    /**
     * Add a miss to the tracker
     */
    public function addMiss(): void;

    /**
     * Write the current statistics to a persistence layer
     *
     * @return bool
     */
    public function write(): bool;
}
