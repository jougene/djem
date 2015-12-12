<?php

namespace DJEM;

class GridHeader
{
    private function nameField($row)
    {
        $column = [];
        if (isset($row['text'])) {
            $column = [
                'hideable' => false,
                'dataIndex' => $row['name']
            ];
        }
        return [ 'field' => [ 'name' => $row['name'] ], 'column' => $column ];
    }

    private function typeField($row)
    {
        return [ 'field' => [ 'type' => $row['type'] ], 'column' => [] ];
    }

    private function sortableField($row)
    {
        return [ 'field' => [], 'column' => [ 'sortable' => $row['sortable'] ] ];
    }

    private function textField($row)
    {
        return [ 'field' => [], 'column' => [ 'text' => $row['text'] ] ];
    }

    private function flexField($row)
    {
        return [ 'field' => [], 'column' => [ 'flex' => $row['flex'] ] ];
    }

    private function widthField($row)
    {
        return [ 'field' => [], 'column' => [ 'width' => $row['width'] ] ];
    }

    public function getFields($fieldsData)
    {
        $fields = [];
        $columns = [];
        $options = [];

        foreach ($fieldsData as $row) {
            $field = [];
            $column = [];
            foreach ($row as $key => $value) {
                if (method_exists(get_called_class(), $method = $key.'Field')) {
                    $data = $this->$method($row);
                    $field = array_merge($field, $data['field']);
                    $column += array_merge($column, $data['column']);
                }
            }

            if (!empty($row['name']) && !empty($row['title'])) {
                $options['title'] = $row['name'];
            }

            $fields[] = $field;
            if (count($column)) {
                $columns[] = $column;
            }
        }
        return [ 'fields' => $fields, 'columns' => $columns, 'options' => $options ];
    }
}