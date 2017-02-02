/* global Ext */
Ext.define('djem.view.crosslink.ImagesController', {
  extend: 'djem.view.crosslink.FilesController',
  alias: 'controller.crosslink-images',

  initAfterRender: function() {
    var me = this, view = me.getView(), el = view.getEl();

    me.processDropZone(false);

    var form = view.up('form');
    if (form) {
      view.on('initValue', function() {
        me.setDirty(true);
        me.field.resetOriginalValue();
      });

      var field = Ext.create('djem.view.crosslink.FileField', { name: view.name });
      me.field = form.add(field);
      me.field.on('dataReady', function() { form.getForm().fireEvent('dataReady'); });
      me.field.on('change', function(_this, value) {
        if (view.single) {
          value = Ext.decode(value);
          var url = value && value[0] && value[0].url;

          if (me.isNewImage(url)) {
            me.setImage(url);
          }
        }
      });
      form.getForm().on('syncFields', function() {
        if (!me.field.validate()) {
          // если не загружали файл на сервер - загрузим
          me.uploadFiles();
        }
      });
    }
    el.on('filechange', function(e, target) {
      if (target) {
        me.dropFiles({ dataTransfer: target });
      }
      e.preventDefault();
      e.stopPropagation();
    });
    el.on('mouseup', function(e) {
      if (Ext.get(e.target).hasCls('trash')) {
        var dom = Ext.get(e.target).up('.thumb-wrap'), record = dom && view.getRecord(dom);

        window.URL.revokeObjectURL(record.data.url);
        view.getStore().remove(record);

        me.setDirty(true);
      }
    });
    if (view.single) {
      view.on('resize', function() { me.recalcImageZoom(); });
      view.on('show', function() { me.recalcImageZoom(); });
      el.on('mousedown', function(evt) {
        if (view.getStore().getCount() == 1) {
          var body = Ext.get(document.body), iframe = Ext.select('iframe'), rec = view.getStore().getAt(0);
          var offset = rec.get('offset') || { x: 0, y: 0 }, zoom = me.getImageZoom();

          body.addCls('x-unselectable');
          iframe.setStyle('pointer-events', 'none');

          me.setImageMoveOffset({ x: evt.event.screenX + offset.x * zoom, y: evt.event.screenY + offset.y * zoom });

          var mousemove = function(evt) { return me.onMouseMove(evt); };
          var detach = function() {
            body.removeCls('x-unselectable');
            iframe.setStyle('pointer-events', null);
            window.removeEventListener('mouseup', detach, true);
            window.removeEventListener('mousemove', mousemove, true);
          };
          window.addEventListener('mousemove', mousemove, true);
          window.addEventListener('mouseup', detach, true);
        }
      });
    }
  },

  applyImage: function(href) {
    var me = this, view = me.getView(), image = new Image();

    if (!href) {
      return;
    }

    var prevImage = me.getImage();
    if (prevImage) {
      prevImage.onload = null;
    }

    image['data-href'] = href;

    me.setImageZoom(1);
    view.setStyle('cursor', 'wait');

    image.onload = function() {
      view.setStyle('cursor', 'default');
      me.recalcImageZoom();
      me.moveSingleImage({ x: 0, y: 0 });
    };

    if (href) {
      image.src = href;
    }

    return image;
  }

});