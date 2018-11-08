<?php

namespace Encore\Admin\Widgets;

use Encore\Admin\Form\Field;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use phpDocumentor\Reflection\Types\This;

/**
 * Class Form.
 *
 * @method Field\Text           text($name, $label = '')
 * @method Field\Password       password($name, $label = '')
 * @method Field\Checkbox       checkbox($name, $label = '')
 * @method Field\Radio          radio($name, $label = '')
 * @method Field\Select         select($name, $label = '')
 * @method Field\MultipleSelect multipleSelect($name, $label = '')
 * @method Field\Textarea       textarea($name, $label = '')
 * @method Field\Hidden         hidden($name, $label = '')
 * @method Field\Id             id($name, $label = '')
 * @method Field\Ip             ip($name, $label = '')
 * @method Field\Url            url($name, $label = '')
 * @method Field\Color          color($name, $label = '')
 * @method Field\Email          email($name, $label = '')
 * @method Field\Mobile         mobile($name, $label = '')
 * @method Field\Slider         slider($name, $label = '')
 * @method Field\Map            map($latitude, $longitude, $label = '')
 * @method Field\Editor         editor($name, $label = '')
 * @method Field\File           file($name, $label = '')
 * @method Field\Image          image($name, $label = '')
 * @method Field\MultipleImage  multipleImage($column, $label = '')
 * @method Field\MultipleFile   multipleFile($column, $label = '')
 * @method Field\Date           date($name, $label = '')
 * @method Field\Datetime       datetime($name, $label = '')
 * @method Field\Time           time($name, $label = '')
 * @method Field\DateRange      dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange  dateTimeRange($start, $end, $label = '')
 * @method Field\TimeRange      timeRange($start, $end, $label = '')
 * @method Field\Number         number($name, $label = '')
 * @method Field\Currency       currency($name, $label = '')
 * @method Field\SwitchField    switch ($name, $label = '')
 * @method Field\Display        display($name, $label = '')
 * @method Field\Rate           rate($name, $label = '')
 * @method Field\Divide         divide()
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($html)
 * @method Field\Tags           tags($column, $label = '')
 * @method Field\Icon           icon($column, $label = '')
 * @method \App\Admin\Extensions\Form\Script script($script, $arguments)
 * @method \App\Admin\Extensions\Form\GeoCompleteMap  geocompletemap($latitude, $longitude, $label = '')
 * @method \App\Admin\Extensions\Form\multiSelectTag  multiselect_tags($column, $label = '')
 * @method \App\Admin\Extensions\Form\InstagramAddSelect2  instagram_add_select2($column, $label = '', $api_url, $type, $modal_get_url)
 * @method \App\Admin\Extensions\Form\Cropper  cropper($column, $label = '')
 * @method \App\Admin\Extensions\Form\PersianDate  pdate($column, $label = '')
 * @method \App\Admin\Extensions\Form\TimePicker  timepicker($column, $label = '')
 * @method \App\Admin\Extensions\Form\PHPEditor  phpeditor($column, $label = '')
 * @method \App\Admin\Extensions\Form\Json  json($column, $label = '')
 * @method \App\Admin\Extensions\Form\CkEditor  ckeditor($column, $label = '')
 * @method \App\Admin\Extensions\Form\MultipleFileConverter  multiple_file_converter($column, $label = '')
 * @method \App\Admin\Extensions\Form\displayimage  displayimage($column, $label = '')
 */
class Form implements Renderable
{

    public static $VALID = "valid";

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $attributes = [];

    protected $disable_footer = true;

    /**
     * Available buttons.
     *
     * @var array
     */
    protected $buttons = ['reset', 'submit'];

    /**
     * Form constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->initData($data);
        $this->initFormAttributes();
    }

    public function initData($data)
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (!empty($data)) {
            $this->data = $data;
        }
    }

    /**
     * Initialize the form attributes.
     */
    protected function initFormAttributes()
    {
        $this->attributes = [
            'method'         => 'POST',
            'action'         => '',
            'class'          => 'form-horizontal',
            'accept-charset' => 'UTF-8',
            'pjax-container' => true,
            'fieldWidth'     => 8,
            'labelWidth'     => 2,
        ];
    }

    /**
     * if $disable_footer = true , then footer showed ,else hide footer of form and not rendering
     * @param bool $disable_footer
     */
    public function disableFooter($disable_footer = true)
    {
        $this->disable_footer = !$disable_footer;
    }

    /**
     * Action uri of the form.
     *
     * @param string $action
     *
     * @return $this
     */
    public function action($action)
    {
        return $this->attribute('action', $action);
    }

    /**
     * Method of the form.
     *
     * @param string $method
     *
     * @return $this
     */
    public function method($method = 'POST')
    {
        return $this->attribute('method', strtoupper($method));
    }

    /**
     * Add form attributes.
     *
     * @param string|array $attr
     * @param string       $value
     *
     * @return $this
     */
    public function attribute($attr, $value = '')
    {
        if (is_array($attr)) {
            foreach ($attr as $key => $value) {
                $this->attribute($key, $value);
            }
        } else {
            $this->attributes[$attr] = $value;
        }

        return $this;
    }

    /**
     * Disable Pjax.
     *
     * @return $this
     */
    public function disablePjax()
    {
        array_forget($this->attributes, 'pjax-container');

        return $this;
    }

    /**
     * Disable reset button.
     *
     * @return $this
     */
    public function disableReset()
    {
        array_delete($this->buttons, 'reset');

        return $this;
    }

    /**
     * Disable submit button.
     *
     * @return $this
     */
    public function disableSubmit()
    {
        array_delete($this->buttons, 'submit');

        return $this;
    }

    /**
     * Set field and label width in current form.
     *
     * @param int $fieldWidth
     * @param int $labelWidth
     *
     * @return $this
     */
    public function setWidth($fieldWidth = 8, $labelWidth = 2)
    {
        collect($this->fields)->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field */
            $field->setWidth($fieldWidth, $labelWidth);
        });
        $this->attributes['fieldWidth'] = $fieldWidth;
        $this->attributes['labelWidth'] = $labelWidth;

        return $this;
    }

    /**
     * Find field class with given name.
     *
     * @param string $method
     *
     * @return bool|string
     */
    public static function findFieldClass($method)
    {
        $class = array_get(\Encore\Admin\Form::$availableFields, $method);

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

    /**
     * Add a form field to form.
     *
     * @param Field $field
     *
     * @return $this
     */
    protected function pushField(Field &$field)
    {
        array_push($this->fields, $field);

        return $this;
    }

    /**
     * Get variables for render form.
     *
     * @return array
     */
    protected function getVariables()
    {
        foreach ($this->fields as $field) {
            $field->fill($this->data);
        }

        return [

            'fields'        => $this->fields,
            'attributes'    => $this->formatAttribute(),
            'method'        => $this->attributes['method'],
            'buttons'    => $this->buttons,

            'fieldWidth'    => $this->attributes['fieldWidth'],
            'labelWidth'    => $this->attributes['labelWidth'],
            'rules'         => $this->getRules(),
            'rules_message' => $this->getRuleMessages(),
            'disable_footer'   => $this->disable_footer,
//======= todo check fieldWidth and ...
//            'fields'     => $this->fields,
//            'attributes' => $this->formatAttribute(),
//            'method'     => $this->attributes['method'],
//            'buttons'    => $this->buttons,
//>>>>>>> upstream/master
        ];
    }

    /**
     * Format form attributes form array to html.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function formatAttribute($attributes = [])
    {
        $attributes = $attributes ?: $this->attributes;

        if ($this->hasFile()) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = [];
        foreach ($attributes as $key => $val) {
            $html[] = "$key=\"$val\"";
        }

        return implode(' ', $html) ?: '';
    }

    /**
     * Determine if form fields has files.
     *
     * @return bool
     */
    public function hasFile()
    {
        foreach ($this->fields as $field) {
            if ($field instanceof Field\File) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get validation messages.
     *
     * @param array $input
     *
     * @return MessageBag|bool
     */
    protected function validationMessages($input)
    {
        $failedValidators = [];

        foreach ($this->fields as $field) {
            if (!$validator = $field->getValidator($input)) {
                continue;
            }

            if (($validator instanceof Validator) && !$validator->passes()) {
                $failedValidators[] = $validator;
            }
        }

        $message = $this->mergeValidationMessages($failedValidators);

        return $message->any() ? $message : false;
    }

    /**
     * Merge validation messages from input validators.
     *
     * @param \Illuminate\Validation\Validator[] $validators
     *
     * @return MessageBag
     */
    protected function mergeValidationMessages($validators)
    {
        $messageBag = new MessageBag();

        foreach ($validators as $validator) {
            $messageBag = $messageBag->merge($validator->messages());
        }

        return $messageBag;
    }

    /**
     * @return bool|Form|\Illuminate\Http\RedirectResponse
     */
    public function validate()
    {
        $data = Input::all();

        // Handle validation errors.
        if ($validationMessages = $this->validationMessages($data)) {
            return back()->withInput()->withErrors($validationMessages);
        }

        return self::$VALID;
    }

    /**
     * Collect rules of all fields.
     *
     * @return array
     */
    public function getRules()
    {
        $rules = [];
        foreach ($this->fields as $item) {
            if (!empty($item->getRules())) {
                $rules[$item->getId()] = $item->getRules();
            }
        }
        $this->Rules = $rules;

        return $rules;
    }

    /**
     * Collect validationMessages of all fields.
     *
     * @return array
     */
    public function getRuleMessages()
    {
        $rules = [];
        foreach ($this->fields as $item) {
            foreach ($item->validationMessages as $key => $value) {
                $rules[$key] = $value;
            }
        }
        $this->RuleMessages = $rules;

        return $rules;
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field|null
     */
    public function __call($method, $arguments)
    {
        if ($className = static::findFieldClass($method)) {
            $name = array_get($arguments, 0, '');

            $element = new $className($name, array_slice($arguments, 1));

            $this->pushField($element);

            return $element;
        }
    }

    /**
     * Render the form.
     *
     * @return string
     */
    public function render()
    {
        return view('admin::widgets.form', $this->getVariables())->render();
    }

    /**
     * Output as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
