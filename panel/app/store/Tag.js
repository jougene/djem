/* global Ext */
Ext.define('djem.store.Tag', {
    extend: 'Ext.data.Store',

    requires: [
        'djem.store.proxy'
    ],

    model: 'djem.model.Tag',
    autoLoad: true,

    proxy: {
        type: 'djem',
        url: 'api/content/load'
    }
});
