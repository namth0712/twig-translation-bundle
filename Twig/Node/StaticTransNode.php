<?php

namespace DarkCat\TwigTranslationBundle\Twig\Node;

use Twig\Compiler;
use Symfony\Bridge\Twig\Node\TransNode;
use Twig\Node\Expression\ArrayExpression;

class StaticTransNode extends TransNode
{
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $defaults = new ArrayExpression([], -1);
        if ($this->hasNode('vars') && ($vars = $this->getNode('vars')) instanceof ArrayExpression) {
            $defaults = $this->getNode('vars');
            $vars = null;
        }
        list($msg, $defaults) = $this->compileString($this->getNode('body'), $defaults, (bool) $vars);

        $args = [];
        $valuePairs = $defaults->getKeyValuePairs();
        if ($valuePairs) {
            foreach ($valuePairs as $pair) {
                $args[$pair['key']->getAttribute('value')] = $pair['value']->getAttribute('value');
            }
        }

        $domain = 'messages';
        if ($this->hasNode('domain')) {
            $domain = $this->getNode('domain')->getAttribute('value');
        }

        $locale = null;
        if ($this->hasNode('locale')) {
            $locale = $this->getNode('locale')->getAttribute('value');
        }

        $count = null;
        if ($this->hasNode('count')) {
            $count = $this->getNode('count')->getAttribute('value');
        }

        $env = $compiler->getEnvironment();
        $message = $env
                    ->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')
                    ->trans($msg->getAttribute('value'), $args, $domain, $locale, $count);

        $compiler
            ->write('echo ');
        if ($defaults) {
            $compiler->raw(sprintf('strtr(\'%s\', ', $message));
            if (null !== $vars) {
                $compiler
                    ->raw('array_merge(')
                    ->subcompile($defaults)
                    ->raw(', ')
                    ->subcompile($this->getNode('vars'))
                    ->raw(')');
            } else {
                $compiler->subcompile($defaults);
            }
            $compiler->raw(')');
        } else {
            $compiler->string($message);
        }
        $compiler
            ->raw(";\n");
    }//end compile()
}//end class
