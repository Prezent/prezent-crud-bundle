<?php

namespace Prezent\CrudBundle\Pagerfanta;

use Pagerfanta\View\Template\Template;
use Symfony\Component\Translation\TranslatorInterface;

class FoundationTemplate extends Template
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct();
        $this->translator = $translator;
    }

    protected static $defaultOptions = [
        'prev_message' => 'pager.previous',
        'next_message' => 'pager.next',
        'dots_message' => '',
        'active_suffix' => '',
        'css_container_class' => 'pagination',
        'css_prev_class' => 'pagination-previous',
        'css_next_class' => 'pagination-next',
        'css_disabled_class' => 'disabled',
        'css_dots_class' => 'ellipsis',
        'css_active_class' => 'current',
    ];

    public function container()
    {
        return sprintf(
            '<ul class="%s" role="navigation" aria-label="Pagination">%%pages%%</ul>',
            $this->option('css_container_class')
        );
    }

    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    public function pageWithText($page, $text)
    {
        $class = null;

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function pageWithTextAndClass($page, $text, $class)
    {
        $href = $this->generateRoute($page);

        return $this->linkLi($class, $href, $text);
    }

    public function previousDisabled()
    {
        $class = $this->previousDisabledClass();
        $text = $this->translator->trans($this->option('prev_message'));

        return $this->li($class, $text);
    }

    public function previousDisabledClass()
    {
        return $this->option('css_prev_class').' '.$this->option('css_disabled_class');
    }

    public function previousEnabled($page)
    {
        $text = $this->translator->trans($this->option('prev_message'));
        $class = $this->option('css_prev_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function nextDisabled()
    {
        $class = $this->nextDisabledClass();
        $text = $this->translator->trans($this->option('next_message'));

        return $this->li($class, $text);
    }

    public function nextDisabledClass()
    {
        return $this->option('css_next_class').' '.$this->option('css_disabled_class');
    }

    public function nextEnabled($page)
    {
        $text = $this->translator->trans($this->option('next_message'));
        $class = $this->option('css_next_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function first()
    {
        return $this->page(1);
    }

    public function last($page)
    {
        return $this->page($page);
    }

    public function current($page)
    {
        $text = trim($page.' '.$this->option('active_suffix'));
        $class = $this->option('css_active_class');

        return $this->li($class, $text);
    }

    public function separator()
    {
        $class = $this->option('css_dots_class');
        $text = $this->option('dots_message');

        return $this->linkLi($class, '', $text);
    }

    protected function li($class, $text)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<li%s>%s</li>', $liClass, $text);
    }

    protected function linkLi($class, $href, $text)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<li%s><a href="%s">%s</a></li>', $liClass, $href, $text);
    }

    protected function spanLi($class, $text)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<li%s><span>%s</span></li>', $liClass, $text);
    }
}
