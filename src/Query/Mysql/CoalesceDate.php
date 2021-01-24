<?php

namespace DoctrineExtensions\Query\Mysql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @author Aurélien Tournayre <aurelien.tournayre@gmail.com>
 */
class CoalesceDate extends FunctionNode
{
    public $coalesceExpressions = [];

    public function getSql(SqlWalker $sqlWalker)
    {
        $expressions = [];
        foreach ($this->coalesceExpressions as $expression) {
            $expressions[] = $sqlWalker->walkArithmeticPrimary($expression);
        }
        return sprintf('COALESCE(%s)', implode(', ', $expressions));
    }

    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->coalesceExpressions[] = $parser->ArithmeticExpression();

        while ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $peek = $lexer->glimpse();
            $this->coalesceExpressions[] = $peek['value'] == '('
                ? $parser->Subselect()
                : $parser->ArithmeticExpression();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
