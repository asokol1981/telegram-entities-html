<?php

declare(strict_types=1);

/*
 * This file is part of Telegram Entities HTML.
 *
 * (c) Aleksei Sokolov <asokol.beststudio@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ASokol1981\Telegram\Entities\Html;

/**
 * Renders Telegram message entities into HTML
 *
 * @see https://core.telegram.org/bots/api#messageentity
 * @see https://core.telegram.org/bots/api#formatting-options
 */
class Renderer
{
    /**
     * Entities that start at the given position
     *
     * @var array
     */
    protected $start_positions = [];

    /**
     * Entities that end at the given position
     *
     * @var array
     */
    protected $end_positions = [];

    /**
     * Fix inaccuracies
     *
     * @var bool
     */
    protected $fix_inaccuracies = false;

    /**
     * Throw errors
     *
     * @var bool
     */
    protected $throw_errors = false;

    /**
     * Opened tags
     *
     * @var array
     */
    protected $opened = [];

    /**
     * Tags provider
     *
     * @var TagsProviderInterface
     */
    protected $tags_provider;

    /**
     * Constructor
     *
     * @param TagsProviderInterface $tags_provider
     */
    public function __construct(?TagsProviderInterface $tags_provider = null)
    {
        $this->tags_provider = $tags_provider ?: new TagsProvider();
    }

    /**
     * Render entities to html
     *
     * @param string $text
     * @param array $entities
     * @return string
     */
    public function render(string $text, array $entities): string
    {
        if (!mb_strlen($text) || !$entities) {
            return $text;
        }

        foreach ($entities as $entity) {
            if (!$this->isValidEntity($entity)) {
                continue;
            }

            $this->start_positions[$entity['offset']][] = $entity;
            $position = $entity['offset'] + $entity['length'];
            if (!isset($this->end_positions[$position])) {
                $this->end_positions[$position] = [];
            }
            // In reverse order
            array_unshift($this->end_positions[$position], $entity);
        }

        if (!$this->start_positions) {
            return $text;
        }

        $previous_encoding = mb_internal_encoding();
        if (! $this->setInternalEncodingToUtf8()) {
            if ($this->throw_errors) {
                throw new \Exception('Failed to set internal encoding to UTF-8.');
            }

            return $text;
        }

        try {
            return $this->process($text);
        } catch (\Throwable $e) {
            if ($this->throw_errors) {
                throw $e;
            }

            return $text;
        } finally {
            if ($previous_encoding) {
                mb_internal_encoding($previous_encoding);
            }
        }
    }

    /**
     * Set internal encoding to UTF-8
     *
     * @return bool
     */
    protected function setInternalEncodingToUtf8(): bool
    {
        return mb_internal_encoding('UTF-8') !== false;
    }

    /**
     * Validate entity
     *
     * @param mixed $entity
     * @return bool
     */
    protected function isValidEntity(mixed $entity): bool
    {
        if (!is_array($entity)) {
            return false;
        }

        if (!isset($entity['offset']) || !isset($entity['length']) || !isset($entity['type'])) {
            return false;
        }

        if (!is_int($entity['offset']) || !is_int($entity['length']) || !is_string($entity['type'])) {
            return false;
        }

        if (!in_array($entity['type'], array_keys($this->tags_provider->getTags()), true)) {
            return false;
        }

        switch ($entity['type']) {
            case 'text_link':
                if (!isset($entity['url']) || !is_scalar($entity['url'])) {
                    return false;
                }
                break;

            case 'text_mention':
                if (!isset($entity['user']) || !isset($entity['user']['id']) || !is_scalar($entity['user']['id'])) {
                    return false;
                }
                break;

            case 'custom_emoji':
                if (!isset($entity['custom_emoji_id']) || !is_scalar($entity['custom_emoji_id'])) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Fix inaccuracies
     *
     * @param bool $fix_inaccuracies
     * @return static
     */
    public function fixInaccuracies(bool $fix_inaccuracies = true): static
    {
        $this->fix_inaccuracies = $fix_inaccuracies;

        return $this;
    }

    /**
     * Throw exception on error
     *
     * @param bool $throw_errors
     * @return static
     */
    public function throwErrors($throw_errors = true): static
    {
        $this->throw_errors = $throw_errors;

        return $this;
    }

    /**
     * Renders entities into html
     *
     * @param string $text
     * @return string
     */
    protected function process(string $text): string
    {
        $this->opened = [];
        $result = $point = '';
        $count = $last_increment = 0;
        $length = strlen($text);

        for ($i = 0; $i < $length; $i++) {
            $unit = substr($text, $i, 1);
            $byte = ord($unit);

            /**
             * @see https://core.telegram.org/api/entities#entity-length
             */
            // If the byte marks the beginning of a UTF-8 code unit (all bytes not starting with 0b10)
            if (($byte & 0xc0) != 0x80) {
                $result .= str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $point);

                // Byte is starting new UTF-8 code point. Check if an entity starts or ends at this count.
                foreach ($this->getEntitiesWhichEndAt($count) as $entity) {
                    $result .= $this->getEndTag($entity);
                }
                // Fix for entities that ends between two units
                if ($this->fix_inaccuracies && ($last_increment == 2)) {
                    if ($this->opened) {
                        foreach ($this->getEntitiesWhichEndAt($count - 1) as $entity) {
                            $result .= $this->getEndTag($entity);
                        }
                    }
                }
                foreach ($this->getEntitiesWhichStartAt($count) as $entity) {
                    $result .= $this->getStartTag($entity);
                }

                // If the byte marks the beginning of a 32-bit UTF-8 code unit (all bytes starting
                // with 0b11110) increment the count by 2, otherwise increment the count by 1
                $last_increment = ($byte >= 0xf0) ? 2 : 1;

                // Fix for entities that starts between two units
                if ($this->fix_inaccuracies && ($last_increment == 2)) {
                    foreach ($this->getEntitiesWhichStartAt($count + 1) as $entity) {
                        $result .= $this->getStartTag($entity);
                    }
                }

                $count += $last_increment;
                $point = '';
            }

            $point .= $unit;
        }

        $result .= $point;

        foreach ($this->getEntitiesWhichEndAt($count) as $entity) {
            $result .= $this->getEndTag($entity);
        }
        foreach ($this->opened as $entity) {
            $result .= $this->getEndTag($entity);
        }

        return $result;
    }

    /**
     * Get the entities that start at the given position
     *
     * @param int $position
     * @return array Entities
     */
    protected function getEntitiesWhichStartAt(int $position): array
    {
        return $this->start_positions[$position] ?? [];
    }

    /**
     * Get the entities that end at the given position
     *
     * @param int $position
     * @return array Entities
     */
    protected function getEntitiesWhichEndAt(int $position): array
    {
        return $this->end_positions[$position] ?? [];
    }

    /**
     * Get the start tag of the entity
     *
     * @param array $entity
     * @return string
     */
    protected function getStartTag(array $entity): string
    {
        $this->opened[] = $entity;

        $tag = $this->getTagsByEntityType($entity['type'])[0];

        return is_callable($tag) ? $tag($entity) : $tag;
    }

    /**
     * Get the end tag of the entity
     *
     * @param array $entity
     * @return string
     */
    protected function getEndTag(array $entity): string
    {
        array_pop($this->opened);

        $tag = $this->getTagsByEntityType($entity['type'])[1];

        return is_callable($tag) ? $tag($entity) : $tag;
    }

    /**
     * Get tags by entity type
     *
     * @param string $type
     * @return array
     */
    protected function getTagsByEntityType(string $type): array
    {
        return $this->tags_provider->getTags()[$type] ?? ['', ''];
    }
}
