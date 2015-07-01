<?php

namespace Model;

use Symfony\Component\Form\Form;

class Util
{
    public static function getFormErrorMessages(Form $form)
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors = $error->getMessage();
        }

        return $errors;
    }
}
