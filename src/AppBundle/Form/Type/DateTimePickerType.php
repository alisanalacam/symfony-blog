<?php
namespace AppBundle\Form\Type;

use AppBundle\Utils\MomentFormatConverter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DateTimePickerType
 * @package AppBundle\Form\Type
 */
class DateTimePickerType extends AbstractType
{
    /**
     * @var MomentFormatConverter
     */
    private $formatConverter;
    public function __construct()
    {
        $this->formatConverter = new MomentFormatConverter();
    }
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-date-format'] = $this->formatConverter->convert($options['format']);;
        $view->vars['attr']['data-date-locale'] = \Locale::getDefault();
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\DateTimeType';
    }
}