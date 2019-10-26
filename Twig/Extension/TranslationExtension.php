<?php

namespace DarkCat\TwigTranslationBundle\Twig\Extension;

use DarkCat\TwigTranslationBundle\Twig\Node\Expression\TransFilterExpression;
use DarkCat\TwigTranslationBundle\Twig\Node\Expression\TransFunctionExpression;
use DarkCat\TwigTranslationBundle\Twig\TokenParser\StaticTransTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Provides integration of the Translation component with Twig.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final since Symfony 4.2
 */
class TranslationExtension extends AbstractExtension
{
    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return AbstractTokenParser[]
     */
    public function getTokenParsers()
    {
        return [
            new StaticTransTokenParser(),
        ];
    }//end getTokenParsers()

    public function getFilters()
    {
        return [
            new TwigFilter(
                't',
                'statictrans',
                [
                    'is_safe' => ['all'],
                    'is_safe_callback' => ['all'],
                    'node_class' => TransFilterExpression::class,
                ]
            ),

            new TwigFilter(
                'statictrans',
                'statictrans',
                [
                    'is_safe' => ['all'],
                    'is_safe_callback' => ['all'],
                    'node_class' => TransFilterExpression::class,
                ]
            ),
        ];
    }//end getFilters()

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                't',
                'statictrans',
                [
                    'needs_environment' => true,
                    'is_safe' => ['all'],
                    'is_safe_callback' => ['all'],
                    'node_class' => TransFunctionExpression::class,
                ]
            ),
            new TwigFunction(
                'statictrans',
                'statictrans',
                [
                    'needs_environment' => true,
                    'is_safe' => ['all'],
                    'is_safe_callback' => ['all'],
                    'node_class' => TransFunctionExpression::class,
                ]
            ),
        ];
    }//end getFunctions()
}//end class
