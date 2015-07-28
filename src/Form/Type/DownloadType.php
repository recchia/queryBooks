<?php
/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/17/2015
 * Time: 12:28 PM
 */

namespace Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Validator\Constraints\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints;
use Form\EventListener\AddDocumentFieldSuscriber;

class DownloadType extends AbstractType
{

    protected $files;

    protected $app;

    public function __construct($files, $app)
    {
        $this->files=$files;
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $documentSuscriber = new AddDocumentFieldSuscriber($factory, $this->app);
        $builder->addEventSubscriber($documentSuscriber);

        /*$builder
            ->add('download', 'submit', ['label' => 'Descargar'])
        ;*/
        $builder
            ->add('files', 'choice', [
                'choices' => ['Files' => $this->files],
                'label' => 'Files',
            ])
            ->add('download', 'submit', ['label' => 'Descargar'])
        ;
        /*$builder
            ->addEventListener(FormEvents::PRE_SET_DATA,
                function(FormEvent $event){
                    $form = $event->getForm();
                    $data =  $event->getData();
                    $form
                    ->add('files', 'choice', [
                        'choices' => ['Files' => $data],
                        'label' => 'Files',
                    ]);
                });*/
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