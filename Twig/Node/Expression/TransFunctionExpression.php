<?php

namespace DarkCat\TwigTranslationBundle\Twig\Node\Expression;

use Twig\Compiler;
use Twig\Node\Expression\FunctionExpression;

class TransFunctionExpression extends FunctionExpression
{
    public function compile(Compiler $compiler)
    {
        if (!$this->hasNode('arguments')) {
            return;
        }
        $arguments = $this->getNode('arguments');
        if (!$arguments) {
            return;
        }
        $params = $this->getParams($arguments);
        if (!$params) {
            return;
        }

        $msg = $params[0];
        $env = $compiler->getEnvironment();

        $domain = 'messages';
        if (isset($params[2])) {
            $domain = $params[2]->getAttribute('value');
        }

        $locale = null;
        if (isset($params[3])) {
            $locale = $params[3]->getAttribute('value');
        }

        $count = null;
        if (isset($params[4])) {
            $count = $params[4]->getAttribute('value');
        }

        $message = $env
                    ->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')
                    ->trans($msg->getAttribute('value'), [], $domain, $locale, $count);

        if (isset($params[1])) {
            $name = $this->getAttribute('name');
            $function = $compiler->getEnvironment()->getFunction($name);

            $this->setAttribute('name', $name);
            $this->setAttribute('type', 'function');
            $this->setAttribute('needs_environment', $function->needsEnvironment());
            $this->setAttribute('needs_context', $function->needsContext());
            $this->setAttribute('arguments', $function->getArguments());
            $callable = $function->getCallable();
            if ('constant' === $name && $this->getAttribute('is_defined_test')) {
                $callable = 'twig_constant_is_defined';
            }
            $this->setAttribute('callable', $callable);
            $this->setAttribute('is_variadic', $function->isVariadic());

            $compiler->raw(sprintf('strtr(\'%s\', ', $message));
            $this->setAttribute(
                'callable',
                function (array $arg = []) {
                }
            );
            $callable = $this->getAttribute('callable');
            $arguments = $this->getArguments($callable, $this->getNode('arguments'));
            if (isset($arguments[1])) {
                $compiler->subcompile($arguments[1]);
            }
            $compiler->raw(')');
        } else {
            $compiler->string($message);
        }//end if
    }//end compile()

    protected function getParams($arguments)
    {
        $params = [];
        $named = false;
        foreach ($arguments as $name => $node) {
            if (!\is_int($name)) {
                $named = true;
                $name = $this->normalizeName($name);
            } elseif ($named) {
                throw new SyntaxError(sprintf('Positional arguments cannot be used after named arguments for %s "%s".', $callType, $callName), $this->getTemplateLine(), null, null, false);
            }
            $params[$name] = $node;
        }

        return $params;
    }//end getParams()
}//end class
