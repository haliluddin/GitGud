$(document).ready(function() {
    $('.customSelectMultiple').each(function() {
        var dropdownParents = $(this).parents('.select2Part');

        $(this).select2({
            dropdownParent: dropdownParents,
        });

        if ($(this).val() && $(this).val().length > 0) {
            $(this).parents('.form-group').addClass('focused');
        }

        $(this)
            .on("select2:open", function () {
                $(this).parents('.form-group').addClass('focused');
            })
            .on("select2:close", function () {
                if ($(this).val() && $(this).val().length > 0) {
                    $(this).parents('.form-group').addClass('focused');
                } else {
                    $(this).parents('.form-group').removeClass('focused');
                }
            })
            .on("select2:select", function () {
                $(this).parents('.form-group').addClass('focused');
            })
            .on("select2:unselect", function () {
                if ($(this).val() && $(this).val().length > 0) {
                    $(this).parents('.form-group').addClass('focused');
                } else {
                    $(this).parents('.form-group').removeClass('focused');
                }
            });
    });
});