<?php

namespace Encore\Admin\Form;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Field.
 */
class Field implements Renderable
{
    use Macroable;

    const FILE_DELETE_FLAG = '_file_del_';

    /**
     * Element id.
     *
     * @var array|string
     */
    protected $id;

    /**
     * Element value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Field original value.
     *
     * @var mixed
     */
    protected $original;

    /**
     * Field default value.
     *
     * @var mixed
     */
    protected $default;

    /**
     * Element label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Column name.
     *
     * @var string|array
     */
    protected $column = '';

    /**
     * Form element name.
     *
     * @var string
     */
    protected $elementName = [];

    /**
     * Form element classes.
     *
     * @var array
     */
    protected $elementClass = [];

    /**
     * Variables of elements.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Options for specify elements.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Checked for specify elements.
     *
     * @var array
     */
    protected $checked = [];

    /**
     * Validation rules.
     *
     * @var string|\Closure
     */
    protected $rules = '';

    /**
     * @var callable
     */
    protected $validator;

    /**
     * Validation messages.
     *
     * @var array
     */
    public $validationMessages = [];

    /**
     * Css required by this field.
     *
     * @var array
     */
    protected static $css = [];

    /**
     * Js required by this field.
     *
     * @var array
     */
    protected static $js = [];

    /**
     * Script for field.
     *
     * @var string
     */
    protected $script = '';

    /**
     * Element attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Parent form.
     *
     * @var Form
     */
    protected $form = null;

    /**
     * View for field to render.
     *
     * @var string
     */
    protected $view = '';

    /**
     * Help block.
     *
     * @var array
     */
    protected $help = [];

    protected $custom_data = [];

    /**
     * Key for errors.
     *
     * @var mixed
     */
    protected $errorKey;

    /**
     * Placeholder for this field.
     *
     * @var string|array
     */
    protected $placeholder;

    /**
     * Width for label and field.
     *
     * @var array
     */
    protected $width = [
        'label'             => 2,
        'field'             => 8,
        'form_can_setwidth' => true,
    ];

    /**
     * viewClass for field.
     *
     * @var array
     */
    protected $viewClass = [
        'label'      => "",
        'field'      => "",
        'form-group' => "",
    ];

    /**
     * If the form horizontal layout.
     *
     * @var bool
     */
    protected $horizontal = true;

    /**
     * todo patch
     * default local use for translate field label
     * or used in jquery plugin as local or language.
     *
     * @var bool
     */
    protected $local = "en";

    /**
     * ltr or rtl
     *
     * @var string
     */
    protected $direction = "ltr";

    /*
     * column data format.
     *
     * @var \Closure
     */
    protected $customFormat = null;

    /**
     * @var bool
     */
    protected $display = true;

    /**
     * @var array
     */
    protected $labelClass = [];


    /**
     * Field constructor.
     *
     * @param       $column
     * @param array $arguments
     */
    public function __construct($column, $arguments = [])
    {
        $this->column = $column;
        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($column);
        $this->local = config('app.locale');
    }

    /**
     * Get assets required by this field.
     *
     * @return array
     */
    public static function getAssets()
    {
        return [
            'css' => static::$css,
            'js'  => static::$js,
        ];
    }

    /**
     * Format the field element id.
     *
     * @param string|array $column
     *
     * @return string|array
     */
    protected function formatId($column)
    {
        return str_replace('.', '_', $column);
    }

    /**
     * Format the label value.
     *
     * @param array $arguments
     *
     * @return string
     */
    protected function formatLabel($arguments = [])
    {
        $column = is_array($this->column) ? current($this->column) : $this->column;

        $trans_key = 'validation.attributes.' . strtolower($column);
        if (isset($arguments[0])) {
            $label = $arguments[0];
        } else if (Lang::has($trans_key)) {
            $label = Lang::get($trans_key);
        } else {
            $label = ucfirst($column);
        }

        return str_replace([
            '.',
            '_'
        ], ' ', $label);
    }

    /**
     * Format the name of the field.
     *
     * @param string $column
     *
     * @return array|mixed|string
     */
    protected function formatName($column)
    {
        if (is_string($column)) {
            $name = explode('.', $column);

            if (count($name) == 1) {
                return $name[0];
            }

            $html = array_shift($name);
            foreach ($name as $piece) {
                $html .= "[$piece]";
            }

            return $html;
        }

        if (is_array($this->column)) {
            $names = [];
            foreach ($this->column as $key => $name) {
                $names[$key] = $this->formatName($name);
            }

            return $names;
        }

        return '';
    }

    /**
     * Set form element name.
     *
     * @param string $name
     *
     * @return $this
     *
     * @author Edwin Hui
     */
    public function setElementName($name)
    {
        $this->elementName = $name;

        return $this;
    }

    /**
     * Fill data to the field.
     *
     * @param array $data
     *
     * @return void
     */
    public function fill($data)
    {
        // Field value is already setted.
        //        if (!is_null($this->value)) {
        //            return;
        //        }

        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $this->value[$key] = array_get($data, $column);
            }

            return;
        }

        $this->value = array_get($data, $this->column);
        if (isset($this->customFormat) && $this->customFormat instanceof \Closure) {
            $this->value = call_user_func($this->customFormat, $this->value);
        }
    }

    /**
     * custom format form column data when edit.
     *
     * @param \Closure $call
     *
     * @return $this
     */
    public function customFormat(\Closure $call)
    {
        $this->customFormat = $call;

        return $this;
    }

    /**
     * Set original value to the field.
     *
     * @param array $data
     *
     * @return void
     */
    public function setOriginal($data)
    {
        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $this->original[$key] = array_get($data, $column);
            }

            return;
        }

        $this->original = array_get($data, $this->column);
    }

    /**
     * @param Form $form
     *
     * @return $this
     */
    public function setForm(Form $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Set width for field and label.
     * by default $this->width['form_can_setwidth'] is true,but with $form_can_setwidth can disable it and set false
     *
     * @param int  $field
     * @param int  $label
     * @param bool $form_can_setwidth ,
     *
     * @return $this
     */
    public function setWidth($field = 8, $label = 2, $form_can_setwidth = false)
    {

        $this->width = [
            'label'             => $label,
            'field'             => $field,
            'form_can_setwidth' => $form_can_setwidth,
        ];

        return $this;
    }

    /**
     * Set the field options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Set the field option checked.
     *
     * @param array $checked
     *
     * @return $this
     */
    public function checked($checked = [])
    {
        if ($checked instanceof Arrayable) {
            $checked = $checked->toArray();
        }

        $this->checked = array_merge($this->checked, $checked);

        return $this;
    }

    /**
     * Get or set rules.
     *
     * @param null  $rules
     * @param array $messages
     *
     * @return $this
     */
    public function rules($rules = null, $messages = [])
    {
        if ($rules instanceof \Closure) {
            $this->rules = $rules;
        }

        if (is_array($rules)) {
            $thisRuleArr = array_filter(explode('|', $this->rules));

            $this->rules = array_merge($thisRuleArr, $rules);
        } elseif (is_string($rules)) {
            $rules = array_filter(explode('|', "{$this->rules}|$rules"));

            $this->rules = implode('|', $rules);
        }

        $this->validationMessages = $messages;

        return $this;
    }

    /**
     * Get field validation rules.
     *
     * @return string
     */
    public function getRules()
    {
        if ($this->rules instanceof \Closure) {
            return $this->rules->call($this, $this->form);
        }

        return $this->rules;
    }

    /**
     * Remove a specific rule by keyword.
     *
     * @param string $rule
     *
     * @return void
     */
    protected function removeRule($rule)
    {
        if (!is_string($this->rules)) {
            return;
        }

        $pattern = "/{$rule}[^\|]?(\||$)/";
        $this->rules = preg_replace($pattern, '', $this->rules, -1);
    }

    /**
     * Set field validator.
     *
     * @param callable $validator
     *
     * @return $this
     */
    public function validator(callable $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get key for error message.
     *
     * @return string
     */
    public function getErrorKey()
    {
        return $this->errorKey ?: $this->column;
    }

    /**
     * Set key for error message.
     *
     * @param string $key
     *
     * @return $this
     */
    public function setErrorKey($key)
    {
        $this->errorKey = $key;

        return $this;
    }

    /**
     * Set or get value of the field.
     *
     * @param null $value
     *
     * @return mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return is_null($this->value) ? $this->getDefault() : $this->value;
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Set default value for field.
     *
     * @param $default
     *
     * @return $this
     */
    public function default($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get default value.
     *
     * @return mixed
     */
    public function getDefault()
    {
        if ($this->default instanceof \Closure) {
            return call_user_func($this->default, $this->form);
        }

        return $this->default;
    }

    /**
     * Set help block for current field.
     *
     * @param string $text
     * @param string $icon
     *
     * @return $this
     */
    public function help($text = '', $icon = 'fa-info-circle')
    {
        $this->help = compact('text', 'icon');

        return $this;
    }

    public function custom_data($custom_data)
    {
        $this->custom_data = $custom_data;

        return $this;
    }

    /**
     * Get column of the field.
     *
     * @return string|array
     */
    public function column()
    {
        return $this->column;
    }

    /**
     * Get label of the field.
     *
     * @return string
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * Get original value of the field.
     *
     * @return mixed
     */
    public function original()
    {
        return $this->original;
    }

    /**
     * Get validator for this field.
     *
     * @param array $input
     *
     * @return bool|Validator
     */
    public function getValidator(array $input)
    {
        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        $rules = $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        if (is_string($this->column)) {
            if (!array_has($input, $this->column)) {
                return false;
            }

            $input = $this->sanitizeInput($input, $this->column);

            $rules[$this->column] = $fieldRules;
            $attributes[$this->column] = $this->label;
        }

        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                if (!array_key_exists($column, $input)) {
                    continue;
                }
                $input[$column . $key] = array_get($input, $column);
                $rules[$column . $key] = $fieldRules;
                $attributes[$column . $key] = $this->label . "[$column]";
            }
        }

        return Validator::make($input, $rules, $this->validationMessages, $attributes);
    }

    /**
     * Sanitize input data.
     *
     * @param array  $input
     * @param string $column
     *
     * @return array
     */
    protected function sanitizeInput($input, $column)
    {
        if ($this instanceof Field\MultipleSelect) {
            $value = array_get($input, $column);
            array_set($input, $column, array_filter($value));
        }

        return $input;
    }

    /**
     * Add html attributes to elements.
     *
     * @param array|string $attribute
     * @param mixed        $value
     *
     * @return $this
     */
    public function attribute($attribute, $value = null)
    {
        if (is_array($attribute)) {
            $this->attributes = array_merge($this->attributes, $attribute);
        } else {
            $this->attributes[$attribute] = (string)$value;
        }

        return $this;
    }

    /**
     * Specifies a regular expression against which to validate the value of the input.
     *
     * @param string $regexp
     *
     * @return Field
     */
    public function pattern($regexp)
    {
        return $this->attribute('pattern', $regexp);
    }

    /**
     * set the input filed required.
     *
     * @param bool $isLabelAsterisked
     *
     * @return Field
     */
    public function required($isLabelAsterisked = true)
    {
        if ($isLabelAsterisked) {
            $this->setLabelClass(['asterisk']);
        }

        return $this->attribute('required', true);
    }

    /**
     * Set the field automatically get focus.
     *
     * @return Field
     */
    public function autofocus()
    {
        return $this->attribute('autofocus', true);
    }

    /**
     * Set the field as readonly mode.
     *
     * @return Field
     */
    public function readOnly()
    {
        return $this->attribute('readonly', true);
    }

    /**
     * Set field as disabled.
     *
     * @return Field
     */
    public function disable()
    {
        return $this->attribute('disabled', true);
    }

    /**
     * Set field placeholder.
     *
     * @param string $placeholder
     *
     * @return Field
     */
    public function placeholder($placeholder = '')
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Get placeholder.
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder ?: trans('admin.input') . ' ' . $this->label;
    }

    /**
     * Prepare for a field value before update or insert.
     *
     * @param $value
     *
     * @return mixed
     */
    public function prepare($value)
    {
        return $value;
    }

    /**
     * Format the field attributes.
     *
     * @return string
     */
    protected function formatAttributes()
    {
        $html = [];

        foreach ($this->attributes as $name => $value) {
            $html[] = $name . '="' . e($value) . '"';
        }

        return implode(' ', $html);
    }

    /**
     * @return $this
     */
    public function disableHorizontal()
    {
        $this->horizontal = false;

        return $this;
    }

    /**
     * @return array
     */
    public function getViewElementClasses()
    {
        if ($this->horizontal) {
            $this->viewClass['label'] = "col-sm-{$this->width['label']} {$this->getLabelClass()} " . $this->viewClass['label'];
            $this->viewClass['field'] = "col-sm-{$this->width['field']} " . $this->viewClass['field'];
            $this->viewClass['form-group'] = "form-group " . $this->viewClass['form-group'];

            return $this->viewClass;
        }

        $this->viewClass['label'] = "{$this->getLabelClass()} " . $this->viewClass['label'];

        return $this->viewClass;
    }

    /**
     *  add Form Group Element Classes
     */
    public function addFormGroupClasses($class)
    {
        $this->viewClass['form-group'] = $this->viewClass['form-group'] . " $class ";

        return $this;
    }

    /**
     *  set View Element Classes
     * @param string $label
     * @param string $field
     * @param string $form_group
     */
    public function setViewElementClasses($label = '', $field = '', $form_group = '')
    {
        $this->viewClass['label'] = $label;
        $this->viewClass['field'] = $field;
        $this->viewClass['form-group'] = $form_group;
    }

    /**
     *  add View Element Classes
     * @param string $label
     * @param string $field
     * @param string $form_group
     */
    public function addViewElementClasses($label = '', $field = '', $form_group = '')
    {
        $this->viewClass['label'] = "col-sm-{$this->width['label']} {$this->getLabelClass()} " . $label;
        $this->viewClass['field'] = "col-sm-{$this->width['field']} " . $field;
        $this->viewClass['form-group'] = "form-group " . $form_group;
    }

    /**
     * Set form element class.
     *
     * @param string|array $class
     *
     * @return $this
     */
    public function setElementClass($class)
    {
        $this->elementClass = (array)$class;

        return $this;
    }

    /**
     * Get element class.
     *
     * @return array
     */
    protected function getElementClass()
    {
        //        if (!$this->elementClass) {
        $name = $this->elementName ?: $this->formatName($this->column);
        $name = (array)str_replace([
            '[',
            ']'
        ], '_', $name);
        $this->addElementClass($name);

        //        }

        return $this->elementClass;
    }

    /**
     * Get element class string.
     *
     * @return mixed
     */
    protected function getElementClassString()
    {
        $elementClass = $this->getElementClass();

        if (Arr::isAssoc($elementClass)) {
            $classes = [];

            foreach ($elementClass as $index => $class) {
                $classes[$index] = is_array($class) ? implode(' ', $class) : $class;
            }

            return $classes;
        }

        return implode(' ', $elementClass);
    }

    /**
     * Get element class selector.
     *
     * @return string|array
     */
    protected function getElementClassSelector()
    {
        $elementClass = $this->getElementClass();

        if (Arr::isAssoc($elementClass)) {
            $classes = [];

            foreach ($elementClass as $index => $class) {
                $classes[$index] = '.' . (is_array($class) ? implode('.', $class) : $class);
            }

            return $classes;
        }

        return '.' . implode('.', $elementClass);
    }

    /**
     * Add the element class.
     *
     * @param $class
     *
     * @return $this
     */
    public function addElementClass($class)
    {
        if (is_array($class) || is_string($class)) {
            $this->elementClass = array_merge($this->elementClass, (array)$class);

            $this->elementClass = array_unique($this->elementClass);
        }

        return $this;
    }

    /**
     * Remove element class.
     *
     * @param $class
     *
     * @return $this
     */
    public function removeElementClass($class)
    {
        $delClass = [];

        if (is_string($class) || is_array($class)) {
            $delClass = (array)$class;
        }

        foreach ($delClass as $del) {
            if (($key = array_search($del, $this->elementClass))) {
                unset($this->elementClass[$key]);
            }
        }

        return $this;
    }

    /**
     * Add variables to field view.
     *
     * @param array $variables
     *
     * @return $this
     */
    protected function addVariables(array $variables = [])
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelClass()
    : string
    {
        return implode(' ', $this->labelClass);
    }

    /**
     * @param array $labelClass
     *
     * @return self
     */
    public function setLabelClass(array $labelClass)
    : self
    {
        $this->labelClass = $labelClass;

        return $this;
    }

    /**
     * Get the view variables of this field.
     *
     * @return array
     */
    public function variables()
    {
        return array_merge($this->variables, [
            'id'          => $this->id,
            'name'        => $this->elementName ?: $this->formatName($this->column),
            'help'        => $this->help,
            'class'       => $this->getElementClassString(),
            'value'       => $this->value(),
            'label'       => $this->label,
            'viewClass'   => $this->getViewElementClasses(),
            'column'      => $this->column,
            'errorKey'    => $this->getErrorKey(),
            'attributes'  => $this->formatAttributes(),
            'placeholder' => $this->getPlaceholder(),
            'custom_data' => $this->custom_data,

        ]);
    }

    /**
     * popover data-popover
     * @param null $title
     * @param      $content
     * @return $this
     */
    public function popover($title = null, $content)
    {
         $this->attribute([
            'data-popover-title' => $title,
            'data-popover'       => $content,
        ]);

        return $this;
    }

    /**
     * Get view of this field.
     *
     * @return string
     */
    public function getView()
    {
        if (!empty($this->view)) {
            return $this->view;
        }

        $class = explode('\\', get_called_class());

        return 'admin::form.' . strtolower(end($class));
    }

    /**
     * Get script of current field.
     *
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /** todo pull
     * Set direction setting.
     * @param string $dir ltr or rtl
     * @return $this
     */
    public function direction($dir = 'ltr')
    {
        $this->direction = $dir;

        return $this;
    }

    /**
     * set local
     * @param string $local
     * @return $this
     */
    public function setLocal($local = 'en')
    {
        $this->local = $local;

        return $this;
    }

    /**
     * @return array
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get form id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /*
     * If this field should render.
     *
     * @return bool
     */
    protected function shouldRender()
    {
        if (!$this->display) {
            return false;
        }

        return true;

    }

    /**
     * Render this filed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        if (!$this->shouldRender()) {
            return '';
        }

        Admin::script($this->script);

        return view($this->getView(), $this->variables());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render()->render();
    }
}
