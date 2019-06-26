<?php

namespace DarkCat\TwigTranslationBundle\Twig\Node\Expression;

use Twig\Compiler;
use Twig\Node\Node;
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
        if(!$params){
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

        if(isset($params[1])){
            $compiler->raw(sprintf('strtr(\'%s\', ', $message));
            $compiler->subcompile($params[1]);
            $compiler->raw(')');
        }else{
            $compiler->string($message);
        }
    }

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
    }
}
