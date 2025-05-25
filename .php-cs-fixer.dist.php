<?php declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        'header_comment' => ['header' => <<<'EOF'
            This file is part of Telegram Entities HTML.

            (c) Aleksei Sokolov <asokol.beststudio@gmail.com>

            This source file is subject to the MIT license that is bundled
            with this source code in the file LICENSE.
            EOF],
    ])
    ->setFinder($finder)
;
