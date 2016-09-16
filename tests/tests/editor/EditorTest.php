<?php

namespace Tests\Editor;

use TestCase;
use DJEM\Doctype;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    public $table = 'news';
    public $fillable = ['name', 'text'];
}

class NewsDocType extends Doctype
{
    public $model = News::class;
}

class Editor extends TestCase
{
    public function testEditor()
    {
        $editor = (new NewsDoctype())->editor();
        $this->assertNotEmpty($editor);

        $model = new News();
        $this->assertEquals($model, $editor->model());
    }

    public function testExistingModel()
    {
        $editor = (new NewsDoctype())->editor();
        $this->assertNotEmpty($editor);

        $model = News::first();
        $editor->loadModel($model);
        $this->assertEquals($model, $editor->model());
    }

    private function checkData($editor)
    {
        $data = collect($editor->getData());
        $attr = collect($editor->model()->getAttributes());

        // проверяем, что все поля модели есть в выдаче
        $attr->each(function ($value, $key) use ($data) {
            $this->assertEquals($value, $data->get($key));
        });

        // проверяем, что в выдаче нет ничего, кроме полей модели
        $data->each(function ($value, $key) use ($attr) {
            $this->assertEquals($value, $attr->get($key));
        });
    }

    public function testModelFields()
    {
        $editor = (new Doctype())->editor();

        $model = News::first();
        $editor->loadModel($model);
        $this->assertEquals($model, $editor->model());

        $this->checkData($editor);
    }

    public function testEmptyView()
    {
        $editor = (new Doctype())->editor();

        $model = News::first();
        $editor->loadModel($model);
        $this->assertEquals((object) [], $editor->getView());
    }

    public function testTabPanel()
    {
        $editor = (new Doctype())->editor();

        $model = News::first();
        $editor->loadModel($model);

        $editor->createTabPanel();
        $this->assertEquals((object) ['xtype' => 'tabpanel'], $editor->getView());

        $editor->createTabPanel()->region('center')->plain(true)->tabPosition('left');
        $this->assertEquals((object) [
            'xtype' => 'tabpanel',
            'region' => 'center',
            'plain' => true,
            'tabPosition' => 'left',
        ], $editor->getView());

        $this->checkData($editor);
    }

    public function testTags()
    {
        $editor = (new Doctype())->editor();

        $model = News::first();
        $editor->loadModel($model);

        $editor->createTag('name')->filterPickList(true)->store(['one', 'two', 'three'])->label('tag name');
        $this->assertEquals((object) [
            'xtype' => 'djem.tag',
            'name' => 'name',
            'filterPickList' => true,
            'store' => ['one', 'two', 'three'],
            'fieldLabel' => 'tag name',
            'queryMode' => 'local',
            'bind' => '{name}',
        ], $editor->getView());

        $this->checkData($editor);
    }
}