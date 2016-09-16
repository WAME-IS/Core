<?php

namespace Wame\Core\Models;

use Nette;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Forms\Controls;


class MaterialDesignRenderer extends DefaultFormRenderer
{
    public $wrappers = array(
        'form' => array(
            'container' => null,
        ),
        'error' => array(
            'container' => 'div class="alert alert-danger"',
            'item' => 'p',
        ),
        'group' => array(
            'container' => 'div',
            'label' => 'legend',
            'description' => 'p',
        ),
        'controls' => array(
            'container' => null,
        ),
        'pair' => array(
            'container' => 'div class="input-field"',
            '.required' => 'required',
            '.optional' => null,
            '.odd' => null,
            '.error' => 'validate invalid',
        ),
        'control' => array(
            'container' => '',
            '.odd' => null,
            'description' => 'span class=help-block',
            'requiredsuffix' => '',
            'errorcontainer' => 'span class=help-block',
            'erroritem' => '',
            '.required' => 'required',
            '.text' => 'text',
            '.password' => 'text',
            '.file' => 'text',
            '.submit' => 'button',
            '.image' => 'imagebutton',
            '.button' => 'button',
        ),
        'label' => array(
            'container' => '',
            'suffix' => null,
            'requiredsuffix' => '',
        ),
        'hidden' => array(
            'container' => 'div',
        ),
    );

    /**
     * Provides complete form rendering.
     * @param  Nette\Forms\Form
     * @param  string 'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
     * @return string
     */
    public function render(Nette\Forms\Form $form, $mode = null)
    {
        $form->getElementPrototype()->setNovalidate('novalidate');

        $usedPrimary = FALSE;
        
        foreach ($form->getControls() as $control) {
            if ($control instanceof Controls\Button) {
                if (strpos($control->getControlPrototype()->getClass(), 'btn') === FALSE) {
                    $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn' : 'btn-flat');
                    $usedPrimary = true;
                }
            } 
        // Select    
            elseif ($control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
//                $control->getControlPrototype()->addClass('form-control');
            } 
        // Textarea
            elseif ($control instanceof Controls\TextArea) {
                $control->getControlPrototype()->addClass('materialize-textarea');
            } 
        // Checkbox
            elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList) {
                $control->getSeparatorPrototype()->setName('div')->addClass('filled-in');
            }
        // Radio
            elseif ($control instanceof Controls\RadioList) {
                $control->getSeparatorPrototype()->setName('div')->addClass('with-gap');
            }
        }

        return parent::render($form, $mode);
    }
}
