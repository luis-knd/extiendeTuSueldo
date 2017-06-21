/**
 * Funcion para colocar el formulario con el campo SPAN en efecto en transición.
 * modifica la clase input-contact de campo input y textarea-contact del campo textarea.
 * le agrega la clase de css active
 */

$(window).load(function () {
    $(".input-contact input, .textarea-contact textarea").focus(function () {
        $(this).next("span").addClass("active");
    });
    $(".input-contact input, .textarea-contact textarea").blur(function () {
        if ($(this).val() === "") {
            $(this).next("span").removeClass("active");
        }
    });
});