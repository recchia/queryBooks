<?php

namespace Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Description of UploadType.
 *
 * @author recchia
 */
class UploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('file', 'file', [
                    'label' => 'Archivo',
                    'constraints' => new Constraints\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/octet-stream',
                            ],
                    ]),
                ])
                ->add('upload', 'submit', ['label' => 'Subir'])
        ;
    }

    public function getName()
    {
        return 'upload_form';
    }
}
