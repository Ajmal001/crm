define([
    'underscore',
    'orotranslation/js/translator',
    'jquery',
    'oroui/js/mediator',
    'oroui/js/delete-confirmation',
    '../../entity-management/entity-component-view',
    'jquery.select2'
], function (_, __, $, mediator, DeleteConfirmation, EntityComponentView) {
    'use strict';

    /** @type {Object.<Backbone.Collection>} **/
    var entitiesCollection;

    /** @const */
    var UPDATE_MARKER = 'formUpdateMarker';

    /**
     * Initialize "Entities selection" component
     *
     * @param {string} selector
     * @param {Array.<{object}>} metadata
     */
    function initializeEntityComponent(selector, metadata) {
        var $storageEl = $(selector),
            value = $storageEl.val(),
            entities = value ? JSON.parse(value) : [],
            entityComponentView = new EntityComponentView({
                data: entities,
                mode: EntityComponentView.prototype.MODES.EDIT_MODE,
                metadata: metadata
            });

        entityComponentView.render();
        $storageEl.after(entityComponentView.$el);
        entitiesCollection = entityComponentView.collection;
        entitiesCollection.on('add remove reset', function updateStorage () {
            $storageEl.val(JSON.stringify(entitiesCollection.pluck('name')));
        });
    }

    /**
     * Initialize "Channel type" component, and handle page reload
     *
     * @param {string} selector
     * @param {Object.<string, *>} fields
     */
    function initializeChannelTypeComponent(selector, fields) {
        var $el = $(selector);

        $el.on('change', function changeTypeHandler(e) {
            var prevEl  = e.removed,
                confirm = new DeleteConfirmation({
                    title:   __('orocrm.channel.confirmation.title'),
                    okText:  __('orocrm.channel.confirmation.agree'),
                    content: __('orocrm.channel.confirmation.text')
                });

            confirm.on('ok', function processChangeType() {
                var data,
                    $form = $el.parents('form'),
                    elementNames = _.map(fields, function (elementIdentifier) {
                        return $(elementIdentifier).attr('name');
                    });

                data = _.filter($form.serializeArray(), function (field) {
                    return _.indexOf(elementNames, field.name) !== -1;
                });
                data.push({name: UPDATE_MARKER, value: 1});

                var event = { formEl: $form, data: data, reloadManually: true };
                mediator.trigger('channelFormReload:before', event);

                if (event.reloadManually) {
                    mediator.execute('submitPage', {
                        url:  $form.attr('action'),
                        type: $form.attr('method'),
                        data: $.param(data)
                    });
                }
            });

            confirm.on('cancel', function revertChanges() {
                $el.select2('val', prevEl.id)
            });

            confirm.open();
        });
    }

    /**
     * Initialize channel form
     *
     * @param {Object} options
     */
    return function (options) {
        initializeEntityComponent(options.channelEntitiesEl, options.entitiesMetadata);
        initializeChannelTypeComponent(options.channelTypeEl, options.fields);

        options._sourceElement.remove();
    }
});
