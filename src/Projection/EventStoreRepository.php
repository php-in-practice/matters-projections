<?php

namespace PhpInPractice\Matters\Projection;

final class EventStoreRepository implements Repository
{
    /** @var string */
    private $projectionClassName;

    /** @var string */
    private $projectionName;

    /** @var Driver */
    private $projectionsDriver;

    /** @var StateSerializer */
    private $resultSerializer;

    public function __construct(
        Driver $projectionsDriver,
        StateSerializer $resultSerializer,
        $projectionClassName,
        $projectionName = null
    ) {
        $this->projectionClassName = $projectionClassName;
        $this->projectionName      = $projectionName ?: $this->normalizeClassName($projectionClassName);
        $this->projectionsDriver   = $projectionsDriver;
        $this->resultSerializer    = $resultSerializer;
    }

    public function result($partition = null)
    {
        $definition = $this->projectionsDriver->get($this->projectionName);

        return $this->resultSerializer->unserialize(
            $this->projectionClassName,
            $this->projectionsDriver->result($definition, $partition)
        );
    }

    /**
     * @param $fqcn
     *
     * @return string
     */
    private function normalizeClassName($fqcn)
    {
        return strtolower(str_replace('\\', '.', trim($fqcn, '\\/ _-')));
    }
}
