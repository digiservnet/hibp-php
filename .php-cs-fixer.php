<?php

declare(strict_types=1);

/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.64.0|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => true,
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_between_import_groups' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_parentheses' => true,
        'explicit_string_variable' => true,
        'fully_qualified_strict_types' => true,
        'function_declaration' => ['closure_function_spacing' => 'one','closure_fn_spacing' => 'one'],
        'global_namespace_import' => ['import_classes' => true,'import_constants' => true,'import_functions' => true],
        'lambda_not_used_import' => true,
        'logical_operators' => true,
        'mb_str_functions' => true,
        'method_argument_space' => false,
        'method_chaining_indentation' => true,
        'modernize_strpos' => true,
        'new_with_parentheses' => ['anonymous_class' => false,'named_class' => true],
        'no_empty_comment' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['imports_order' => ['class','function','const'],'sort_algorithm' => 'length'],
        'simplified_if_return' => true,
        'single_line_after_imports' => true,
        'strict_comparison' => true,
        'ternary_to_null_coalescing' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arguments','arrays','match','parameters']],
        'trim_array_spaces' => true,
        'use_arrow_functions' => true,
        'void_return' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->in(__DIR__)
    // ->exclude([
    //     'folder-to-exclude',
    // ])
    // ->append([
    //     'file-to-include',
    // ])
    )->setCacheFile('.php-cs-fixer.cache');
