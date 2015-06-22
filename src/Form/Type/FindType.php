<?php

namespace Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Description of SearchForm
 *
 * @author recchia
 */
class FindType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('isbn', null, [
                    'label' => 'ISBN',
                    'constraints' => new Constraints\NotBlank(['message' => 'El campo ISBN es obligatorio'])
                    ])
                ->add('api', 'choice', [
                    'choices' => ['google_book' => 'Google Books API'],
                    'label' => 'Api'
                ])
                ->add('search', 'submit', ['label' => 'Buscar']);
    }

    public function getName()
    {
        return 'find_form';
    }
}
