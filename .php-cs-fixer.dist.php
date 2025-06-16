<?php

$finder = (new PhpCsFixer\Finder())
    ->in(['src'])
;

return (new PhpCsFixer\Config())
    ->setRules([
    ])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
;
