<?php

namespace App\Doctypes;

use App\Models;
use Illuminate\Http\Request;

class News extends \DJEM\Doctype
{
    use Traits\UploadImage;

    public $model = Models\News::class;

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function gridItems()
    {
        $items = (new $this->model())->orderBy('id');
        $items = $items->paginate($this->request->input('limit'));

        $items->each(function ($item, $key) {
            $item->_doctype = get_class($this);
        });

        return $items;
    }

    public function editor()
    {
        $editor = parent::editor();

        $editor->createLayout('hbox')->flex(1)->items(function ($items) {
            $items->addLayout(['type' => 'vbox', 'align' => 'stretch'])->flex(1)->items(function ($items) {
                $items->addText('name')->label('Name')->validate('required|max:255');
                $items->addTag('tagsList')->label('Field Tags')->filterPickList(true)->store(['one', 'two', 'three']);
                $items->addRichText('text')->label('Text')->flex(1);
                $items->addFile('document')->height('40px')->flex(1);
            });
            $items->addImage('images')->height('100%')->width('20%')->save($this->uploadImage());
        });

        return $editor;
    }
}
