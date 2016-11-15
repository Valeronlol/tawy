<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class adminAdd extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'Заголовок'))
            ->add('description', TextType::class, array('label' => 'Описание'))
            ->add('content', 'textarea', array( 'label' => 'Содержание записи', 'attr' => array('cols' => '120', 'rows' => '30' )))
            ->add('imageFile', FileType::class, array('label' => 'Миниатюра'))
            ->add('save', SubmitType::class, array('label' => 'Отправить'))
            ->getForm();
    }
}