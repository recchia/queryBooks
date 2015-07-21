<?php

namespace Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Description of SearchForm.
 *
 * @author recchia
 */
class FindType extends AbstractType
{
    protected $apis;

    public function __construct (array $apis)
    {
        $this->apis = $apis;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('isbn', null, [
                    'label' => 'ISBN',
                    'constraints' => new Constraints\NotBlank(['message' => 'El campo ISBN es obligatorio']),
                    ])
                ->add('api', 'choice', [
                    'choices' => ['APis' => $this->apis],
                    'label' => 'Api',
                ])
                ->add('search', 'submit', ['label' => 'Buscar']);
    }

    public function getName()
    {
        return 'find_form';
    }
}
