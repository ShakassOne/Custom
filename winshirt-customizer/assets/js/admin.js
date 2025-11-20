(function ($) {
    function bindRepeatables() {
        $('[data-add-row]').off('click').on('click', function (e) {
            e.preventDefault();
            const target = $(this).data('add-row');
            const table = $(`table[data-repeatable="${target}"] tbody`);
            const index = table.children().length;
            const template = table.children().first().clone(true);
            template.find('input').each(function () {
                const name = $(this).attr('name');
                $(this).val('');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
            template.find('select').each(function () {
                const name = $(this).attr('name');
                $(this).val($(this).find('option:first').val());
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
            table.append(template);
            bindRemovers();
            bindMediaUploaders();
        });
    }

    function bindRemovers() {
        $('.winshirt-remove').off('click').on('click', function (e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            if (row.siblings().length === 0) {
                row.find('input, select').val('');
            } else {
                row.remove();
            }
        });
    }

    function bindMediaUploaders() {
        $('.winshirt-upload-3d').off('click').on('click', function (e) {
            e.preventDefault();
            const button = $(this);
            const input = button.closest('td').find('input[type="url"]');
            const frame = wp.media({
                title: 'Sélectionner un fichier 3D',
                button: { text: 'Utiliser ce fichier' },
                multiple: false,
                library: {
                    type: ['model/gltf-binary', 'model/gltf+json', 'model/obj']
                }
            });

            frame.on('select', function () {
                const attachment = frame.state().get('selection').first().toJSON();
                if (attachment && attachment.url) {
                    input.val(attachment.url).trigger('change');
                }
            });

            frame.open();
        });
    }

    $(document).ready(function () {
        bindRepeatables();
        bindRemovers();
        bindMediaUploaders();
    });
})(jQuery);
