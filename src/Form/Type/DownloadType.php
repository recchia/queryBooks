<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/17/2015
 * Time: 12:28 PM
 */

namespace Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class DownloadType extends AbstractType
{

    protected $files;

    public function __construct($files)
    {
        $this->files=$files;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', 'choice', [
                'choices' => ['Files' => $this->files],
                'label' => 'Files',
            ])
            ->add('download', 'submit', ['label' => 'Descargar'])
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'download_form';
    }
}