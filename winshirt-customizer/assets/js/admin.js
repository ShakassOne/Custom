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

    $(document).ready(function () {
        bindRepeatables();
        bindRemovers();
    });
})(jQuery);
