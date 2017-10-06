<?php
namespace AppBundle\Twig;

use AppBundle\Utils\Markdown;

/**
 * Class AppExtension
 * @package AppBundle\Twig
 */
class AppExtension extends \Twig_Extension
{
    private $parser;

    /**
     * AppExtension constructor.
     * @param Markdown $parser
     */
    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'md2html',
                array($this, 'markdownToHtml'),
                array('is_safe' => array('html'), 'pre_escape' => 'html')
            ),
        );
    }

    /**
     * @param $content
     * @return string
     */
    public function markdownToHtml($content)
    {
        return $this->parser->toHtml($content);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}