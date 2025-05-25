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

class TagsProvider implements TagsProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getTags(): array
    {
        return [
            'bold' => ['<b>', '</b>'],
            'italic' => ['<i>', '</i>'],
            'underline' => ['<u>', '</u>'],
            'strikethrough' => ['<s>', '</s>'],
            'spoiler' => ['<tg-spoiler>', '</tg-spoiler>'],
            'blockquote' => ['<blockquote>', '</blockquote>'],
            'expandable_blockquote' => ['<blockquote class="expandable">', '</blockquote>'],
            'code' => ['<code>', '</code>'],
            'pre' => [
                function (array $entity): string {
                    if (isset($entity['language'])) {
                        return sprintf('<pre><code class="language-%s">', $entity['language']);
                    }
                    return '<pre>';
                },
                function (array $entity): string {
                    if (isset($entity['language'])) {
                        return '</code></pre>';
                    }
                    return '</pre>';
                },
            ],
            'text_link' => [
                function (array $entity): string {
                    return sprintf('<a href="%s">', $entity['url']);
                },
                '</a>',
            ],
            'text_mention' => [
                function (array $entity): string {
                    return sprintf('<a href="tg://user?id=%s">', $entity['user']['id']);
                },
                '</a>',
            ],
            'custom_emoji' => [
                function (array $entity): string {
                    return sprintf('<tg-emoji emoji-id="%s">', $entity['custom_emoji_id']);
                },
                '</tg-emoji>',
            ],
        ];
    }
}
