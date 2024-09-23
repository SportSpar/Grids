<?php

$rules = [
    '@PSR2'                                 => true,
    '@Symfony'                              => true,

    // Change Symfony parameters
    'binary_operator_spaces'                => false,
    'no_superfluous_phpdoc_tags'            => false,
    'concat_space'                          => ['spacing' => 'one'],
    'no_trailing_comma_in_singleline_array' => true,
    'trailing_comma_in_multiline'           => false,
    'yoda_style'                            => false,
    'phpdoc_summary'                        => false,
    'increment_style'                       => false,
    'cast_spaces'                           => false,
    'single_line_throw'                     => false,

    // Additional rules
    'align_multiline_comment'               => true,
    'linebreak_after_opening_tag'           => true,
    'no_useless_else'                       => true,
    'no_useless_return'                     => true,
    'ternary_to_null_coalescing'            => true,
    'global_namespace_import'               => true
];

return (new PhpCsFixer\Config())->setRules($rules);
