<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use SquidIT\PhpCodingStandards\PhpCsFixer\Rules;

$finder = Finder::create()
    ->in(__DIR__);

$phpFixer = new Config();

return $phpFixer
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setRules(Rules::getRules([
        'single_line_empty_body' => true,
        'binary_operator_spaces' => [
            'default'   => 'align_single_space_minimal',
            'operators' => [
                '===' => 'single_space',
                '??'  => 'single_space',
            ],
        ],
    ]));
