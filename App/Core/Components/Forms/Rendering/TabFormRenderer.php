<?php

namespace Core\Components\Forms\Rendering;

use Nette;
use Nette\Utils\Html;
use Nette\Forms\Rendering\DefaultFormRenderer;

/**
 * Converts a Form into the HTML output.
 */
//TODO clean method that are identical to DefaultFormRenderer
class TabFormRenderer extends DefaultFormRenderer
{
	use Nette\SmartObject;

	/**
	 *  /--- form.container
	 *
	 *    /--- error.container
	 *      .... error.item [.class]
	 *    \---
	 *
	 *    /--- hidden.container
	 *      .... HIDDEN CONTROLS
	 *    \---
	 *
	 *    /--- group.container
	 *      .... group.label
	 *      .... group.description
	 *
	 *      /--- controls.container
	 *
	 *        /--- pair.container [.required .optional .odd]
	 *
	 *          /--- label.container
	 *            .... LABEL
	 *            .... label.suffix
	 *            .... label.requiredsuffix
	 *          \---
	 *
	 *          /--- control.container [.odd]
	 *            .... CONTROL [.required .text .password .file .submit .button]
	 *            .... control.requiredsuffix
	 *            .... control.description
	 *            .... control.errorcontainer + control.erroritem
	 *          \---
	 *        \---
	 *      \---
	 *    \---
	 *  \--
	 *
	 * @var array of HTML tags */
	public $wrappers = [
		'form' => [
			'container' => NULL,
			'.container' => 'form-horizontal tab-wrapper'
		],

		'error' => [
			'container' => 'ul class=error',
			'item' => 'li',
		],

		'group' => [
			'container' => 'fieldset',
			'.container' => 'tab-content',
			'label' => 'label',
			'.label' => 'tab-label',
			'description' => 'p',
		],

		'controls' => [
			'container' => 'div',
		],

		'pair' => [
			'container' => 'div class=form-group',
			'.required' => 'required',
			'.optional' => NULL,
			'.odd' => NULL,
			'.error' => NULL,
		],

		'control' => [
            'container' => 'div class=col-sm-9',
			'.odd' => NULL,

			'description' => 'small',
			'requiredsuffix' => '',
			'errorcontainer' => 'span class=error',
			'erroritem' => '',

			'.required' => 'required',
			'.text' => 'form-control',
			'.textarea' => 'form-control',
			'.password' => 'text',
			'.file' => 'text',
			'.email' => 'text',
			'.number' => 'text',
			'.submit' => 'button',
			'.image' => 'imagebutton',
			'.button' => 'button',
		],

		'label' => [
            'container' => 'div class="col-sm-3 control-label"',
			'suffix' => NULL,
			'requiredsuffix' => '',
		],

		'hidden' => [
			'container' => 'div',
		],
	];

	/** @var Nette\Forms\Form */
	protected $form;

	/** @var int */
	protected $counter;


	/**
	 * Provides complete form rendering.
	 * @param  Nette\Forms\Form
	 * @param  string 'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
	 * @return string
	 */
	public function render(Nette\Forms\Form $form, $mode = NULL)
	{
        $form->getElementPrototype()->appendAttribute('class', $this->getValue('form .container'));
        //add form-control wrapper
        foreach ($form->getControls() as $control) {
            if ($control instanceof Controls\Button) {
                if (strpos($control->getControlPrototype()->getClass(), 'btn') === FALSE) {
                    $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
                    $usedPrimary = true;
                }
            } elseif ($control instanceof Controls\TextBase ||
                $control instanceof Controls\SelectBox ||
                $control instanceof Controls\TextArea ||
                $control instanceof Controls\MultiSelectBox) {
                $control->getControlPrototype()->addClass('form-control');
            }
        }
        return parent::render($form, $mode);

	}


	/**
	 * Renders form body.
	 * @return string
	 */
	public function renderBody()
	{
		$s = $remains = '';

		$defaultContainer = $this->getWrapper('group container');
		$translator = $this->form->getTranslator();

        $checkedRadio = false;

		foreach ($this->form->getGroups() as $group) {
			if (!$group->getControls() || !$group->getOption('visual')) {
				continue;
			}
			$container = $group->getOption('container', $defaultContainer);
			$container = $container instanceof Html ? clone $container : Html::el($container);
            $container->class = $this->getValue('group .container');

            $lid = 'tab-radio-'.$group->getOption('label');  //locale_id
            $radioControl = Html::el('input')->addAttributes([
               'id' => $lid,
               'type' => 'radio',
               'name' => 'tab-radio-control',
                'class' => 'tab-radio'
            ]);
            if (!$checkedRadio) {
                $checkedRadio = true;
                $radioControl->setAttribute('checked', 'checked');
            }
            $s .= $radioControl;

            //set label
			$text = $group->getOption('label');
			if ($text instanceof Html) {
				$s .= $this->getWrapper('group label')->addHtml($text)
                    ->addAttributes([
                        'class' => $this->getValue('group .label'),
                        'for' => $lid
                        ]);

			} elseif (is_string($text)) {
				if ($translator !== NULL) {
					$text = $translator->translate($text);
				}
				$s .= "\n" . $this->getWrapper('group label')->setText($text)->addAttributes([
                        'class' => $this->getValue('group .label'),
                        'for' => $lid
                        ]) . "\n";
			}

			$id = $group->getOption('id');
			if ($id) {
				$container->id = $id;
			}
            //start fieldset
			$s .= "\n" . $container->startTag();

			$text = $group->getOption('description');

			if ($text instanceof Html) {
				$s .= $text;

			} elseif (is_string($text)) {
				if ($translator !== NULL) {
					$text = $translator->translate($text);
				}
				$s .= $this->getWrapper('group description')->setText($text) . "\n";
			}

			$s .= $this->renderControls($group);
            //end fieldset
			$remains = $container->endTag() . "\n" . $remains;
			if (!$group->getOption('embedNext')) {
				$s .= $remains;
				$remains = '';
			}
		}

        //set wrapper class to form itself
		$s .= $remains . $this->renderControls($this->form);

		$container = $this->getWrapper('form container');
		$container->setHtml($s);
		return $container->render(0);
	}


	/**
	 * Renders group of controls.
	 * @param  Nette\Forms\Container|Nette\Forms\ControlGroup
	 * @return string
	 */
	public function renderControls($parent)
	{
		if (!($parent instanceof Nette\Forms\Container || $parent instanceof Nette\Forms\ControlGroup)) {
			throw new Nette\InvalidArgumentException('Argument must be Nette\Forms\Container or Nette\Forms\ControlGroup instance.');
		}

		$container = $this->getWrapper('controls container');

		$buttons = NULL;
		foreach ($parent->getControls() as $control) {
			if ($control->getOption('rendered') || $control->getOption('type') === 'hidden' || $control->getForm(FALSE) !== $this->form) {
				// skip

			} elseif ($control->getOption('type') === 'button') {
				$buttons[] = $control;

			} else {
				if ($buttons) {
					$container->addHtml($this->renderPairMulti($buttons));
					$buttons = NULL;
				}
				$container->addHtml($this->renderPair($control));
			}
		}

		if ($buttons) {
			$container->addHtml($this->renderPairMulti($buttons));
		}

		$s = '';
		if (count($container)) {
			$s .= "\n" . $container . "\n";
		}

		return $s;
	}


	/**
	 * Renders single visual row.
	 * @return string
	 */
	public function renderPair(Nette\Forms\IControl $control)
	{
		$pair = $this->getWrapper('pair container');
		$pair->addHtml($this->renderLabel($control));
		$pair->addHtml($this->renderControl($control));
		$pair->class($this->getValue($control->isRequired() ? 'pair .required' : 'pair .optional'), TRUE);
		$pair->class($control->hasErrors() ? $this->getValue('pair .error') : NULL, TRUE);
		$pair->class($control->getOption('class'), TRUE);
		if (++$this->counter % 2) {
			$pair->class($this->getValue('pair .odd'), TRUE);
		}
		$pair->id = $control->getOption('id');
		return $pair->render(0);
	}


	/**
	 * Renders single visual row of multiple controls.
	 * @param  Nette\Forms\IControl[]
	 * @return string
	 */
	public function renderPairMulti(array $controls)
	{
		$s = [];
		foreach ($controls as $control) {
			if (!$control instanceof Nette\Forms\IControl) {
				throw new Nette\InvalidArgumentException('Argument must be array of Nette\Forms\IControl instances.');
			}
			$description = $control->getOption('description');
			if ($description instanceof Html) {
				$description = ' ' . $control->getOption('description');

			} elseif (is_string($description)) {
				if ($control instanceof Nette\Forms\Controls\BaseControl) {
					$description = $control->translate($description);
				}
				$description = ' ' . $this->getWrapper('control description')->setText($description);

			} else {
				$description = '';
			}

			$control->setOption('rendered', TRUE);
			$el = $control->getControl();
			if ($el instanceof Html && $el->getName() === 'input') {
				$el->class($this->getValue("control .$el->type"), TRUE);
			}
			$s[] = $el . $description;
		}
		$pair = $this->getWrapper('pair container');
		$pair->addHtml($this->renderLabel($control));
		$pair->addHtml($this->getWrapper('control container')->setHtml(implode(' ', $s)));
		return $pair->render(0);
	}


	/**
	 * Renders 'label' part of visual row of controls.
	 * @return string
	 */
	public function renderLabel(Nette\Forms\IControl $control)
	{
		$suffix = $this->getValue('label suffix') . ($control->isRequired() ? $this->getValue('label requiredsuffix') : '');
		$label = $control->getLabel();
		if ($label instanceof Html) {
			$label->addHtml($suffix);
			if ($control->isRequired()) {
				$label->class($this->getValue('control .required'), TRUE);
			}
		} elseif ($label != NULL) { // @intentionally ==
			$label .= $suffix;
		}
		return $this->getWrapper('label container')->setHtml($label);
	}


	/**
	 * Renders 'control' part of visual row of controls.
	 * @return string
	 */
	public function renderControl(Nette\Forms\IControl $control)
	{
		$body = $this->getWrapper('control container');
		if ($this->counter % 2) {
			$body->class($this->getValue('control .odd'), TRUE);
		}

		$description = $control->getOption('description');
		if ($description instanceof Html) {
			$description = ' ' . $description;

		} elseif (is_string($description)) {
			if ($control instanceof Nette\Forms\Controls\BaseControl) {
				$description = $control->translate($description);
			}
			$description = ' ' . $this->getWrapper('control description')->setText($description);

		} else {
			$description = '';
		}

		if ($control->isRequired()) {
			$description = $this->getValue('control requiredsuffix') . $description;
		}

		$control->setOption('rendered', TRUE);
		$el = $control->getControl();
		if ($el instanceof Html && ($el->getName() === 'input' || $el->getName() === 'textarea')) {
			$el->class($this->getValue("control .".($el->type ?? $el->getName())), TRUE);
		}
		return $body->setHtml($el . $description . $this->renderErrors($control));
	}


	/**
	 * @param  string
	 * @return Html
	 */
	protected function getWrapper($name)
	{
		$data = $this->getValue($name);
		return $data instanceof Html ? clone $data : Html::el($data);
	}


	/**
	 * @param  string
	 * @return string
	 */
	protected function getValue($name)
	{
		$name = explode(' ', $name);
		$data = & $this->wrappers[$name[0]][$name[1]];
		return $data;
	}

}
