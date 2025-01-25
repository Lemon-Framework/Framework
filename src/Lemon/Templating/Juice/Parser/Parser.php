<?php

declare(strict_types=1);

namespace Lemon\Templating\Juice\Parser;

use Lemon\Contracts\Templating\Juice\Lexer;
use Lemon\Templating\Exceptions\CompilerException;
use Lemon\Templating\Juice\Context;
use Lemon\Templating\Juice\HtmlNodes;
use Lemon\Templating\Juice\Nodes\Html\Attribute;
use Lemon\Templating\Juice\Nodes\Html\Comment;
use Lemon\Templating\Juice\Nodes\Html\Node as HtmlNode;
use Lemon\Templating\Juice\Nodes\Html\StringLiteral;
use Lemon\Templating\Juice\Nodes\Html\Text;
use Lemon\Templating\Juice\Nodes\NodeList;
use Lemon\Templating\Juice\Position;
use Lemon\Templating\Juice\Token\HtmlTokenKind;

class Parser
{
    public function __construct( 
        public readonly Lexer $lexer,
        public readonly HtmlNodes $nodes,
    ) {
    }

    public function line(): int 
    {
        return $this->position()->line;
    }

    public function pos(): int
    {
        return $this->position()->pos;
    }

    public function position(): Position
    {
        return $this->lexer->current()->position;
    }

    public function parse(?callable $end = null, ?callable $error = null): NodeList
    {
        $list = new NodeList();
        $this->lexer->changeContext(Context::Html);
        $didnt_end = true;
        while ($this->lexer->peek() && ($didnt_end = !$end || !$end())) { // todo get rid of spaces grrrrr 
            $this->lexer->next();
            $list->add(
                $this->parseHtmlTag()
                ?? $this->parseText()
            );
        }

        if ($end && $didnt_end) {
            $error();
        }

        return $list;    
    }

    public function parseHtmlTag(): ?HtmlNode
    {
        if ($this->lexer->current()?->kind != HtmlTokenKind::TagOpen) {
            return null;
        }

        $line = $this->line();
        $pos = $this->pos();
        $position = $this->position();
        $this->lexer->changeContext(Context::HtmlTag);

        if ($this->lexer->next()?->kind !== HtmlTokenKind::Name) {
            throw new CompilerException("Unexpected token after <, expected tag name!", $this->line(), $this->pos()); // @hint If you want to write < symbol, use "&lt;" 
        }

        $name = $this->lexer->current()->content;

        $attributes = new NodeList();
        while ($attribute = $this->parseAttribute()) {
            $attributes->add($attribute);
        }

        if ($this->lexer->current()->kind !== HtmlTokenKind::TagClose) {
            throw new CompilerException("Unexpected token, expected either attribute or >!", $this->line(), $this->pos());
        }

        if ($this->nodes->isSingleton($name)) {
            return new HtmlNode($name, $position, $attributes);
        }

        $this->lexer->changeContext(Context::Html);

        $body = $this->parse(
            fn() => $this->parseClosingHtmlTag($name), 
            fn() => throw new CompilerException("Unlosed tag {$name}", $line, $pos)
        );

        return new HtmlNode($name, $position, $attributes, $body);
    }

    public function parseAttribute(): ?Attribute 
    {
        if ($this->lexer->next()->kind !== HtmlTokenKind::Name) {
            return null;
        }

        $name = $this->lexer->current()->content;
        $pos = $this->position();

        if ($this->lexer->peek()->kind !== HtmlTokenKind::Equals) {
            return new Attribute($name, $pos);
        }

        $this->lexer->next();
        $this->lexer->next();
        $content = $this->parseString();
        if ($content === null)  {
            throw new CompilerException('Unexpected token after =', $this->line(), $this->pos());
        }
        
        return new Attribute($name, $pos, $content);
    }

    public function parseString(): ?NodeList
    {
        if ($this->lexer->current()->kind !== HtmlTokenKind::StringDelim) {
            return null;
        }

        $start = $this->lexer->current();

        $result = new NodeList(); 
        $this->lexer->changeContext(Context::HtmlString);
        while (($current = $this->lexer->next())?->content !== $start->content) {
            if ($current === null) {
                throw new CompilerException('Unclosed string!', $start->line, $start->pos);
            }
            // todo ast wit positions
            $result->add(match($current->kind) {
                HtmlTokenKind::EscapedStringDelim => new StringLiteral($current->content, $this->position()),
                HtmlTokenKind::StringContent => new StringLiteral($current->content, $this->position()),
                HtmlTokenKind::StringDelim => new StringLiteral($current->content, $this->position()),
                // todo juice 
                // if this happens we're cooked
                default => throw new CompilerException('Internal error within compiler, open issue please', $start->line, $start->pos),
            });
        }
        $this->lexer->changeContext(Context::HtmlTag);

        return $result;
    }

    public function parseText(): Text
    {
        return new Text($this->lexer->current()->content, $this->position());
    }

    public function parseClosingHtmlTag(string $name): bool
    {
        if ($this->lexer->peek()?->kind !== HtmlTokenKind::EndTagOpen) {
            return false;
        }
        $this->lexer->next();
        $this->lexer->changeContext(Context::HtmlTag);
        if ($this->lexer->next()?->kind !== HtmlTokenKind::Name) {
            throw new CompilerException('Unexpected token!', $this->line(), $this->pos());
        }

        if ($this->lexer->current()->content !== $name) {
            throw new CompilerException('Unclosed tag '.$name, $this->line(), $this->pos());
        }

        if ($this->lexer->next()?->kind !== HtmlTokenKind::TagClose) {
            throw new CompilerException('Unexpected token!', $this->line(), $this->pos());
        }

        $this->lexer->changeContext(Context::Html);

        return true;
    }

    public function parseHtmlComment(): ?Comment
    {
        if ($this->lexer->current()?->kind !== HtmlTokenKind::CommentOpen) {
            return null;
        }

        $result = '';
        while ($this->lexer->next()?->kind !== HtmlTokenKind::CommentClose) {
            $result .= $this->lexer->current()->content;
        }

        return new Comment($result, $this->position());
    }
}
