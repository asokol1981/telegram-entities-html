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

interface TagsProviderInterface
{
    /**
     * Get tags
     *
     * @return array[]
     *
     * @psalm-var array<string, array{0: string|\Closure, 1: string|\Closure}>
     * @phpstan-type TagWrapper array{0: string, 1: string}
     * @phpstan-type CallbackWrapper array{0: \Closure, 1: \Closure}
     * @phpstan-return array<string, TagWrapper|CallbackWrapper>
     */
    public function getTags(): array;
}
