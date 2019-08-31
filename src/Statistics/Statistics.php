<?php

namespace Beryllium\Cache\Statistics;

/**
 * Class for unified statistics control
 *
 * @package
 * @version $id$
 * @author Yaroslav Nechaev <mail@remper.ru>
 * @license See LICENSE.md
 */
class Statistics
{
    /** @var int $hits */
    protected $hits;
    /** @var int $misses */
    protected $misses;
    /** @var array $additionalData */
    protected $additionalData;

    /**
     * Create statistics object based on raw data
     *
     * @param int $hits
     * @param int $misses
     */
    public function __construct(int $hits = 0, int $misses = 0)
    {
        $this->additionalData = [];
        $this->hits = $hits;
        $this->misses = $misses;
    }

    /**
     * Hits
     *
     * @return int
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * Misses
     *
     * @return int
     */
    public function getMisses(): int
    {
        return $this->misses;
    }

    /**
     * Get helpfulness percentage
     *
     * @return float
     */
    public function getHelpfulness(): float
    {
        if ($this->hits + $this->misses === 0) {
            return 0.00;
        }

        return (float)number_format(($this->hits / ($this->hits + $this->misses)) * 100, 2);
    }

    /**
     * @param mixed[] $additionalData
     */
    public function setAdditionalData(array $additionalData): void
    {
        $this->additionalData = $additionalData;
    }

    /**
     * @return mixed[]
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    /**
     * @return mixed[]
     */
    public function getFormattedArray(): array
    {
        return array_merge(
            $this->getAdditionalData(),
            array(
                'Hits' => $this->getHits(),
                'Misses' => $this->getMisses(),
                'Helpfulness' => $this->getHelpfulness()
            )
        );
    }
}
