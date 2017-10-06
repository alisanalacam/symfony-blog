<?php
namespace AppBundle\Utils;

class Markdown
{
    /**
     * @var \Parsedown
     */
    private $parser;
    /**
     * @var \HTMLPurifier
     */
    private $purifier;
    public function __construct()
    {
        $this->parser = new \Parsedown();
        $this->purifier = new \HTMLPurifier();
    }
    /**
     * @param string $text
     *
     * @return string
     */
    public function toHtml($text)
    {
        $html = $this->parser->text($text);
        $safeHtml = $this->purifier->purify($html);
        return $safeHtml;
    }
}