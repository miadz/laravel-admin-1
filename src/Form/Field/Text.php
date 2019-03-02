<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Text extends Field
{
    use PlainInput;

    /**
     * Render this filed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->addElementClass($this->direction);
        $this->initPlainInput();

        $this->prepend('<i class="fa fa-pencil fa-fw"></i>')
            ->defaultAttribute('type', 'text')
//            ->defaultAttribute('style', "direction: $this->direction !important;")
            ->defaultAttribute('id', $this->id)
            ->defaultAttribute('name', $this->elementName ?: $this->formatName($this->column))
            ->defaultAttribute('value', old($this->column, $this->value()))
            ->defaultAttribute('class', 'form-control '.$this->getElementClassString())
            ->defaultAttribute('placeholder', $this->getPlaceholder());

        $this->addVariables([
            'prepend' => $this->prepend,
            'append'  => $this->append,
        ]);

        return parent::render();
    }
}
