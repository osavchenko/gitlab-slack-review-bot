<?php

use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_align' => ['align' => 'vertical'],
        'global_namespace_import' => ['import_classes' => true],
        'phpdoc_separation' => ['groups' => [['ORM\*'], ['Assert\*']]],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'yoda_style' => false,
        'doctrine_annotation_braces' => ['syntax' => 'with_braces'],
        'increment_style' => ['style' => 'post'],

    ])
    ->setFinder($finder);
