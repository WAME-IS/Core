<?php

namespace Wame\Core\Models;

use Nette;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Forms\Controls;


class MaterialDesignRenderer_1 extends DefaultFormRenderer
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
            'container' => 'fieldset',
            'label' => 'legend',
            'description' => 'p',
        ),
        'controls' => array(
            'container' => null,
        ),
        'pair' => array(
            'container' => 'div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label"',
            '.required' => 'required',
            '.optional' => null,
            '.odd' => null,
            '.error' => 'is-invalid',
        ),
        'control' => array(
            'container' => '',
            '.odd' => null,
            'description' => 'span class=help-block',
            'requiredsuffix' => '',
            'errorcontainer' => 'span class="mdl-textfield__error"',
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
            'container' => 'label class="mdl-textfield__label"',
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
        // Buttons
            if ($control instanceof Controls\Button) {
                if (strpos($control->getControlPrototype()->getClass(), 'mdl-button') === FALSE) {
                    $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent' : 'mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect');
                    $usedPrimary = true;
                }
            }
        // Text
            elseif ($control instanceof Controls\TextBase) {
                $control->getControlPrototype()->addClass('mdl-textfield__input');
            } 
        // Select
            elseif ($control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
                $control->getControlPrototype()->addClass('mdl-textfield__input is-focused is-upgraded is-dirty');
            } 
        // Checkbox
            elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList) {
                $control->getSeparatorPrototype()->setName('label')->addClass('mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect');
                $control->getControlPrototype()->addClass('mdl-checkbox__input');
            } 
        // Radio
            elseif ($control instanceof Controls\RadioList) {
                $control->getSeparatorPrototype()->setName('label')->addClass('mdl-radio mdl-js-radio mdl-js-ripple-effect');
                $control->getControlPrototype()->addClass('mdl-radio__button');
            }
        }

        return parent::render($form, $mode);
    }
}
