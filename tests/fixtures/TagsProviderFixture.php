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

use ASokol1981\Telegram\Entities\Html\TagsProviderInterface;

class TagsProviderFixture implements TagsProviderInterface
{
    public function getTags(): array
    {
        return [
            'bold' => ['<strong>', '</strong>'],
        ];
    }
}
