<?php

/**
 * Created by PhpStorm.
 * User: Santiago
 * Date: 7/28/2015
 * Time: 10:28 AM
 */

namespace Form\EventListener;

use Model\Constants;
use Model\DBConnection;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddDocumentFieldSuscriber implements EventSubscriberInterface
{

    private $app;

    private $factory;

    public function __construct(FormFactoryInterface $factory, $app)
    {
        $this->factory = $factory;
        $this->app = $app;
    }


    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND => 'preBind'
        );
    }

    private function addDocumentForm($form, $document)
    {
        $database = new DBConnection($this->app);
        if (is_string($document))
        {
            $qb = $document;
        }
        elseif (is_numeric($document))
        {

            $qb = $database->findDocumentNameById($document);
        }
        else
        {
            $qb = $database->findAllDocuments();
        }

        $form->add($this->factory->createNamed('files', 'choice', null, array(
            'empty_value' => 'Empty',
            'auto_initialize' => false,
            'choices' => ['Files' => $qb]
        )));
        /*$form->add($this->factory->createNamed('files', 'choice', null, [
            'choices' => ['Files' => $document],
            'label' => 'Files',
        ]));*/
    }

    public function preSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();
        if (null === $data) {
            return;
        }
        $document = array_key_exists('files', $data) ? $data['files'] : null;
        $this->addDocumentForm($form, $document);
    }

    public function preBind(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();
        if (null === $data) {
            return;
        }
        $document = array_key_exists('files', $data) ? $data['files'] : null;

        $this->addDocumentForm($form, $document);
    }
}