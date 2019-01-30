<?php

namespace Encore\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class InfoBox extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.info-box';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * InfoBox constructor.
     *
     * @param string $name
     * @param string $icon
     * @param string $color
     * @param string $link
     * @param string $info
     */
    public function __construct($name, $icon, $class, $link, $info_html, $more_text = null, $bg_color = null)
    {
        if ($more_text == null) $more_text = trans('admin.more');

        if (!str_contains($icon, ['fab', 'fas', 'fa'])) {
            $icon = "fa fa-$icon";
        }

        $this->data = [
            'name'      => $name,
            'icon'      => $icon,
            'link'      => $link,
            'info_html'      => $info_html,
            'more_text' => $more_text,
        ];

        $style = [
            "cursor: pointer",
           "color: white !important"
        ];
        if ($bg_color != null) {
            $style = array_merge($style,["background-color: {$bg_color} !important"]);
        }

        $this->style(implode(";",$style));

        $this->class("small-box $class");
    }

    /**
     * @return string
     */
    public function render()
    {
        $variables = array_merge($this->data, ['attributes' => $this->formatAttributes()]);

        return view($this->view, $variables)->render();
    }
}
