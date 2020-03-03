/* Menu  dropdown headre*/
$(document).click(function (event) {
    if (!$(event.target).closest(".dropdown_item, .drp").length) {
        $(".dropdown_item").removeClass("active");
    }
});
$(document).ready(function () {
    $('.drp').on('click', function (e) {
        e.preventDefault()
        $('.dropdown_item').removeClass('active')
        $(this).parent().addClass('active')
    })
})
/* end  dropdown headre*/



/* Home  slidre */
$(document).ready(function () {
    $('#topBrenslidre').owlCarousel({
        loop: false,
        margin: 10,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
                nav: true
            },
            600: {
                items: 4,
                nav: true
            },
            1000: {
                items: 7,
                nav: true,
                loop: false,
                dots: false,
                margin: 20
            }
        }
    })
});

$(document).ready(function () {
    $('#topBrenslidre2').owlCarousel({
        loop: false,
        margin: 10,
        responsiveClass: true,
        responsive: {
            0: {
                items: 3,
                nav: true
            },
            600: {
                items: 4,
                nav: true
            },
            1000: {
                items: 7,
                nav: true,
                loop: false,
                dots: false,
                margin: 20
            }
        }
    })
});

/* end Home  slidre */

/* Start filter Right*/
$(document).ready(function ($) {
    $('.click-filter').on('click', function () {
        if ($('.filters-pages').hasClass('active')) {

            $('.filters-pages').removeClass('active');
        }
        else {
            $('.filters-pages').addClass('active');
        }
    });
});
/* end filter Right*/

/* claick yurakcha color*/
$('.btn-favorite-view').on('click', function () {
    $(this).toggleClass('btn-favorite--is-active');
});

$('.filter-sum').on('click', function () {
    $(this).toggleClass('is-active');
});
/* claick yurakcha color*/


/* start  bootstrap input increment decrement*/

$('.btn-number').click(function (e) {
    e.preventDefault();

    fieldName = $(this).attr('data-field');
    type = $(this).attr('data-type');
    var input = $("input[name='" + fieldName + "']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if (type == 'minus') {

            if (currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            }

        } else if (type == 'plus') {

            if (currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }

        }
    } else {
        input.val(0);
    }
});

// $('.input-number').focusin(function(){
//    $(this).data('oldValue', $(this).val());
// });

// $('.input-number').change(function() {

//     minValue =  parseInt($(this).attr('min'));
//     maxValue =  parseInt($(this).attr('max'));
//     valueCurrent = parseInt($(this).val());

//     name = $(this).attr('name');
//     if(valueCurrent >= minValue) {
//         $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
//     } else {
//         alert('Sorry, the minimum value was reached');
//         $(this).val($(this).data('oldValue'));
//     }
//     if(valueCurrent <= maxValue) {
//         $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
//     } else {
//         alert('Sorry, the maximum value was reached');
//         $(this).val($(this).data('oldValue'));
//     }


// });
// $(".input-number").keydown(function (e) {
//       // Allow: backspace, delete, tab, escape, enter and .
//       if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
//            // Allow: Ctrl+A
//           (e.keyCode == 65 && e.ctrlKey === true) || 
//            // Allow: home, end, left, right
//           (e.keyCode >= 35 && e.keyCode <= 39)) {
//                // let it happen, don't do anything
//                return;
//       }
//       // Ensure that it is a number and stop the keypress
//       if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
//           e.preventDefault();
//       }
//   });
/* end  bootstrap input increment decrement*/

/* start loaded */
$(document).ready(function () {

});
/* end loaded */
$(window).on('load',function() {
    $('body').addClass('loaded');
});
/* start download file */
$(".btn-inputfile").change(function () {
    var previewImageID = $(this).parent().find("img").attr("id");
    // alert(previewImageID);
    previewFile(this, previewImageID);
});

function previewFile(input, image) {
    var preview = document.getElementById(image);
    var file = input.files[0];
    var reader = new FileReader();
    reader.addEventListener("load", function () {
        preview.src = reader.result;
    }, false);
    if (file) {
        reader.readAsDataURL(file);
    }
}
/* end download file */

/* icon next icon bottom */
$('#accordion .btn-link').on('click', function () {
    $(this).toggleClass('a-is-active');
});
/* end icon next icon bottom */


/* start  sercha*/
let inp = document.getElementById('sinput');

inp.addEventListener('click', function () {
    document.getElementById('parent').classList.add('one')
    $(":input[name=search_input]").focus();
})
let x = document.getElementById('xbtn');
x.addEventListener('click', function (event) {
    document.getElementById('parent').classList.remove('one')
    $(":input[name=search_input]").val("")
    event.preventDefault()
})
$(document).click(function (event) {
    if (!$(event.target).closest(".sorchemy").length && $(":input[name=search_input]").val() == "") {
        $(".sorchemy").removeClass("one");
    }
});
/* start  sercha*/

$(document).ready(function () {
    $('.clickEvent').on('click', function (e) {
        e.stopPropagation();
        $('.topmobilgfamicon').toggleClass('active');
    });
    $("body").click(function () {
        if ($('.topmobilgfamicon').hasClass('active'))
            $('.topmobilgfamicon').removeClass('active');
    });
});
$(document).ready(function () {
    // Hides all paragraphs
    $(".titleNone-block").hide();
    // Optional for showing the first paragraph. For animation use .slideDown(200) instead of .show()
    $(".titleNone-block-filter").first().show();

    $(".titleNone").click(function () {
        // Toggles the paragraph under the header that is clicked. .slideToggle(200) can be changed to .slideDown(200) to make sure one paragraph is shown at all times.
        $(this).next(".titleNone-block").slideToggle(200);
        // Makes other pararaphes that is not under the current clicked heading dissapear
        $(this).siblings().next(".titleNone-block").slideUp(200);
    });

    let previousScrollY = 0;

    $(document).on('show.bs.modal', () => {
        previousScrollY = window.scrollY;
        $('html').addClass('modal-open').css({
            marginTop: -previousScrollY,
            overflow: 'hidden',
            left: 0,
            right: 0,
            top: 0,
            bottom: 0,
            position: 'fixed',
        });
    }).on('hidden.bs.modal', () => {
        $('html').removeClass('modal-open').css({
            marginTop: 0,
            overflow: 'visible',
            left: 'auto',
            right: 'auto',
            top: 'auto',
            bottom: 'auto',
            position: 'static',
        });
        window.scrollTo(0, previousScrollY);
    });
});