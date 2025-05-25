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

namespace ASokol1981\Telegram\Entities\Html\Fixtures;

use ASokol1981\Telegram\Entities\Html\Renderer;

class RendererFixture extends Renderer
{
    private ?string $testing = null;

    public function setTesting(string $testing): static
    {
        $this->testing = $testing;

        return $this;
    }

    protected function setInternalEncodingToUtf8(): bool
    {
        if ($this->testing === 'mb_internal_encoding') {
            return false;
        }

        return parent::setInternalEncodingToUtf8();
    }

    protected function process(string $text): string
    {
        if ($this->testing === 'process') {
            throw new \Exception('test exception');
        }

        return parent::process($text);
    }
}
