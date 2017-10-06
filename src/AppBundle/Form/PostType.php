<?php
namespace AppBundle\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PostType
 * @package AppBundle\Form
 */
class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'attr' => array('autofocus' => true),
                'label' => 'Başlık',
            ))
            ->add('body', null, array(
                'label' => 'İçerik',
                'attr' => array('rows' => 18)
            ))
            ->add('author', EntityType::class, array(
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC');
                },
                'choice_label' => 'username',
            ))
            ->add('publishedAt', 'AppBundle\Form\Type\DateTimePickerType', array(
                'label' => 'Yayın Tarihi',
                'format' => 'dd.MM.yyyy HH:mm',
                'attr' => array('data-toggle' => 'datetimepicker')
            ))
        ;
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Blog\Post',
        ));
    }
}