<?php

namespace DarkCat\TwigTranslationBundle\Twig\Node\Expression;

use Twig\Compiler;
use Twig\Node\Expression\FilterExpression;

class TransFilterExpression extends FilterExpression
{
    public function compile(Compiler $compiler)
    {
        if (!$this->hasNode('node')) {
            return;
        }

        $msg = $this->getNode('node');
        $env = $compiler->getEnvironment();

        $arguments = [];
        if ($this->hasNode('arguments')) {
            $name = $this->getNode('filter')->getAttribute('value');
            $filter = $compiler->getEnvironment()->getFilter($name);

            $this->setAttribute('name', $name);
            $this->setAttribute('type', 'filter');
            $this->setAttribute('needs_environment', $filter->needsEnvironment());
            $this->setAttribute('needs_context', $filter->needsContext());
            $this->setAttribute('arguments', $filter->getArguments());
            $this->setAttribute('callable', $filter->getCallable());
            $this->setAttribute('is_variadic', $filter->isVariadic());

            $this->setAttribute(
                'callable',
                function (array $arg = []) {
                }
            );
            $callable = $this->getAttribute('callable');
            $arguments = $this->getArguments($callable, $this->getNode('arguments'));
        }

        $domain = 'messages';
        if (isset($arguments[1])) {
            $domain = $arguments[1]->getAttribute('value');
        }

        $locale = null;
        if (isset($arguments[2])) {
            $locale = $arguments[2]->getAttribute('value');
        }

        $count = null;
        if (isset($arguments[3])) {
            $count = $arguments[3]->getAttribute('value');
        }

        $message = $env
                    ->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')
                    ->trans($msg->getAttribute('value'), [], $domain, $locale, $count);


        if (isset($arguments[0])) {
            $compiler->raw(sprintf('strtr(\'%s\', ', $message));
            $compiler->subcompile($arguments[0]);
            $compiler->raw(')');
        } else {
            $compiler->string($message);
        }
    }//end compile()
}//end class
