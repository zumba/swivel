<?php

namespace Zumba\Swivel;

class Behavior implements BehaviorInterface
{
    use \Psr\Log\LoggerAwareTrait;

    /**
     * Fully qualified feature slug.
     *
     * E.g. "Feature.New.VersionA"
     *
     * @var string
     */
    protected string $slug;

    /**
     * The strategy to be executed.
     *
     * @var callable
     */
    protected $strategy;

    /**
     * Zumba\Swivel\Behavior.
     *
     * @param string   $slug
     * @param callable $strategy
     */
    public function __construct(string $slug, callable $strategy)
    {
        $this->slug = $slug;
        $this->strategy = $strategy;
    }

    /**
     * Execute the behavior's callable and return the result.
     *
     * @param array $args
     *
     * @return mixed
     *
     * @see \Zumba\Swivel\BehaviorInterface
     */
    public function execute(array $args = []): mixed
    {
        $slug = $this->slug;
        if ($this->logger) {
            $this->logger->debug('Swivel - Executing behavior.', compact('slug', 'args'));
        }

        $method = $this->strategy;
        return $method(...$args);
    }

    /**
     * Get the behavior's slug.
     *
     * @return string
     *
     * @see \Zumba\Swivel\BehaviorInterface
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}
