<?php

namespace DarkCat\TwigTranslationBundle\Twig\TokenParser;

use DarkCat\TwigTranslationBundle\Twig\Node\StaticTransNode;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;
use Twig\Node\TextNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Token Parser for the 'statictrans' tag.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class StaticTransTokenParser extends AbstractTokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @return Node
     *
     * @throws SyntaxError
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $count = null;
        $vars = new ArrayExpression([], $lineno);
        $domain = null;
        $locale = null;
        if (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->test('count')) {
                // {% statictrans count 5 %}
                $stream->next();
                $count = $this->parser->getExpressionParser()->parseExpression();
            }

            if ($stream->test('with')) {
                // {% statictrans with vars %}
                $stream->next();
                $vars = $this->parser->getExpressionParser()->parseExpression();
            }

            if ($stream->test('from')) {
                // {% statictrans from "messages" %}
                $stream->next();
                $domain = $this->parser->getExpressionParser()->parseExpression();
            }

            if ($stream->test('into')) {
                // {% statictrans into "fr" %}
                $stream->next();
                $locale = $this->parser->getExpressionParser()->parseExpression();
            } elseif (!$stream->test(Token::BLOCK_END_TYPE)) {
                throw new SyntaxError('Unexpected token. Twig was looking for the "with", "from", or "into" keyword.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
            }
        }//end if

        // {% statictrans %}message{% endstatictrans %}
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decidestatictransFork'], true);

        if (!$body instanceof TextNode && !$body instanceof AbstractExpression) {
            throw new SyntaxError('A message inside a statictrans tag must be a simple text.', $body->getTemplateLine(), $stream->getSourceContext());
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new StaticTransNode($body, $domain, $count, $vars, $locale, $lineno, $this->getTag());
    }//end parse()

    public function decidestatictransFork($token)
    {
        return $token->test(['endstatictrans']);
    }//end decidestatictransFork()

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'statictrans';
    }//end getTag()
}//end class
