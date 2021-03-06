<?php

namespace App\Doctypes;

use View;
use DJEM\Main\Grid;
use Illuminate\Http\Request;

class Colors extends \DJEM\Doctype
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function grid()
    {
        return Grid::custom($this);
    }

    public function load()
    {
        $editor = $this->editor();

        return [
            'data' => '',
            'code' => View::make('djem.colors-js')->render(),
            'view' => $editor->getView(),
        ];
    }

    public function editor()
    {
        $editor = parent::editor();

        $editor->createLayout('hbox')->items(function ($items) {
            $items->addStaticHtml($this->colors())->flex(1);
        });

        return $editor;
    }

    public function colors()
    {
        return View::make('djem.colors')->render();
    }
}
