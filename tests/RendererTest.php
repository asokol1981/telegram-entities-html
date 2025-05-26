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

use ASokol1981\Telegram\Entities\Html\Fixtures\RendererFixture;
use ASokol1981\Telegram\Entities\Html\Fixtures\TagsProviderFixture;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    public static function dataProvider(): array
    {
        return [
            'readme' => [
                'text' => '😎 Text message with bold and italic',
                'entities' => [
                    ['type' => 'bold', 'offset' => 21, 'length' => 4],
                    ['type' => 'italic', 'offset' => 30, 'length' => 6],
                ],
                'result' => '😎 Text message with <b>bold</b> and <i>italic</i>',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'smoke_test' => [
                'text' => 'ok',
                'entities' => [],
                'result' => 'ok',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'empty_text_and_entities' => [
                'text' => '',
                'entities' => [],
                'result' => '',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'empty_entities' => [
                'text' => 'test',
                'entities' => [],
                'result' => 'test',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'empty_text' => [
                'text' => '',
                'entities' => [['type' => 'bold', 'offset' => 0, 'length' => 4]],
                'result' => '',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'mb_internal_encoding_false_throw_errors_true' => [
                'text' => 'test',
                'entities' => [['type' => 'bold', 'offset' => 0, 'length' => 4]],
                'result' => 'test',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => true,
                    'expected_exception' => \Exception::class,
                    'testing' => 'mb_internal_encoding',
                ],
            ],
            'mb_internal_encoding_false_throw_errors_false' => [
                'text' => 'test',
                'entities' => [['type' => 'bold', 'offset' => 0, 'length' => 4]],
                'result' => 'test',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => 'mb_internal_encoding',
                ],
            ],
            'invalid_entities' => [
                'text' => '01234567',
                'entities' => [
                    'not array',
                    ['offset' => 0, 'length' => 1, 'type' => 'bold'],
                    ['offset' => 1, 'length' => 1],
                    ['offset' => 2, 'length' => 1, 'type' => 1],
                    ['offset' => 3, 'length' => 1, 'type' => 'invalid'],
                    ['offset' => 4, 'length' => 1, 'type' => 'text_link'], // no url
                    ['offset' => 5, 'length' => 1, 'type' => 'text_mention'], // no user
                    ['offset' => 6, 'length' => 1, 'type' => 'text_mention', 'user' => []], // no user.id
                    ['offset' => 7, 'length' => 1, 'type' => 'custom_emoji'], // no custom_emoji_id
                ],
                'result' => '<b>0</b>1234567',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'empty_start_positions' => [
                'text' => '0',
                'entities' => [
                    'not array',
                    ['offset' => 1, 'length' => 1],
                ],
                'result' => '0',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'valid_entities' => [
                'text' => '0123456789abc',
                'entities' => [
                    ['offset' => 0, 'length' => 1, 'type' => 'bold'],
                    ['offset' => 1, 'length' => 1, 'type' => 'italic'],
                    ['offset' => 2, 'length' => 1, 'type' => 'underline'],
                    ['offset' => 3, 'length' => 1, 'type' => 'strikethrough'],
                    ['offset' => 4, 'length' => 1, 'type' => 'spoiler'],
                    ['offset' => 5, 'length' => 1, 'type' => 'blockquote'],
                    ['offset' => 6, 'length' => 1, 'type' => 'expandable_blockquote'],
                    ['offset' => 7, 'length' => 1, 'type' => 'code'],
                    ['offset' => 8, 'length' => 1, 'type' => 'pre'],
                    ['offset' => 9, 'length' => 1, 'type' => 'pre', 'language' => 'php'],
                    ['offset' => 10, 'length' => 1, 'type' => 'text_link', 'url' => 'https://example.com'],
                    ['offset' => 11, 'length' => 1, 'type' => 'text_mention', 'user' => ['id' => 1]],
                    ['offset' => 12, 'length' => 1, 'type' => 'custom_emoji', 'custom_emoji_id' => '2'],
                ],
                'result' => implode('', [
                    '<b>0</b>',
                    '<i>1</i>',
                    '<u>2</u>',
                    '<s>3</s>',
                    '<tg-spoiler>4</tg-spoiler>',
                    '<blockquote>5</blockquote>',
                    '<blockquote class="expandable">6</blockquote>',
                    '<code>7</code>',
                    '<pre>8</pre>',
                    '<pre><code class="language-php">9</code></pre>',
                    '<a href="https://example.com">a</a>',
                    '<a href="tg://user?id=1">b</a>',
                    '<tg-emoji emoji-id="2">c</tg-emoji>',
                ]),
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'fix_inaccuracies_false_offset_0' => [
                'text' => '😎emoji',
                'entities' => [['type' => 'bold', 'offset' => 0, 'length' => 1]],
                'result' => '<b>😎emoji</b>',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'fix_inaccuracies_false_offset_1' => [
                'text' => '😎emoji',
                'entities' => [['type' => 'bold', 'offset' => 1, 'length' => 1]],
                'result' => '😎</b>emoji',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'fix_inaccuracies_true_offset_0' => [
                'text' => '😎emoji',
                'entities' => [['type' => 'bold', 'offset' => 0, 'length' => 1]],
                'result' => '<b>😎</b>emoji',
                'options' => [
                    'fix_inaccuracies' => true,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'fix_inaccuracies_true_offset_1' => [
                'text' => '😎emoji',
                'entities' => [['type' => 'bold', 'offset' => 1, 'length' => 1]],
                'result' => '<b>😎</b>emoji',
                'options' => [
                    'fix_inaccuracies' => true,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => null,
                ],
            ],
            'process_exception_throw_errors_true' => [
                'text' => 'test',
                'entities' => [['type' => 'bold', 'offset' => 0, 'length' => 4]],
                'result' => 'test',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => true,
                    'expected_exception' => \Exception::class,
                    'testing' => 'process',
                ],
            ],
            'process_exception_throw_errors_false' => [
                'text' => 'test',
                'entities' => [['type' => 'bold', 'offset' => 0, 'length' => 4]],
                'result' => 'test',
                'options' => [
                    'fix_inaccuracies' => false,
                    'throw_errors' => false,
                    'expected_exception' => null,
                    'testing' => 'process',
                ],
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function test(string $text, array $entities, string $result, array $options): void
    {
        $converter = new RendererFixture();
        $converter->fixInaccuracies($options['fix_inaccuracies']);
        $converter->throwErrors($options['throw_errors']);
        if (!is_null($options['expected_exception'])) {
            $this->expectException($options['expected_exception']);
        }
        if (!is_null($options['testing'])) {
            $converter->setTesting($options['testing']);
        }

        $this->assertSame($result, $converter->render($text, $entities));
    }

    public function test_tags_provider(): void
    {
        $converter = new RendererFixture(new TagsProviderFixture());

        $this->assertSame('<strong>test</strong>', $converter->render('test', [['type' => 'bold', 'offset' => 0, 'length' => 4]]));
    }
}
