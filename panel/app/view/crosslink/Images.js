Ext.define('djem.view.crosslink.Images', {
  extend: 'djem.view.crosslink.Files',
  alias: 'widget.crosslink.Images',
  
  cls: 'x-form-crosslink-images-wrap',

  controller: 'crosslink-images',
  // viewModel: { type: 'crosslink-images' },

  overItemCls: 'x-grid-item-over',
  tpl: [
    '<tpl for=".">', '<div class="thumb-wrap {new}">', '<a href="#" class="trash">&#xF156;</a>',
    '<div class="thumb" style="background-repeat: no-repeat;',
    'background-image: url({url});background-position:{calcOffset};background-size:100%;background-size:{calcZoom}">',
    '</div>', '<span>{name}</span>', '</div>', '</tpl>'
  ]
});