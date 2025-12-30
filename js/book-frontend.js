function open_category_popup() {
  var popup = document.getElementById("category_popuptext");
  popup.classList.toggle("category_show");
}

function roll_to_cat(id) {
  var popup = document.getElementById("category_popuptext");
  popup.classList.toggle("category_show");
  // console.log($("#link_term_" + id).offset().top);
  var $nav = jQuery(".mobile-menu-toggle");
  var nar_height = $nav.height();
  var tudc2 = 0;
  if (jQuery("#wpadminbar").length > 0) {
    tudc2 = jQuery("#wpadminbar").height();
  }
  if (jQuery(this).width() <= 768) {
    if (jQuery(".shop-mobile").length > 0) {
      jQuery("html, body").animate(
        {
          scrollTop:
            jQuery("#link_term_" + id).offset().top -
            nar_height - tudc2 -
            jQuery(".menu-categories").height() -
            jQuery(".shop-notify.shop-mobile")[0].offsetHeight,
        },
        300
      );
    } else {
      jQuery("html, body").animate(
        {
          scrollTop:
            jQuery("#link_term_" + id).offset().top -
            nar_height - tudc2 -
            jQuery(".menu-categories").height(),
        },
        300
      );
    }
  } else {
    if (jQuery(".hihi").length > 0) {
      jQuery("html, body").animate(
        {
          scrollTop:
            jQuery("#link_term_" + id).offset().top -
            nar_height -
            tudc2 -
            jQuery(".hihi").height() -
            10,
        },
        300
      );
    } else {
      jQuery("html, body").animate(
        {
          scrollTop:
            jQuery("#link_term_" + id).offset().top - nar_height - tudc2 - 10,
        },
        300
      );
    }
  }
}
jQuery.event.special.touchstart = {
  setup: function( _, ns, handle ){
    if ( ns.includes("noPreventDefault") ) {
      this.addEventListener("touchstart", handle, { passive: false });
    } else {
      this.addEventListener("touchstart", handle, { passive: true });
    }
  }
};

function fix_layout_menu($){
  if ($(".hihi").is(":visible")){
    let top = 0;
    if($("#wpadminbar").is(":visible")){
      top += $("#wpadminbar").height();
    }
    if($(".outer-wrap").is(":visible")){
      top += $(".outer-wrap").height();
    }
    if($(".mobile-menu-toggle").is(":visible")){
      top += $(".mobile-menu-toggle").height();
    }

    $(".hihi").css({ top: top + "px" })
  }

  let height = window.innerHeight; 
  let adminbar = 0;
  let header = 0;
  let floatcart = 0;
  let notify = 0;
  let headerMobie = 0;
  if($("#wpadminbar").is(":visible")){
    adminbar = $("#wpadminbar").height();
  }
  if($(".outer-wrap").is(":visible")){
    header = $(".outer-wrap").innerHeight();
  }
  if($(".mobile-menu-toggle").is(":visible")){
    headerMobie = $(".mobile-menu-toggle").innerHeight();
  }
  if($(".hihi").is(":visible")){
    notify = $(".hihi").innerHeight();
  }
  if($(".dsmart-float-cart").is(":visible")){
    floatcart = $(".dsmart-float-cart").outerHeight();
  }
  let topPos = (adminbar + header + headerMobie + notify) + "px";
  let heightheight = (height - (adminbar + header + floatcart + headerMobie + notify)) + "px";
  $(".menu-categories-wrapper").css({ top: topPos });
  $(".menu-categories-wrapper").css({height:heightheight});

}

jQuery(document).ready(function ($) {

  fix_layout_menu($);

  $('.nice-select-custom').niceSelect();
  
  $("#myModal").click(function() {
		$("#myModal").hide();
	});

  $("#myModal2").click(function() {
		$("#myModal2").hide();
	});

  if($(".dsmart-float-cart").hasClass("active") && $(".dsmart-show-notify").is(":visible")){
    // document.getElementsByClassName("dsmart-show-notify")[0].style.bottom = 5 + document.getElementsByClassName("dsmart-show-notify")[0].offsetHeight + "px";
    document.getElementsByClassName("dsmart-show-notify")[0].style.bottom = $('.dsmart-show-notify').outerHeight(true) + "px";
  }
  
  if ($(window).width() <= 1350){
    $(".list-product .item h3.title sup").css("position", "inherit");
  }else{
    $(".list-product .item h3.title sup").css("position", "relative");
  }

  if ($(window).width() > 768) {
    fix_layout_menu($);
    $('#category_popuptext').css("display", "none");
    $(".outer_category_box").hide();
    $(".menu-categories-wrapper").show();
  }
  else{
    $(".outer_category_box").show();
    $('#category_popuptext').css("display", "flex");
    $(".menu-categories-wrapper").hide();
  }

  // console.log($($('.container').get(1)).width());
  jQuery("body").addClass("loaded"); // that is :)
  //Preloader Effect
  //code
  
  $(".change_when_scroll").each(function (index, value) {
    var waypoint = new Waypoint({
      element: value,
      handler: function (direction) {
        if (direction === "down") {
          $(".category_single").removeClass("active");
          $(".big_menu_side").removeClass("active");
          var id = $(value).parent().parent().children("input")[0].value;
          $("#menu_side_" + id).addClass("active");
          $("#big_menu_side_" + id).addClass("active");
          $(".current_category").text($("#menu_side_" + id).text());
          
        } else {
          if (index > 0) {
            $(".category_single").removeClass("active");
            $(".big_menu_side").removeClass("active");
            var id = $($(".change_when_scroll").get(index - 1))
              .parent()
              .parent()
              .children("input")[0].value;
            $("#menu_side_" + id).addClass("active");
            $("#big_menu_side_" + id).addClass("active");
            $(".current_category").text($("#menu_side_" + id).text());
          }
        }
      },
      offset: "50%",
    });
  });

  window.onresize = function () {
    if ($(window).width() <= 1350){
      $(".list-product .item h3.title sup").css("position", "inherit");
    }else{
      $(".list-product .item h3.title sup").css("position", "relative");
    }
    if ($(this).width() > 768) {
      fix_layout_menu($);
      $('#category_popuptext').css("display", "none");
      $(".outer_category_box").hide();
      $(".menu-categories-wrapper").show();
    }else{
      $(".outer_category_box").show();
      $('#category_popuptext').css("display", "flex");
      $(".menu-categories-wrapper").hide();
    }
    
    if($(".dsmart-float-cart").hasClass("active") && $(".dsmart-show-notify").is(":visible")){
      // document.getElementsByClassName("dsmart-show-notify")[0].style.bottom = 5 + document.getElementsByClassName("dsmart-show-notify")[0].offsetHeight + "px";
      document.getElementsByClassName("dsmart-show-notify")[0].style.bottom = $('.dsmart-float-cart').outerHeight(true) + "px";
    }
    if ($(window).width() > 768) {
      fix_layout_menu($);
      $(".menu-categories ul").trigger("destroy.owl.carousel");
      $(".menu-categories ul").removeClass("owl-carousel");
      $(".add-to-cart").css("position", "relative;");
      $(".custom-radio-checkbox").css("position", "relative;");
      $(".variable-select").css("position", "relative;");
      $(".menu-categories-wrapper").show();
    } else {
      $(".add-to-cart").css("position", "inherit");
      $(".custom-radio-checkbox").css("position", "inherit");
      $(".variable-select").css("position", "inherit");
      $(".menu-categories ul").trigger("destroy.owl.carousel");
      $(".menu-categories ul").removeClass("owl-carousel");
      $(".menu-categories ul").addClass("owl-carousel");
      $(".menu-categories-wrapper").hide();
    }
  };

  $(window).scroll(function () {
    fix_layout_menu($);
    if($(".dsmart-float-cart").hasClass("active") && $(".dsmart-show-notify").is(":visible")){
      // document.getElementsByClassName("dsmart-show-notify")[0].style.bottom = 5 + document.getElementsByClassName("dsmart-show-notify")[0].offsetHeight + "px";
      document.getElementsByClassName("dsmart-show-notify")[0].style.bottom =$('.dsmart-float-cart').outerHeight(true) + "px";
    }

    //console.log($(this).scrollTop() );
    if ($(this).width() > 768) {
      jQuery("#copyright").addClass("damn");
      $(".outer_category_box").hide();
    }
  });

  const $ratingLabel = $(".filter-stars a.filter-label"),
    $priceRange = $(".price-range .range"),
    $bookLoading = $(".book-loading"),
    $popupAlert = $(".popup-alert"),
    $body = $("body"),
    $Notify = $(".dsmart-notify");
  $couponHtml = `<tr class="cart-coupon" data-type="{{type}}" data-coupon="{{key}}">
            <td>
                <span class="remove-coupon">x</span>
                Rabattcode ({{key}})
            </td>
            <td>
                <span class="coupon-text">{{price}}</span>
            </td>
        </tr>`;
  function show_alert(text) {
    var time = bookingVars.popup_time;
    $(".popup-alert").remove();
    $body.append(
      `<div class="popup-alert"><div class="popup-alert-wrap">${text}</div></div>`
    );
    $(".popup-alert").fadeIn();
    setTimeout(function () {
      $(".popup-alert").fadeOut();
    }, time);
  }
  function setCookie(name, value, days) {
    var expires = "";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }
  function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }
  $ratingLabel.on("mouseenter", function () {
    var rating = parseInt($(this).attr("data-rating"));
    var i = 1;
    for (i = 1; i <= rating; i++) {
      $(`.filter-stars a.filter-label:nth-child(${i})`).addClass("active");
    }
  });
  $ratingLabel.on("mouseleave", function () {
    $ratingLabel.removeClass("active");
  });
  if ($priceRange.length > 0 && $priceRange.val() != "") {
    var input_val = $priceRange.val();
    var arr = input_val.split(";");
    $priceRange.ionRangeSlider({
      min: bookingVars.min_price,
      max: bookingVars.max_price,
      from: arr[0],
      to: arr[1],
      type: "double",
      prefix: bookingVars.currency,
      grid: false,
    });
  } else {
    $priceRange.ionRangeSlider({
      min: bookingVars.min_price,
      max: bookingVars.max_price,
      from: bookingVars.min_price,
      to: bookingVars.max_price,
      type: "double",
      prefix: bookingVars.currency,
      grid: false,
    });
  }
  $(".plus-product").on("click", function () {
    var value = parseInt($(this).siblings("input[name=quantity-input]").val());
    if (value == 0) {
      value = 1;
    }
    value = value + 1;
    $(this).siblings("input[name=quantity-input]").val(value);
  });
  $(".minus-product").on("click", function () {
    var value = parseInt($(this).siblings("input[name=quantity-input]").val());
    value = value - 1;
    if (value < 1) {
      value = 1;
    }
    $(this).siblings("input[name=quantity-input]").val(value);
  });
  var timer;
  $("#user-location").keyup(function () {
    var address = jQuery("#user-location").val();
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address }, function (results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        var latitude = results[0].geometry.location.lat();
        var longitude = results[0].geometry.location.lng();
        jQuery("input[name=latitude]").val(latitude);
        jQuery("input[name=longitude]").val(longitude);
      }
    });
    clearTimeout(timer);
    timer = setTimeout(function () {
      let shipping_method = $(
        ".dsmart-cart-total select[name=choose-shipping]"
      ).val();
      let user_location = $(
        ".dsmart-cart-total input[name=user-location]"
      ).val();
      let latitude = $("input[name=latitude]").val();
      let longitude = $("input[name=longitude]").val();
      let delivery_time = $("input[name=delivery_time]").val();
      let delivery_date = $("input[name=delivery_date]").val();
      if (user_location != "" && latitude != "" && longitude != "") {
        $Notify.hide();
        $.ajax({
          type: "POST",
          dataType: "json",
          url: bookingVars.ajaxurl,
          data: {
            action: "get_shipping_fee",
            shipping_method: shipping_method,
            user_location: user_location,
            latitude: latitude,
            longitude: longitude,
            delivery_time: delivery_time,
            delivery_date: delivery_date,
          },
          beforeSend: function () {
            $bookLoading.show();
          },
          success: function (data) {
            console.log(data);
            $bookLoading.hide();
            if (data.check == false) {
              $Notify
                .removeClass("dsmart-danger")
                .removeClass("dsmart-success");
              $Notify.show();
              $Notify.addClass("dsmart-danger");
              $Notify.text(data.message);
              $(".cart-discount").addClass("hidden");
              $(".shipping-text").text(data.shipping);
              $(".total-text").text(data.total);
              $(".cart-coupon").remove();
              $.each(data.coupon, function (key, val) {
                var $coupon = `<tr class="cart-coupon" data-type="${val.type}" data-coupon="${key}">
                                    <td>
                                        <span class="remove-coupon">x</span>
                                        Rabattcode (${key})
                                    </td>
                                    <td>
                                        <span class="coupon-text">${val.price}</span>
                                    </td>
                                </tr>`;
                $(".order-total").before($coupon);
              });
            } else {
              $(".shipping-text").text(data.shipping);
              $(".total-text").text(data.total);
              $(".cart-coupon").remove();
              $.each(data.coupon, function (key, val) {
                var $coupon = `<tr class="cart-coupon" data-type="${val.type}" data-coupon="${key}">
                                    <td>
                                        <span class="remove-coupon">x</span>
                                        Rabattcode (${key})
                                    </td>
                                    <td>
                                        <span class="coupon-text">${val.price}</span>
                                    </td>
                                </tr>`;
                $(".order-total").before($coupon);
              });
              $(".cart-discount .percent").text(` (${data.reduce_percent})`);
              $(".cart-discount .number").text(`- ${data.reduce}`);
              if (data.has_reduce === true) {
                $(".cart-discount").removeClass("hidden");
              } else {
                $(".cart-discount").addClass("hidden");
              }
            }
          },
        });
      } else if (user_location != "" && latitude == "" && longitude == "") {
        $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
        $Notify.show();
        $Notify.addClass("dsmart-danger");
        $Notify.text("Die Adresse existiert nicht in Google Maps");
        $(".cart-discount").addClass("hidden");
      } else {
        $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
        $Notify.show();
        $Notify.addClass("dsmart-danger");
        $Notify.text("Die Adresse existiert nicht in Google Maps");
        $(".cart-discount").addClass("hidden");
      }
    }, 1000);
  });
  $("#user-location").on("change", function () {
    let shipping_method = $(
      ".dsmart-cart-total select[name=choose-shipping]"
    ).val();
    let user_location = $(".dsmart-cart-total input[name=user-location]").val();
    let latitude = $("input[name=latitude]").val();
    let longitude = $("input[name=longitude]").val();
    let delivery_time = $("input[name=delivery_time]").val();
    let delivery_date = $("input[name=delivery_date]").val();
    if (user_location != "" && latitude != "" && longitude != "") {
      $Notify.hide();
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "get_shipping_fee",
          shipping_method: shipping_method,
          user_location: user_location,
          latitude: latitude,
          longitude: longitude,
          delivery_time: delivery_time,
          delivery_date: delivery_date,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == false) {
            $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
            $Notify.show();
            $Notify.addClass("dsmart-danger");
            $Notify.text(data.message);
            show_alert(data.message);
            $(".cart-discount").addClass("hidden");
            $(".shipping-text").text(data.shipping);
            /*$(".tax-text7").text(data.vat7);
                        $(".tax-text19").text(data.vat19);*/
            $(".total-text").text(data.total);
            $(".cart-coupon").remove();
            $.each(data.coupon, function (key, val) {
              var $coupon =
                '<tr class="cart-coupon" data-type="' +
                val.type +
                '" data-coupon="' +
                key +
                '"><td><span class="remove-coupon">x</span>Rabattcode (' +
                key +
                ')</td><td><span class="coupon-text">' +
                val.price +
                "</span></td></tr>";
              $(".order-total").before($coupon);
            });
          } else {
            $(".shipping-text").text(data.shipping);
            /*$(".tax-text7").text(data.vat7);
                        $(".tax-text19").text(data.vat19);*/
            $(".total-text").text(data.total);
            $(".cart-coupon").remove();
            $.each(data.coupon, function (key, val) {
              var $coupon =
                '<tr class="cart-coupon" data-type="' +
                val.type +
                '" data-coupon="' +
                key +
                '"><td><span class="remove-coupon">x</span>Rabattcode (' +
                key +
                ')</td><td><span class="coupon-text">' +
                val.price +
                "</span></td></tr>";
              $(".order-total").before($coupon);
            });
            $(".cart-discount .percent").text(" (" + data.reduce_percent + ")");
            $(".cart-discount .number").text("- " + data.reduce);
            if (data.has_reduce === true) {
              $(".cart-discount").removeClass("hidden");
            } else {
              $(".cart-discount").addClass("hidden");
            }
          }
        },
      });
    } else if (user_location != "" && latitude == "" && longitude == "") {
      $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
      $Notify.show();
      $Notify.addClass("dsmart-danger");
      $Notify.text("Die Adresse existiert nicht in Google Maps");
      $(".cart-discount").addClass("hidden");
      show_alert("Die Adresse existiert nicht in Google Maps");
    } else {
      $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
      $Notify.show();
      $Notify.addClass("dsmart-danger");
      $Notify.text("Die Adresse existiert nicht in Google Maps");
      $(".cart-discount").addClass("hidden");
      show_alert("Die Adresse existiert nicht in Google Maps");
    }
  });
  $("body").on(
    "change",
    ".shipping_caculate select[name='choose-shipping'],input[name=delivery_date],input[name=delivery_time]",
    function () {
      let shipping_method = $("select[name=choose-shipping]").val();
      let user_location = $(
        ".dsmart-cart-total input[name=user-location]"
      ).val();
      let latitude = $("input[name=latitude]").val();
      let longitude = $("input[name=longitude]").val();
      let shipping_time = $("input[name=shipping_time]").val();
      let delivery_time = $("input[name=delivery_time]").val();
      let delivery_date = $("input[name=delivery_date]").val();
      let filled_zipcode = $("input[name=zipcode-location]").val();
      $(".cart-discount").addClass("hidden");
      if (shipping_method != "") {
        if (shipping_method == "shipping") {
          /*if(user_location != "" && latitude != "" && longitude != ""){*/
          $Notify.hide();
          $.ajax({
            type: "POST",
            dataType: "json",
            url: bookingVars.ajaxurl,
            data: {
              action: "get_shipping_fee",
              shipping_method: shipping_method,
              user_location: user_location,
              latitude: latitude,
              longitude: longitude,
              delivery_date: delivery_date,
              delivery_time: delivery_time,
              filled_zipcode: filled_zipcode,
            },
            beforeSend: function () {
              $bookLoading.show();
            },
            success: function (data) {
              $bookLoading.hide();
              if (data.check == false) {
                $Notify
                  .removeClass("dsmart-danger")
                  .removeClass("dsmart-success");
                $Notify.show();
                $Notify.addClass("dsmart-danger");
                $Notify.text(data.message);
                show_alert(data.message);
                $(".cart-discount").addClass("hidden");
                $(".shipping-text").text(data.shipping);
                /*$(".tax-text7").text(data.vat7);
                                $(".tax-text19").text(data.vat19);*/
                $(".total-text").text(data.total);
                $(".cart-coupon").remove();
                $.each(data.coupon, function (key, val) {
                  var $coupon =
                    '<tr class="cart-coupon" data-type="' +
                    val.type +
                    '" data-coupon="' +
                    key +
                    '"><td><span class="remove-coupon">x</span>Rabattcode (' +
                    key +
                    ')</td><td><span class="coupon-text">' +
                    val.price +
                    "</span></td></tr>";
                  $(".order-total").before($coupon);
                });
              } else {
                $(".shipping-text").text(data.shipping);
                /*$(".tax-text7").text(data.vat7);
                                $(".tax-text19").text(data.vat19);*/
                $(".total-text").text(data.total);
                $(".cart-coupon").remove();
                $.each(data.coupon, function (key, val) {
                  var $coupon =
                    '<tr class="cart-coupon" data-type="' +
                    val.type +
                    '" data-coupon="' +
                    key +
                    '"><td><span class="remove-coupon">x</span>Rabattcode (' +
                    key +
                    ')</td><td><span class="coupon-text">' +
                    val.price +
                    "</span></td></tr>";
                  $(".order-total").before($coupon);
                });
                $(".cart-discount .percent").text(
                  " (" + data.reduce_percent + ")"
                );
                $(".cart-discount .number").text("- " + data.reduce);
                if (data.has_reduce === true) {
                  $(".cart-discount").removeClass("hidden");
                } else {
                  $(".cart-discount").addClass("hidden");
                }
              }
            },
          });
          /*}else if(user_location != "" && latitude == "" && longitude == ""){
                    $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
                    $Notify.show();
                    $Notify.addClass("dsmart-danger");
                    $Notify.text("Die Adresse existiert nicht in Google Maps");
                    $(".cart-discount").addClass("hidden");
                }else{*/
          // $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
          // $Notify.show();
          // $Notify.addClass("dsmart-danger");
          // $Notify.text("Die Adresse existiert nicht in Google Maps");
          /*}*/
        } else {
          $Notify.hide();
          $.ajax({
            type: "POST",
            dataType: "json",
            url: bookingVars.ajaxurl,
            data: {
              action: "check_time_available",
              time: shipping_time,
              method: "direct",
            },
            beforeSend: function () {
              $bookLoading.show();
            },
            success: function (data) {
              $bookLoading.hide();
              if (data.check == false) {
                $Notify
                  .removeClass("dsmart-danger")
                  .removeClass("dsmart-success");
                $Notify.show();
                $Notify.addClass("dsmart-danger");
                $Notify.text(data.message);
                $(".shipping-text").text(data.shipping);
                /*$(".tax-text7").text(data.vat7);
                            $(".tax-text19").text(data.vat19);*/
                $(".total-text").text(data.total);
                $(".cart-coupon").remove();
                $.each(data.coupon, function (key, val) {
                  var $coupon =
                    '<tr class="cart-coupon" data-type="' +
                    val.type +
                    '" data-coupon="' +
                    key +
                    '"><td><span class="remove-coupon">x</span>Rabattcode (' +
                    key +
                    ')</td><td><span class="coupon-text">' +
                    val.price +
                    "</span></td></tr>";
                  $(".order-total").before($coupon);
                });
                $(".cart-discount").addClass("hidden");
              } else {
                $(".shipping-text").text(data.shipping);
                /*$(".tax-text7").text(data.vat7);
                            $(".tax-text19").text(data.vat19);*/
                $(".total-text").text(data.total);
                $(".cart-coupon").remove();
                $.each(data.coupon, function (key, val) {
                  var $coupon =
                    '<tr class="cart-coupon" data-type="' +
                    val.type +
                    '" data-coupon="' +
                    key +
                    '"><td><span class="remove-coupon">x</span>Rabattcode (' +
                    key +
                    ')</td><td><span class="coupon-text">' +
                    val.price +
                    "</span></td></tr>";
                  $(".order-total").before($coupon);
                });
                if ($(".cart-discount .percent").length > 0) {
                  $(".cart-discount .percent").text(
                    " (" + data.reduce_percent + ")"
                  );
                  $(".cart-discount .number").text("- " + data.reduce);
                } else {
                  $(".cart-shipping").after(
                    '<tr class="cart-discount hidden"><td>Rabatt <span class="percent">' +
                      data.reduce_percent +
                      '</span></td><td class="number">- ' +
                      data.reduce +
                      "</td></tr>"
                  );
                }
                if (data.has_reduce === true) {
                  $(".cart-discount").removeClass("hidden");
                } else {
                  $(".cart-discount").addClass("hidden");
                }
              }
            },
          });
        }
      } else {
        /*$Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
            $Notify.show();
            $Notify.addClass("dsmart-danger");
            $Notify.text("Bitte vervollständigen Sie alle benötigten Informationen");
            show_alert('Bitte vervollständigen Sie alle benötigten Informationen');*/
      }
    }
  );
  $("button[name=caculate-now]").on("click", function () {
    let shipping_method = $(
      ".dsmart-cart-total select[name=choose-shipping]"
    ).val();
    let user_location = $(".dsmart-cart-total input[name=user-location]").val();
    let latitude = $("input[name=latitude]").val();
    let longitude = $("input[name=longitude]").val();
    let shipping_time = $("input[name=shipping_time]").val();
    let delivery_time = $("input[name=delivery_time]").val();
    let delivery_date = $("input[name=delivery_date]").val();
    let filled_zipcode = $("input[name=zipcode-location]").val();
    if (shipping_method != "") {
      if (shipping_method == "shipping") {
        if (user_location != "" && latitude != "" && longitude != "") {
          $Notify.hide();
          $.ajax({
            type: "POST",
            dataType: "json",
            url: bookingVars.ajaxurl,
            data: {
              action: "get_shipping_fee",
              shipping_method: shipping_method,
              user_location: user_location,
              latitude: latitude,
              longitude: longitude,
              delivery_time: delivery_time,
              delivery_date: delivery_date,
              filled_zipcode: filled_zipcode,
            },
            beforeSend: function () {
              $bookLoading.show();
            },
            success: function (data) {
              $bookLoading.hide();
              if (data.check == false) {
                $Notify
                  .removeClass("dsmart-danger")
                  .removeClass("dsmart-success");
                $Notify.show();
                $Notify.addClass("dsmart-danger");
                $Notify.text(data.message);
                show_alert(data.message);
              } else {
                $(".shipping-text").text(data.shipping);
                /*$(".tax-text7").text(data.vat7);
                                $(".tax-text19").text(data.vat19);*/
                $(".total-text").text(data.total);
                $(".cart-coupon").remove();
                $.each(data.coupon, function (key, val) {
                  var $coupon =
                    '<tr class="cart-coupon" data-type="' +
                    val.type +
                    '" data-coupon="' +
                    key +
                    '"><td><span class="remove-coupon">x</span>Rabattcode (' +
                    key +
                    ')</td><td><span class="coupon-text">' +
                    val.price +
                    "</span></td></tr>";
                  $(".order-total").before($coupon);
                });
                $(".cart-discount .percent").text(
                  " (" + data.reduce_percent + ")"
                );
                $(".cart-discount .number").text("- " + data.reduce);
                if (data.has_reduce === true) {
                  $(".cart-discount").removeClass("hidden");
                } else {
                  $(".cart-discount").addClass("hidden");
                }
              }
            },
          });
        } else if (user_location != "" && latitude == "" && longitude == "") {
          $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
          $Notify.show();
          $Notify.addClass("dsmart-danger");
          $Notify.text("Die Adresse existiert nicht in Google Maps");
          show_alert("Die Adresse existiert nicht in Google Maps");
        } else {
          $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
          $Notify.show();
          $Notify.addClass("dsmart-danger");
          $Notify.text("Die Adresse existiert nicht in Google Maps");
          show_alert("Die Adresse existiert nicht in Google Maps");
        }
      } else {
        $Notify.hide();
        $.ajax({
          type: "POST",
          dataType: "json",
          url: bookingVars.ajaxurl,
          data: {
            action: "get_shipping_fee",
            shipping_method: shipping_method,
            shipping_time: shipping_time,
          },
          beforeSend: function () {
            $bookLoading.show();
          },
          success: function (data) {
            $bookLoading.hide();
            if (data.check == false) {
              $Notify
                .removeClass("dsmart-danger")
                .removeClass("dsmart-success");
              $Notify.show();
              $Notify.addClass("dsmart-danger");
              $Notify.text(data.message);
              show_alert(data.message);
            } else {
              // $(".shipping-text").text(data.shipping);
              // $(".tax-text7").text(data.vat7);
              // $(".tax-text19").text(data.vat19);
              $(".total-text").text(data.total);
              $(".cart-coupon").remove();
              $.each(data.coupon, function (key, val) {
                var $coupon =
                  '<tr class="cart-coupon" data-type="' +
                  val.type +
                  '" data-coupon="' +
                  key +
                  '"><td><span class="remove-coupon">x</span>Rabattcode (' +
                  key +
                  ')</td><td><span class="coupon-text">' +
                  val.price +
                  "</span></td></tr>";
                $(".order-total").before($coupon);
              });
              $(".cart-discount .percent").text(
                " (" + data.reduce_percent + ")"
              );
              $(".cart-discount .number").text("- " + data.reduce);
              if (data.has_reduce === true) {
                $(".cart-discount").removeClass("hidden");
              } else {
                $(".cart-discount").addClass("hidden");
              }
            }
          },
        });
      }
    } else {
      $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
      $Notify.show();
      $Notify.addClass("dsmart-danger");
      $Notify.text("Bitte vervollständigen Sie alle benötigten Informationen");
      show_alert('Bitte vervollständigen Sie alle benötigten Informationen');
    }
  });
  //caculate distance from customer to shop
  var rad = function (x) {
    return (x * Math.PI) / 180;
  };
  var getDistance = function (p1, p2) {
    var R = 6378137;
    var dLat = rad(p2.lat() - p1.lat());
    var dLong = rad(p2.lng() - p1.lng());
    var a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(rad(p1.lat())) *
        Math.cos(rad(p2.lat())) *
        Math.sin(dLong / 2) *
        Math.sin(dLong / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c;
    return d;
  };
  //change when change choose shop
  $(".shipping_caculate select[name=choose-shipping]").on(
    "change",
    function () {
      var value = $(this).val();
      if (value == "shipping") {
        $(".shipping_caculate .get-current-location").show();
        $(".shipping_caculate .get-current-time").hide();
      } else if (value == "direct") {
        $(".shipping_caculate .get-current-location").hide();
        $(".shipping_caculate .get-current-time").show();
      } else {
        $(".shipping_caculate .get-current-location").hide();
        $(".shipping_caculate .get-current-time").hide();
      }
    }
  );
  $("button[name=update_cart]").on("click", function (e) {
    e.preventDefault();
    var array = {};
    $("#dsmart-cart-table tbody tr").each(function () {
      var id = $(this).attr("data-id");
      var quantity = parseInt($(this).find("input[name=quantity]").val());
      if (quantity < 1) {
        quantity = 1;
      }
      if (id != "") {
        array[id] = quantity;
      }
    });
    $.ajax({
      type: "POST",
      url: bookingVars.ajaxurl,
      data: {
        action: "dsmart_update_cart",
        items: array,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        window.location.reload();
      },
    });
  });
  $(".dsmart-remove-item").on("click", function () {
    var id = $(this).parents("tr").attr("data-id");
    if (id != "") {
      $.ajax({
        type: "POST",
        url: bookingVars.ajaxurl,
        data: {
          action: "dsmart_delete_item_cart",
          id: id,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          window.location.reload();
        },
      });
    }
  });
  /*$("button[name=dsmart_apply_coupon]").on("click",function(){
        var id = $("#coupon_code").val();
        if(id == ""){
            $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
            $Notify.show();
            $Notify.addClass("dsmart-danger");
            $Notify.text("Please enter coupon");
        }else{
            $.ajax({
                type: 'POST',  
                url: bookingVars.ajaxurl,
                data:{
                    'action':'dsmart_add_coupon_in_cart',
                    'id' : id,
                },
                beforeSend:function(){
                    $bookLoading.show();
                },
                success:function(data){
                    $bookLoading.hide();
                    window.location.reload();
                }
            });
        }
    });*/
  $(".dsmart-coupon form").on("submit", function (e) {
    e.preventDefault();
    $Notify.hide();
    var id = $("#coupon_code").val();
    if (id == "") {
      $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
      $Notify.show();
      $Notify.addClass("dsmart-danger");
      $Notify.text("Please enter coupon");
    } else {
      let shipping_method = $(
        ".dsmart-cart-total select[name=choose-shipping]"
      ).val();
      let user_location = $(
        ".dsmart-cart-total input[name=user-location]"
      ).val();
      let latitude = $("input[name=latitude]").val();
      let longitude = $("input[name=longitude]").val();
      let delivery_time = $("input[name=delivery_time]").val();
      let delivery_date = $("input[name=delivery_date]").val();
      let shipping_time = $("input[name=shipping_time]").val();
      $.ajax({
        type: "POST",
        url: bookingVars.ajaxurl,
        dataType: "json",
        data: {
          action: "dsmart_add_coupon_in_cart",
          id: id,
          shipping_method: shipping_method,
          user_location: user_location,
          latitude: latitude,
          longitude: longitude,
          shipping_time: shipping_time,
          delivery_date: delivery_date,
          delivery_time: delivery_time,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          $Notify.show().html(data.message);
          if (typeof data.message2 !== "undefined") {
            show_alert(data.message2);
          }
          $(".cart_totals .dsmart-table").html(data.html);
          //window.location.reload();
        },
      });
    }
  });
  $body.on("click", ".remove-coupon", function (e) {
    e.preventDefault();
    var coupon = $(this).parents("tr").attr("data-coupon");
    let shipping_method = $(
      ".dsmart-cart-total select[name=choose-shipping]"
    ).val();
    let user_location = $(".dsmart-cart-total input[name=user-location]").val();
    let latitude = $("input[name=latitude]").val();
    let longitude = $("input[name=longitude]").val();
    let delivery_time = $("input[name=delivery_time]").val();
    let delivery_date = $("input[name=delivery_date]").val();
    let shipping_time = $("input[name=shipping_time]").val();
    if (coupon != "") {
      $.ajax({
        type: "POST",
        url: bookingVars.ajaxurl,
        dataType: "json",
        data: {
          action: "dsmart_remove_coupon_in_cart",
          coupon: coupon,
          shipping_method: shipping_method,
          user_location: user_location,
          latitude: latitude,
          longitude: longitude,
          shipping_time: shipping_time,
          delivery_date: delivery_date,
          delivery_time: delivery_time,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          $Notify.show().html(data.message);
          if (typeof data.message2 !== "undefined") {
            show_alert(data.message2);
          }
          $(".cart_totals .dsmart-table").html(data.html);
        },
      });
    }
  });
  $("button[name=checkout-now]").on("click", function () {
    var array = {};
    $("#dsmart-cart-table tbody tr").each(function () {
      var id = $(this).attr("data-id");
      var quantity = parseInt($(this).find("input[name=quantity]").val());
      if (quantity < 1) {
        quantity = 1;
      }
      if (id != "") {
        array[id] = quantity;
      }
    });
    $.ajax({
      type: "POST",
      url: bookingVars.ajaxurl,
      data: {
        action: "dsmart_update_cart",
        items: array,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        // window.location.reload();
        var shipping_method = $(
          ".dsmart-cart-total select[name=choose-shipping]"
        ).val();
        var user_location = $(".dsmart-cart-total input[name=user-location]").val();
        var latitude = $("input[name=latitude]").val();
        var longitude = $("input[name=longitude]").val();
        var shipping_time = $("input[name=shipping_time]").val();
        var delivery_time = $("input[name=delivery_time]").val();
        var delivery_date = $("input[name=delivery_date]").val();
        let filled_zipcode = $("input[name=zipcode-location]").val();
        if (shipping_method != "") {
          if (shipping_method == "shipping") {
            if (user_location != "" && latitude != "" && longitude != "") {
              $Notify.hide();
              $.ajax({
                type: "POST",
                dataType: "json",
                url: bookingVars.ajaxurl,
                data: {
                  action: "check_can_shipping_or_not",
                  shipping_method: shipping_method,
                  user_location: user_location,
                  latitude: latitude,
                  longitude: longitude,
                  shipping_time: shipping_time,
                  delivery_time: delivery_time,
                  delivery_date: delivery_date,
                  filled_zipcode: filled_zipcode,
                },
                beforeSend: function () {
                  $bookLoading.show();
                },
                success: function (data) {
                  if (data.check == false) {
                    $bookLoading.hide();
                    $Notify
                      .removeClass("dsmart-danger")
                      .removeClass("dsmart-success");
                    $Notify.show();
                    $Notify.addClass("dsmart-danger");
                    $Notify.text(data.message);
                    show_alert(data.message);
                  } else {
                    setTimeout(function () {
                      $bookLoading.hide();
                      window.location.href = data.redirect_url;
                    }, 2000);
                  }
                },
              });
            } else if (user_location != "" && latitude == "" && longitude == "") {
              $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
              $Notify.show();
              $Notify.addClass("dsmart-danger");
              $Notify.text("Die Adresse existiert nicht in Google Maps");
              show_alert("Die Adresse existiert nicht in Google Maps");
            } else {
              $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
              $Notify.show();
              $Notify.addClass("dsmart-danger");
              $Notify.text("Bitte geben Sie Ihre Adresse ein.");
              show_alert("Bitte geben Sie Ihre Adresse ein.");
            }
          } else {
            $.ajax({
              type: "POST",
              dataType: "json",
              url: bookingVars.ajaxurl,
              data: {
                action: "check_can_shipping_or_not",
                shipping_method: shipping_method,
                user_location: user_location,
                latitude: latitude,
                longitude: longitude,
                shipping_time: shipping_time,
                delivery_date: delivery_date,
                filled_zipcode: filled_zipcode,
              },
              beforeSend: function () {
                $bookLoading.show();
              },
              success: function (data) {
                if (data.check == false) {
                  $bookLoading.hide();
                  $Notify
                    .removeClass("dsmart-danger")
                    .removeClass("dsmart-success");
                  $Notify.show();
                  $Notify.addClass("dsmart-danger");
                  $Notify.text(data.message);
                } else {
                  setTimeout(function () {
                    $bookLoading.hide();
                    window.location.href = data.redirect_url;
                  }, 2000);
                }
              },
            });
          }
        } else {
          $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
          $Notify.show();
          $Notify.addClass("dsmart-danger");
          $Notify.text("Bitte vervollständigen Sie alle benötigten Informationen.");
          show_alert("Bitte vervollständigen Sie alle benötigten Informationen");
          //tempAlert('Bitte vervollständigen Sie alle benötigten Informationen',bookingVars.popup_time);
        }
      },
    });
  });
  function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test($email);
  }
  $(".select-wrap .timepicker").timepicker({
    timeFormat: "H:i",
    useSelect: true,
    disableTimeRanges: bookingVars.close_time,
    step: parseInt(bookingVars.takeaway_step, 10),
  });
  $(".select-wrap .timepicker2").timepicker({
    timeFormat: "H:i",
    useSelect: true,
    disableTimeRanges: bookingVars.close_time2,
    step: parseInt(bookingVars.delivery_step, 10),
  });
  if ($(".select-wrap .timepicker2").size() > 0) {
    if (bookingVars.buynow == "1") {
      setTimeout(function () {
        $(".select-wrap .timepicker2")
          .siblings(".ui-timepicker-select")
          .prepend(
            '<option value="So schnell wie möglich">So schnell wie möglich</option>'
          )
          .val("So schnell wie möglich")
          .trigger("change");
      }, 100);
    }
  }
  $(".select-wrap .ds-datepicker").on("change", function () {
    var jsDate = $(this).datepicker("getDate");
    var weekend_long = $.datepicker.formatDate("DD", jsDate);
    var weekend = weekend_long.substr(0, 2).toLowerCase();
    var today = new Date();
    today.setHours(0);
    today.setMinutes(0);
    today.setSeconds(0);
    var value = $(this).val();
    if (Date.parse(today) == Date.parse(jsDate)) {
      $(".select-wrap .timepicker2").timepicker({
        timeFormat: "H:i",
        useSelect: true,
        disableTimeRanges: bookingVars.close_time2,
        step: parseInt(bookingVars.delivery_step, 10),
      });
    } else {
      $(".select-wrap .timepicker2").timepicker({
        timeFormat: "H:i",
        useSelect: true,
        disableTimeRanges: bookingVars.close_all[value],
        step: parseInt(bookingVars.delivery_step, 10),
      });
    }
    //var time_value = $("select[name=ui-timepicker-delivery_time] option:not([disabled]):first").attr("value");
    //$("select[name=ui-timepicker-delivery_time]").val(time_value).trigger("change");
    if ($(".select-wrap .timepicker2").size() > 0) {
      if (bookingVars.buynow == "1") {
        setTimeout(function () {
          $(".select-wrap .timepicker2")
            .siblings(".ui-timepicker-select")
            .prepend(
              '<option value="So schnell wie möglich">So schnell wie möglich</option>'
            )
            .val("So schnell wie möglich")
            .trigger("change");
        }, 100);
      }
    }
  });
  $(".dsmart-setting-page .timepicker").timepicker({
    timeFormat: "H:i",
    useSelect: true,
  });
  $(".dsmart-setting-page .timepicker-input").timepicker({ timeFormat: "H:i" });

  $(".table-custom-date,.table-custom-time").on(
    "click",
    ".remove-row",
    function () {
      $(this).parents("tr").remove();
    }
  );
  $(".dsmart-setting-page .dsmart-add-new-row").on("click", function () {
    var row = $(this)
      .parent()
      .find(".table-custom-date tbody tr.hidden-field")
      .clone(true);
    row.removeClass("hidden-field");
    row.find("input").prop("required", true).attr("id", "");
    row.insertAfter($(".table-custom-date").find("tbody tr:last"));
    row.find("input.dsmart-datepicker").datepicker("destroy");
    row
      .find("input.dsmart-datepicker")
      .removeClass("hasDatepicker")
      .removeData("datepicker")
      .unbind()
      .datepicker({
        changeYear: true,
        dateFormat: "dd-mm-yy",
      });
    let id = row.find("input.dsmart-datepicker").attr("id");
    row.attr("id", "tr" + id);
  });
  $(".dsmart-setting-page .dsmart-add-new-row-time").on("click", function () {
    var parent = $(this).parents(".form-group").find(".table-custom-time");
    var row = parent.find("tbody tr.hidden-field").clone(true);
    row.removeClass("hidden-field");
    row.find("input").prop("required", true).attr("id", "");
    row.insertAfter(parent.find("tbody tr:last"));
    row.find("input.dsmart-datepicker").datepicker("destroy");
    row
      .find("input.dsmart-datepicker")
      .removeClass("hasDatepicker")
      .removeData("datepicker")
      .unbind()
      .datepicker({
        changeYear: true,
        dateFormat: "dd-mm-yy",
      });
  });

  function checkEmptyField(text)
  {
    $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
    $Notify.show();
    $Notify.addClass("dsmart-danger");
    $Notify.text("Bitte vervollständigen Sie alle benötigten Informationen: " + text);
    show_alert("Bitte vervollständigen Sie alle benötigten Informationen: " + text);
    $("html, body").animate(
      {
        scrollTop: $Notify.offset().top - 30,
      },
      300
    );
  }

  $("button[name=place-order]").on("click", function () {
    var customer_name1 = $("input[name=customer_name1]").val();
    var customer_name2 = $("input[name=customer_name2]").val();
    var customer_phone = $("input[name=customer_phone]").val();
    var customer_email = $("input[name=customer_email]").val();
    var more_additional = $("textarea[name=more_additional]").val();
    var customer_etage = $("input[name=customer_etage]").val();
    var customer_address = $("input[name=customer_address]").val();
    var customer_zipcode = $("input[name=customer_zipcode]").val();
    var method = $("input[name=dsmart-method]:checked").val();
    if ($("input[name=dsmart-bab]").is(":checked")) {
      var bab = 1;
    } else {
      var bab = 0;
    }
    if ($("input[name=dsmart-ar]").is(":checked")) {
      var ar = 1;
      var r_prefix = $("select[name=r_prefix]").val();
      var r_first_name = $("input[name=r_first_name]").val();
      var r_last_name = $("input[name=r_last_name]").val();
      var r_company = $("input[name=r_company]").val();
      var r_zipcode = $("input[name=r_zipcode]").val();
      var r_city = $("input[name=r_city]").val();
      var r_street = $("input[name=r_street]").val();
      var data_ar = {
        ar: ar,
        r_prefix: r_prefix,
        r_first_name: r_first_name,
        r_last_name: r_last_name,
        r_company: r_company,
        r_zipcode: r_zipcode,
        r_city: r_city,
        r_street: r_street,
      };
    } else {
      var ar = 0;
      var data_ar = {
        ar: ar,
        r_prefix: "",
        r_first_name: "",
        r_last_name: "",
        r_company: "",
        r_zipcode: "",
        r_city: "",
        r_street: "",
      };
    }
    if (typeof method === "undefined" || method == "") {
      $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
      $Notify.show();
      $Notify.addClass("dsmart-danger");
      $Notify.text("Bitte wählen Sie eine Zahlungsmethode.");
      $("html, body").animate(
        {
          scrollTop: $Notify.offset().top - 30,
        },
        300
      );
    } 
    else if(customer_name1 == "")
    {
      checkEmptyField("Nachname");
    }
    else if(customer_name2 == "")
    {
      checkEmptyField("Vorname");
    }
    else if(customer_phone == "")
    {
      checkEmptyField("Telefonnummer");
    }
    else if(customer_email == "")
    {
      checkEmptyField("Email Adresse");
    }
    else if(customer_etage == "")
    {
      checkEmptyField("Etage");
    }
    else if(customer_zipcode == "")
    {
      checkEmptyField("Postleitzahl");
    }
    else if(customer_address == "")
    {
      checkEmptyField("Lieferung");
    }
    else if (customer_email != "" && !validateEmail(customer_email)) {
      $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
      $Notify.show();
      $Notify.addClass("dsmart-danger");
      $Notify.text("Bitte füllen Sie das korrekte E-Mail-Format aus.");
      show_alert("Bitte füllen Sie das korrekte E-Mail-Format aus.");
      $("html, body").animate(
        {
          scrollTop: $Notify.offset().top - 30,
        },
        300
      );
    } else if (
      ar == 1 &&
      (r_prefix == "" ||
        r_first_name == "" ||
        r_last_name == "" ||
        r_company == "" ||
        r_zipcode == "" ||
        r_city == "" ||
        r_street == "")
    ) {
      $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
      $Notify.show();
      $Notify.addClass("dsmart-danger");
      $Notify.text("Bitte vervollständigen Sie alle benötigten Informationen.");
      show_alert("Bitte vervollständigen Sie alle benötigten Informationen.");
      $("html, body").animate(
        {
          scrollTop: $Notify.offset().top - 30,
        },
        300
      );
    } else if ($("input[name=dsmart-term-condition]").is(":checked") == false) {
      $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
      $Notify.show();
      $Notify.addClass("dsmart-danger");
      $Notify.text(
        "Bitte akzeptieren Sie unsere Allgemeinen Geschäftsbedingungen."
      );
      show_alert(
        "Bitte akzeptieren Sie unsere Allgemeinen Geschäftsbedingungen."
      );
      $("html, body").animate(
        {
          scrollTop: $Notify.offset().top - 30,
        },
        300
      );
    } else {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "checkout_cart",
          customer_name1: customer_name1,
          customer_name2: customer_name2,
          customer_phone: customer_phone,
          customer_email: customer_email,
          more_additional: more_additional,
          customer_etage: customer_etage,
          customer_zipcode: customer_zipcode,
          customer_address: customer_address,
          method: method,
          bab: bab,
          ar: JSON.stringify(data_ar),
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == false) {
            $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
            $Notify.show();
            $Notify.addClass("dsmart-danger");
            $Notify.text(data.message);
            show_alert(data.message);
          } else {
            window.location.href = data.redirect_url;
          }
        },
      });
    }
  });
  // $(".get-current-time .ui-timepicker-select option").on("click",function(){
  //     alert('a');
  // }
  $(".get-current-time .ui-timepicker-select").on("change", function () {
    var time = $(this).val();
    $("input[name=shipping_time]").val(time);
    $Notify.hide();
    if (time != "") {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "check_time_available",
          time: time,
          method: "direct",
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == false) {
            $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
            $Notify.show();
            $Notify.addClass("dsmart-danger");
            $Notify.text(data.message);
            show_alert(data.message);
          } else {
            $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
            $Notify.hide();
          }
          $(".shipping-text").text(data.shipping);
          /*$(".tax-text7").text(data.vat7);
                    $(".tax-text19").text(data.vat19);*/
          $(".total-text").text(data.total);
          $(".cart-coupon").remove();
          $.each(data.coupon, function (key, val) {
            var $coupon =
              '<tr class="cart-coupon" data-type="' +
              val.type +
              '" data-coupon="' +
              key +
              '"><td><span class="remove-coupon">x</span>Rabattcode (' +
              key +
              ')</td><td><span class="coupon-text">' +
              val.price +
              "</span></td></tr>";
            $(".order-total").before($coupon);
          });
          $(".cart-discount .percent").text(" (" + data.reduce_percent + ")");
          $(".cart-discount .number").text("- " + data.reduce);
          if (data.has_reduce === true) {
            $(".cart-discount").removeClass("hidden");
          } else {
            $(".cart-discount").addClass("hidden");
          }
        },
      });
    }
  });
  // $("input[name=shipping_time]").on("change",function(){
  //     var time = $(this).val();
  //     $Notify.hide();
  //     if(time != ""){
  //         $.ajax({
  //             type: 'POST',
  //             dataType: 'json',
  //             url: bookingVars.ajaxurl,
  //             data:{
  //                 'action':'check_time_available',
  //                 'time' : time,
  //             },
  //             beforeSend:function(){
  //                 $bookLoading.show();
  //             },
  //             success:function(data){
  //                 $bookLoading.hide();
  //                 if(data.check == false){
  //                     $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
  //                     $Notify.show();
  //                     $Notify.addClass("dsmart-danger");
  //                     $Notify.text(data.message);
  //                 }else{
  //                     $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
  //                     $Notify.hide();
  //                 }
  //                 $(".total-text").text(data.total);
  //                 $(".coupon-text").text(data.coupon);
  //                 $(".cart-discount .percent").text(' ('+data.reduce_percent+')');
  //                 $(".cart-discount .number").text('- '+data.reduce);
  //                 if(data.has_reduce === true){
  //                     $(".cart-discount").removeClass("hidden");
  //                 }else{
  //                     $(".cart-discount").addClass("hidden");
  //                 }
  //             }
  //         });
  //     }
  // });
  //get current location
  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    } else {
      show_alert("Geolocation is not supported by this browser.");
    }
  }
  function showPosition(position) {
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;
    $.ajax({
      type: "POST",
      dataType: "json",
      url: bookingVars.ajaxurl,
      data: {
        action: "get_api_google_maps",
        latitude: lat,
        longtitude: lng,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == false) {
          $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
          $Notify.show();
          $Notify.addClass("dsmart-danger");
          $Notify.text(data.message);
          show_alert(data.message);
        } else {
          $Notify.removeClass("dsmart-danger").removeClass("dsmart-success");
          $Notify.hide();
          $(".list-suggestion-address").show();
          $(".list-suggestion-address").html(data.result);
        }
      },
    });
  }
  $("#current-location").on("click", function () {
    getLocation();
  });
  $body.on("click", ".list-suggestion-address li", function () {
    var lat = $(this).attr("data-lat");
    var lng = $(this).attr("data-lng");
    var address = $(this).attr("data-address");
    if (lat != "" && lng != "" && address != "") {
      $("input[name=latitude]").val(lat);
      $("input[name=longitude]").val(lng);
      $("input[name=user-location]").val(address);
    }
  });
  $("#rating-star").barrating({
    theme: "fontawesome-stars",
  });
  $("#comment-form").on("submit", function (e) {
    var rating = $("select[name=rating-star]").val();
    var comment = $("textarea[name=comment-text]").val();
    if (rating == "" || comment == "") {
      e.preventDefault();
      $(".comment-notify")
        .removeClass("dsmart-danger")
        .removeClass("dsmart-success");
      $(".comment-notify").show();
      $(".comment-notify").addClass("dsmart-danger");
      $(".comment-notify").text(
        "Bitte vervollständigen Sie alle benötigten Informationen."
      );
    }
  });
  //show modal
  $(".cancel-order").on("click", function () {
    var value = $(this).attr("data-item");
    $("#cancel-order input[name=order-id]").val(value);
    $("#cancel-order").modal("show");
  });
  $(".complete-order").on("click", function () {
    var value = $(this).attr("data-item");
    $("#complete-order input[name=order-id]").val(value);
    $("#complete-order").modal("show");
  });
  //click button in modal
  /*$(".action-order .complete-order").on("click",function(){
        var value = $(this).attr("data-item");
        if(value != ""){
            $.ajax({
                type: 'POST',
                dataType: 'json',       
                url: bookingVars.ajaxurl,
                data:{
                    'action':'ajax_complete_order',
                    'id' : value,
                },
                beforeSend:function(){
                    $bookLoading.show();
                },
                success:function(data){
                    $bookLoading.hide();
                    if(data.check == false){
                        $(".notify").show();
                        $(".notify").text(data.message);
                    }else{
                        window.location.reload();
                    }
                }
            });
        }else{
            $("#complete-order .dsmart-modal-notify").show();
            $("#complete-order .dsmart-modal-notify").text("An error occurred. Please try again.");
        }
    });*/
  $("#cancel-order .cancel-button").on("click", function () {
    var value = $("#cancel-order input[name=order-id]").val();
    if (value != "") {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "ajax_cancel_order",
          id: value,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == false) {
            $("#cancel-order .dsmart-modal-notify").show();
            $("#cancel-order .dsmart-modal-notify").text(data.message);
          } else {
            window.location.reload();
          }
        },
      });
    } else {
      $("#cancel-order .dsmart-modal-notify").show();
      $("#cancel-order .dsmart-modal-notify").text("Ein Fehler ist aufgetreten. Bitte versuche es erneut.");
    }
  });
  $("#complete-order .complete-button").on("click", function () {
    var value = $("#complete-order input[name=order-id]").val();
    if (value != "") {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "ajax_complete_order",
          id: value,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == false) {
            $("#complete-order .dsmart-modal-notify").show();
            $("#complete-order .dsmart-modal-notify").text(data.message);
          } else {
            window.location.reload();
          }
        },
      });
    } else {
      $("#complete-order .dsmart-modal-notify").show();
      $("#complete-order .dsmart-modal-notify").text("Ein Fehler ist aufgetreten. Bitte versuche es erneut.");
    }
  });
  $(".list-product  button.add-to-cart").on("click", function (e) {
    e.preventDefault();
    let id = $(this).attr("data-id");
    let total_quantity = 1;
    let extra_info = null;
    let sidedish_info = null;
    let variable_id = null;
    if ($(this).parents(".item").find(".choose-variable").length > 0) {
      variable_id = $(this)
        .parents(".choose-variable")
        .find("option:selected")
        .attr("data-id");
      total_quantity = parseInt(
        $(this).parents("form").find(".total-quantity input").val()
      );
    } else {
      total_quantity = 1;
    }
    if ($(this).parents(".item").find(".extra-product").length > 0) {
      extra_info = $(this)
        .parents(".item")
        .find(".extra-product input[name='extra_info']")
        .val();
    }
    if ($(this).parents(".item").find(".sidedish-product").length > 0) {
      sidedish_info = $(this)
        .parents(".item")
        .find(".sidedish-product input[name='sidedish_info']")
        .val();
    }
    if(total_quantity < 1) total_quantity = 1;
    if (id != "") {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "ajax_add_to_cart",
          id: id,
          total_quantity: (total_quantity < 1) ? 1 : total_quantity,
          variable_id: variable_id,
          extra_info: extra_info,
          sidedish_info: sidedish_info,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          console.log(data);
          $bookLoading.hide();
          if (data.check == true) {
            $Notify.show();
            $Notify.text(data.message);
            show_alert(data.message);
          } else {
            $Notify.show();
            $Notify.addClass("dsmart-success");
            $Notify.html(data.message);
            $(".dsmart-float-cart .cart-info span").text(data.total);
            $(".dsmart-float-cart .total-price").text(data.price);
            if (!$(".dsmart-float-cart").hasClass("active")) {
              $(".dsmart-float-cart").addClass("active");
            }
          }
        },
      });
    }
    return false;
  });
  $("#list-order-user .print-order").on("click", function () {
    var id = $(this).parents("tr").attr("data-item");
    if (id != "") {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "ajax_print_order",
          id: id,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == false) {
            $Notify.show();
            $Notify.text(data.message);
          } else {
            window.open(data.url, "_blank");
          }
        },
      });
    }
  });
  $(".single-orders .print-order").on("click", function () {
    var id = $(this).attr("data-item");
    if (id != "") {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "ajax_print_order",
          id: id,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == false) {
            $Notify.show();
            $Notify.text(data.message);
          } else {
            window.open(data.url, "_blank");
          }
        },
      });
    }
  });
  if ($(".single-product .list-img").length > 0) {
    $(".main-img").slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      fade: true,
      asNavFor: ".list-img",
    });
    $(".list-img").slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      asNavFor: ".main-img",
      dots: true,
      centerMode: true,
      focusOnSelect: true,
      dots: false,
    });
  }
  $(".dsmart-datepicker").datepicker({
    changeYear: true,
    dateFormat: "dd-mm-yy",
  });
  /*$(".order-title .change-order>a").on("click",function(e){
        e.preventDefault();
        var value = $(this).parents("tr").attr("data-item");
        var url = $(this).attr("href");
        if(value != ""){
            $.ajax({
                type: 'POST',
                dataType: 'json',       
                url: bookingVars.ajaxurl,
                data:{
                    'action':'ajax_complete_order',
                    'id' : value,
                },
                beforeSend:function(){
                    $bookLoading.show();
                },
                success:function(data){
                    $bookLoading.hide();
                    if(data.check == false){
                        $("#complete-order .dsmart-modal-notify").show();
                        $("#complete-order .dsmart-modal-notify").text(data.message);
                    }else{
                        window.location.href = url;
                    }
                }
            });
        }else{
            $("#complete-order .dsmart-modal-notify").show();
            $("#complete-order .dsmart-modal-notify").text("Có lỗi xảy ra. Vui lòng thử lại.");
        }
    });*/
  $(".show-hide-time .show-hide-button").on("click", function (e) {
    e.preventDefault();
    $(this).siblings(".content-time").slideToggle();
  });

  
  $(".sidedish-product input[name='sidedish_product']")
  .each(function () {
    if ($(this).is(":checked")) {
      let sidedish_product_Arr = [];
      let temp_id = $(this).attr("data-id");
      sidedish_product_Arr.push({
        sidedish_id: temp_id,
      });

      $(this)
      .parents("form")
      .find("input[name='sidedish_info']")
      .val(JSON.stringify(sidedish_product_Arr));
    }
  });

  /////////////////////////////////////
  //  PRODUCT QUANTITY
  /////////////////////////////////////
  function number_format(number, decimals, decPoint, thousandsSep) {
    // eslint-disable-line camelcase
    number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
    let n = !isFinite(+number) ? 0 : +number;
    let prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    let sep = typeof thousandsSep === "undefined" ? "," : thousandsSep;
    let dec = typeof decPoint === "undefined" ? "." : decPoint;
    let s = "";

    let toFixedFix = function (n, prec) {
      if (("" + n).indexOf("e") === -1) {
        return +(Math.round(n + "e+" + prec) + "e-" + prec);
      } else {
        let arr = ("" + n).split("e");
        let sig = "";
        if (+arr[1] + prec > 0) {
          sig = "+";
        }
        return (+(
          Math.round(+arr[0] + "e" + sig + (+arr[1] + prec)) +
          "e-" +
          prec
        )).toFixed(prec);
      }
    };
    // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec).toString() : "" + Math.round(n)).split(".");
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || "").length < prec) {
      s[1] = s[1] || "";
      s[1] += new Array(prec - s[1].length + 1).join("0");
    }
    return s.join(dec);
  }

  function ds_price_format_text(price) {
    let currency = bookingVars.currency;
    price = ds_convert_price(price);
   
    price = currency + " " + number_format(price, 2, ".", " ");
    return price;
  }

  function ds_convert_price(price) {
    let currency = bookingVars.currency;
    dsmart_currency_rate = bookingVars.currency_rate;
    if (dsmart_currency_rate != "") {
      currency_rate = parseFloat(dsmart_currency_rate);
    } else {
      currency_rate = 1;
    }
    if (currency == "$") {
      return parseFloat(number_format(price, 2));
    } else {
      return parseFloat(number_format(parseFloat(price) * currency_rate, 2));
    }
  }

  function updateProduct(el){
    let input_quantity = parseInt(
      $(el).parents("form").find(".total-quantity input").val()
    );
    let variable_price = parseFloat(
      $(el).parents(".choose-variable").find("option:selected").attr("data-price")
    );
    let extra_price = 0;
    let extra_product_Arr = [];
    let sidedish_price = 0;
    let sidedish_product_Arr = [];
    $(el)
      .parents("form")
      .find(".sidedish-product input[name='sidedish_product']")
      .each(function () {
        let temp_id = $(this).attr("data-id");
        
        if ($(this).is(":checked")) {
          sidedish_product_Arr.push({
            sidedish_id: temp_id,
          });
          if($(this).attr("data-price") === "") sidedish_price = 0;
          else sidedish_price = parseFloat($(this).attr("data-price"));
          console.log(sidedish_price);
        }
      });
    $(el)
      .parents("form")
      .find(".extra-product input[name='extra_product']")
      .each(function () {
        let temp_quantity = $(this).attr("data-quantity");
        let temp_price = $(this).attr("data-price");
        let temp_id = $(this).attr("data-id");
        if (
          $(this).parents(".custom-radio-checkbox").find(".extra-quantity")
            .length > 0
        ) {
          temp_quantity = $(this)
            .parents(".custom-radio-checkbox")
            .find(".extra-quantity input")
            .val();
        }
        if ($(this).is(":checked")) {
          temp_price = parseFloat(temp_price) * parseInt(temp_quantity);
          extra_price = extra_price + parseFloat(temp_price);
          extra_product_Arr.push({
            extra_id: temp_id,
            extra_quantity: temp_quantity,
          });
        }
      });
    extra_price = parseFloat(number_format(extra_price, 2));
    let total_price = 0;
    if(variable_price > 0){
      total_price = (extra_price + variable_price + sidedish_price) * input_quantity;
    }else{
      total_price = (extra_price + sidedish_price + parseFloat($(el)
      .parents("form")
      .find("input[name='origin_price']")
      .val())) * input_quantity;
    }

    $(el)
      .parents("form")
      .find("input[name='extra_info']")
      .val(JSON.stringify(extra_product_Arr));
    $(el)
      .parents("form")
      .find("input[name='sidedish_info']")
      .val(JSON.stringify(sidedish_product_Arr));
    $(el)
      .parents("form")
      .find(".add-to-cart")
      .text(ds_price_format_text(total_price));
    $(el)
      .parents(".item")
      .find(".price span")
      .text(ds_price_format_text(total_price));

  }

  $(document).on("click", ".quantity-wrap .minus", function (e) {
    e.preventDefault();
    let quantity = $(this).next().val();
    if (quantity > 1) {
      quantity = Number(quantity) - 1;
    }
    $(this).next().val(quantity);

    updateProduct(this);
    
    return false;
  });
  $(document).on("click", ".quantity-wrap .plus", function (e) {
    e.preventDefault();
    let quantity = $(this).prev().val();
    quantity = Number(quantity) + 1;
    $(this).prev().val(quantity);

    updateProduct(this);
    return false;
  });
  $(document).on("change", ".variable-select", function (e) {
    updateProduct(this);
  });

  $(document).on(
    "change",
    ".extra-product input[name='extra_product']",
    function () {
      updateProduct(this);
    }
  );
    $(document).on(
    "change",
    ".sidedish-product input[name='sidedish_product']",
    function () {
      updateProduct(this);
    }
  );
  $("#discount-group .add-time").on("click", function () {
    let id = $(this).parent().attr("id");
    $(".add-time-form .object").val(id);
    $(".add-time-form").fadeIn();
    $(".book-overlay").show();
  });
  $("#discount-group-2 .add-time-2").on("click", function () {
    let id = $(this).parent().attr("id");
    $(".add-time-form-2 .object").val(id);
    $(".add-time-form-2").fadeIn();
    $(".book-overlay").show();
  });
  $("#custom-discount-group .add-time").on("click", function () {
    let id = $(this).parents("tr").attr("id");
    $(".add-time-form .object").val(id);
    $(".add-time-form").fadeIn();
    $(".book-overlay").show();
  });
  $("#custom-discount-group-2 .add-time-2").on("click", function () {
    let id = $(this).parents("tr").attr("id");
    $(".add-time-form .object").val(id);
    $(".add-time-form-2").fadeIn();
    $(".book-overlay").show();
  });
  $(".add-time-form").on("click", ".btn-cancel", function () {
    $(".add-time-form").fadeOut();
    $(".add-time-form input").val("");
    $(".book-overlay").hide();
  });

  $(".add-time-form-2").on("click", ".btn-cancel", function () {
    $(".add-time-form-2").fadeOut();
    $(".add-time-form-2 input").val("");
    $(".book-overlay").hide();
  });

  $(".book-overlay").on("click", function () {
    $(".add-time-form,.add-time-form-2").hide();
    $(".add-time-form input,.add-time-form-2 input").val("");
    $(".book-overlay").hide();
  });

  $(".add-time-form,.add-time-form-2").on("click", ".btn-ok", function () {
    let from = $(this)
      .parent()
      .children("input[name='discount_form_mo']")
      .val();
    let to = $(this).parent().children("input[name='discount_to_mo']").val();
    let id = $(this).parent().children(".object").val();
    let discount_time = $("#" + id)
      .find("input.multi_timepicker")
      .val();
    var stt = new Date("November 13, 2013 " + from);
    stt = stt.getTime();
    var endt = new Date("November 13, 2013 " + to);
    endt = endt.getTime();
    if (from === "" || to === "") {
      $(this)
        .parent()
        .children(".message")
        .text(bookingVars.time_discount_error1)
        .removeClass("hidden");
    } else if (from === to) {
      $(this)
        .parent()
        .children(".message")
        .text(bookingVars.time_discount_error2)
        .removeClass("hidden");
    } else if (stt > endt) {
      $(this)
        .parent()
        .children(".message")
        .text(bookingVars.time_discount_error3)
        .removeClass("hidden");
    } else {
      $(this).children(".message").addClass("hidden");
      let input_val = from + "-" + to;
      if (discount_time == "") {
        $("#" + id)
          .find("input.multi_timepicker")
          .val(input_val);
      } else {
        discount_time_Arr = discount_time.split(",");
        if (jQuery.inArray(input_val, discount_time_Arr) !== -1) {
        } else {
          discount_time_Arr.push(input_val);
        }
        $("#" + id)
          .find("input.multi_timepicker")
          .val(discount_time_Arr.toString());
      }
    }
  });
  $(".dsmart-add-new-row2").on("click", function () {
    var data =
      '<tr><td><input type="text" name="dsmart_shipping_from[]" class="form-control" value="" autocomplete="off" required/></td><td><input type="text" name="dsmart_shipping_to[]" class="form-control" value="" autocomplete="off" required/></td><td><input type="text" name="dsmart_shipping_cs_fee[]" class="form-control" value="" autocomplete="off" required/></td><td><span class="btn btn-danger remove-row2"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td></tr>';
    $(".table-custom-shipping tbody").append(data);
  });
  $body.on("click", ".remove-row2", function () {
    $(this).parents("tr").remove();
  });
  $(".dsmart-add-new-row3").on("click", function () {
    var data =
      '<tr><td><input type="text" name="dsmart_custom_method[]" class="form-control" value="" autocomplete="off" required/></td><td><span class="btn btn-danger remove-row3"><i class="fa fa-trash-o" aria-hidden="true"></i></span></td></tr>';
    $(".table-custom-method tbody").append(data);
  });
  $body.on("click", ".remove-row3", function () {
    $(this).parents("tr").remove();
  });
  $(".ds-datepicker")
    .datepicker({
      dateFormat: "dd-mm-yy",
      minDate: new Date(),
      maxDate: "+7D",
    })
    .datepicker("setDate", new Date());
  $(".reset-order").on("click", function () {
    if (confirm("Do you want to reset your second order number?")) {
      $.ajax({
        type: "POST",
        url: bookingVars.ajaxurl,
        data: {
          action: "reset_your_order_number",
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          show_alert("Reset successful!");
        },
      });
    }
  });
  $body.on("click", ".dsmart-ar", function () {
    if ($(this).is(":checked")) {
      $(this).parents(".dsmart-checkbox").find(".show-data").addClass("show");
    } else {
      $(this)
        .parents(".dsmart-checkbox")
        .find(".show-data")
        .removeClass("show");
    }
  });
  /*$body.on("change",".ds-zipcode",function(){
        let zipcode = $(".ds-zipcode").val();
        if(zipcode != ""){
            $.ajax({
                type: 'POST',   
                dataType: 'json',    
                url: bookingVars.ajaxurl,
                data:{
                    'action':'get_zipcode_availabe_or_not',
                    'zipcode' : zipcode
                },
                beforeSend:function(){
                    $bookLoading.show();
                },
                success:function(data){
                    $bookLoading.hide();
                    if(data.check == true){
                        $(".ds-searchform .ds-liefern").removeClass("cs-btn-disabled");
                    }else{
                        $(".ds-searchform .ds-liefern").addClass("cs-btn-disabled");
                    }
                }
            });
        }
    });*/
  $body.on("click", ".ds-searchform .ds-liefern", function () {
    let zipcode = $(".ds-zipcode").val();
    if (!$(this).hasClass("cs-btn-disabled") && zipcode !== "") {
      $.ajax({
        type: "POST",
        dataType: "json",
        url: bookingVars.ajaxurl,
        data: {
          action: "save_zipcode_availabe_or_not",
          zipcode: zipcode,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == true) {
            setCookie("defaultShipping", "shipping", 30);
            window.location.href = data.link;
          } else {
            show_alert("Wir liefern nicht zu der gewünschten PLZ");
            //$(".ds-searchform .ds-liefern").addClass("cs-btn-disabled");
          }
        },
      });
    }
  });
  $body.on("click", ".ds-searchform .ds-abholen", function () {
    if (!$(this).hasClass("cs-btn-disabled")) {
      setCookie("defaultShipping", "direct", 30);
      window.location.href = $(this).attr("data-href");
    }
  });
  function init_change_to_default_shipping() {
    if (
      (getCookie("defaultShipping") == "shipping" ||
        getCookie("defaultShipping") == "direct") &&
      $body.hasClass("page-template-cart-page")
    ) {
      $(".shipping_caculate select[name=choose-shipping]")
        .val(getCookie("defaultShipping"))
        .change();
    }
  }
  init_change_to_default_shipping();
  //account js
  $body.on("submit", ".login-form", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let username = $(this).find("input[name=username]").val();
    let password = $(this).find("input[name=password]").val();
    let login_nonce = $(this).find("#login_nonce").val();
    if (username == "" || password == "") {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("Bitte Benutzernamen und Passwort eingeben.");
      return false;
    }
    if (login_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    $.ajax({
      type: "POST",
      dataType: "json",
      url: bookingVars.ajaxurl,
      data: {
        action: "ds_login",
        username: username,
        password: password,
        _wpnonce: login_nonce,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == true) {
          notify.addClass("alert alert-success").show().text(data.message);
          window.location.href = data.link;
        } else {
          notify.addClass("alert alert-danger").show().text(data.message);
        }
      },
    });
  });
  $body.on("submit", ".register-form", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let firstname = $(this).find("input[name=firstname]").val();
    let lastname = $(this).find("input[name=lastname]").val();
    let phone = $(this).find("input[name=phone]").val();
    let etage = $(this).find("input[name=etage]").val();
    let street = $(this).find("input[name=street]").val();
    let city = $(this).find("input[name=city]").val();
    let zipcode = $(this).find("input[name=zipcode]").val();
    let username = $(this).find("input[name=username]").val();
    let email = $(this).find("input[name=email]").val();
    let password = $(this).find("input[name=password]").val();
    let repassword = $(this).find("input[name=repassword]").val();
    let register_nonce = $(this).find("#register_nonce").val();
    if (
      email == "" ||
      firstname == "" ||
      lastname == "" ||
      phone == "" ||
      street == "" ||
      city == "" ||
      zipcode == "" ||
      username == "" ||
      password == ""
    ) {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("Bitte geben Sie die vollständigen Informationen ein.");
      return false;
    }
    if (!validateEmail(email)) {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("E-Mail-Format falsch.");
      return false;
    }
    if (password.length < 6) {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("Passwortlänge mehr als 6 Zeichen.");
      return false;
    }
    if (password != repassword) {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("Das Passwort stimmt nicht überein.");
      return false;
    }
    if (register_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    $.ajax({
      type: "POST",
      dataType: "json",
      url: bookingVars.ajaxurl,
      data: {
        action: "ds_register",
        firstname: firstname,
        lastname: lastname,
        phone: phone,
        etage: etage,
        street: street,
        city: city,
        zipcode: zipcode,
        username: username,
        email: email,
        password: password,
        _wpnonce: register_nonce,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == true) {
          notify.addClass("alert alert-success").show().text(data.message);
          window.location.href = data.link;
        } else {
          notify.addClass("alert alert-danger").show().text(data.message);
        }
      },
    });
  });
  $body.on("submit", ".lostpass-form", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let $this = $(this);
    let username = $(this).find("input[name=username]").val();
    let lostpass_nonce = $(this).find("#lostpass_nonce").val();
    $(this)
      .find(".cs-notify")
      .removeClass("alert-danger")
      .removeClass("alert-success")
      .text("");
    if (username == "") {
      notify
        .addClass("alert alert-danger")
        .show()
        .text(
          "Bitte geben Sie Ihren Benutzernamen oder Ihre E-Mail Adresse ein."
        );
      return false;
    }
    if (lostpass_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    $.ajax({
      type: "POST",
      dataType: "json",
      url: bookingVars.ajaxurl,
      data: {
        action: "ds_lostpass",
        username: username,
        _wpnonce: lostpass_nonce,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == true) {
          notify.addClass("alert alert-success").show().text(data.message);
        } else {
          notify.addClass("alert alert-danger").show().text(data.message);
        }
      },
    });
    return false;
  });
  $body.on("submit", ".reset-form", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let $this = $(this);
    let password = $(this).find("input[name=password]").val();
    let password2 = $(this).find("input[name=password2]").val();
    let key = $(this).find("input[name=key]").val();
    let login = $(this).find("input[name=login]").val();
    let resetpass_nonce = $(this).find("#resetpass_nonce").val();
    if (
      password == "" ||
      password2 == "" ||
      password.length < 6 ||
      password != password2
    ) {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("Das Passwort ist leer oder stimmt nicht überein.");
      return false;
    }
    if (resetpass_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    $.ajax({
      type: "POST",
      dataType: "json",
      url: bookingVars.ajaxurl,
      data: {
        action: "ds_resetpass",
        password: password,
        key: key,
        login: login,
        _wpnonce: resetpass_nonce,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == true) {
          notify.addClass("alert alert-success").show().text(data.message);
        } else {
          notify.addClass("alert alert-danger").show().text(data.message);
        }
      },
    });
  });
  $body.on("submit", ".profile-form", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let $this = $(this);
    let firstname = $(this).find("input[name=firstname]").val();
    let lastname = $(this).find("input[name=lastname]").val();
    let profile_nonce = $(this).find("#profile_nonce").val();
    if (firstname == "" || lastname == "") {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("Bitte geben Sie alle Informationen an.");
      return false;
    }
    if (profile_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    $.ajax({
      type: "POST",
      dataType: "json",
      url: bookingVars.ajaxurl,
      data: {
        action: "ds_update_profile",
        firstname: firstname,
        lastname: lastname,
        _wpnonce: profile_nonce,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == true) {
          notify.addClass("alert alert-success").show().text(data.message);
        } else {
          notify.addClass("alert alert-danger").show().text(data.message);
        }
      },
    });
  });
  $body.on("submit", ".password-form", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let $this = $(this);
    let password = $(this).find("input[name=new-pass]").val();
    let password2 = $(this).find("input[name=confirm-pass]").val();
    let oldpassword = $(this).find("input[name=old-pass]").val();
    let profile_nonce = $(this).find("#profile_nonce").val();
    if (
      oldpassword == "" ||
      password == "" ||
      password2 == "" ||
      password.length < 6 ||
      password != password2
    ) {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("Das Passwort ist leer oder stimmt nicht überein.");
      return false;
    }
    if (profile_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    $.ajax({
      type: "POST",
      dataType: "json",
      url: bookingVars.ajaxurl,
      data: {
        action: "ds_change_password",
        password: password,
        oldpassword: oldpassword,
        _wpnonce: profile_nonce,
      },
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == true) {
          notify.addClass("alert alert-success").show().text(data.message);
        } else {
          notify.addClass("alert alert-danger").show().text(data.message);
        }
      },
    });
  });
  $body.on("click", ".avatar-form .btn-avatar", function () {
    $(".avatar-file").trigger("click");
  });
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $(".btn-avatar img").attr("src", e.target.result).attr("srcset", "");
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  $(".avatar-form input[name=avatar]").on("change", function () {
    readURL(this);
  });
  $body.on("submit", ".avatar-form", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let form_data = new FormData(this);
    form_data.append("action", "ds_update_avatar");
    $.ajax({
      type: "POST",
      url: bookingVars.ajaxurl,
      cache: false,
      dataType: "json",
      data: form_data,
      mimeType: "multipart/form-data",
      contentType: false,
      processData: false,
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == true) {
          notify.addClass("alert alert-success").show().text(data.message);
        } else {
          notify.addClass("alert alert-danger").show().text(data.message);
        }
      },
    });
  });
  $body.on("submit", ".address-form", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    let $this = $(this);
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let customer_name1 = $(this).find("input[name=customer_name1]").val();
    let customer_name2 = $(this).find("input[name=customer_name2]").val();
    let customer_phone = $(this).find("input[name=customer_phone]").val();
    let customer_email = $(this).find("input[name=customer_email]").val();
    let customer_etage = $(this).find("input[name=customer_etage]").val();
    let customer_street = $(this).find("input[name=customer_street]").val();
    let customer_city = $(this).find("input[name=customer_city]").val();
    let customer_zipcode = $(this).find("input[name=customer_zipcode]").val();
    let profile_nonce = $(this).find("#profile_nonce").val();
    if (
      customer_name1 == "" ||
      customer_name2 == "" ||
      customer_phone == "" ||
      customer_street == "" ||
      customer_city == "" ||
      customer_zipcode == ""
    ) {
      notify
        .addClass("alert alert-danger")
        .show()
        .text("Bitte geben Sie alle Informationen an.");
      return false;
    }
    if (!validateEmail(customer_email)) {
      notify.addClass("alert alert-danger").show().text("E-Mail falsch.");
      return false;
    }
    if (profile_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    let form_data = new FormData(this);
    form_data.append("action", "ds_update_address");
    $.ajax({
      type: "POST",
      url: bookingVars.ajaxurl,
      dataType: "json",
      data: form_data,
      cache: false,
      contentType: false,
      processData: false,
      beforeSend: function () {
        $bookLoading.show();
      },
      success: function (data) {
        $bookLoading.hide();
        if (data.check == true) {
          notify.addClass("alert alert-success").show().text(data.message);
          if (address_action == "add-new")
            $this
              .find(
                "input[name=customer_name1],input[name=customer_name2],input[name=customer_phone],input[name=customer_email],input[name=customer_address]"
              )
              .val("");
        } else {
          notify.addClass("alert alert-danger").show().text(data.message);
        }
      },
    });
  });
  $body.on("click", ".set-default-address", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    let $this = $(this);
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let id = $(this).parents(".address-item").attr("data-id");
    let profile_nonce = $("#profile_nonce").val();
    if (id === "") {
      notify.addClass("alert alert-danger").show().text("Fehler.");
      return false;
    }
    if (profile_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    if (confirm("Möchten Sie diese Adresse auf Standard setzen?")) {
      $.ajax({
        type: "POST",
        url: bookingVars.ajaxurl,
        dataType: "json",
        data: {
          action: "ds_update_address",
          id: id,
          address_action: "set_default",
          profile_nonce: profile_nonce,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == true) {
            notify.addClass("alert alert-success").show().text(data.message);
            setTimeout(function () {
              window.location.reload();
            }, 5000);
          } else {
            notify.addClass("alert alert-danger").show().text(data.message);
          }
        },
      });
    }
  });
  $body.on("click", ".remove-address", function (e) {
    e.preventDefault();
    let notify = $(".notify");
    let $this = $(this);
    notify.removeClass("alert alert-danger alert-success").html("").hide();
    let id = $(this).parents(".address-item").attr("data-id");
    let profile_nonce = $("#profile_nonce").val();
    if (id === "") {
      notify.addClass("alert alert-danger").show().text("Error.");
      return false;
    }
    if (profile_nonce == "") {
      notify.addClass("alert alert-danger").show().text("Fehler-Token.");
      return false;
    }
    if (confirm("Möchten Sie diese Adresse löschen?")) {
      $.ajax({
        type: "POST",
        url: bookingVars.ajaxurl,
        dataType: "json",
        data: {
          action: "ds_update_address",
          id: id,
          address_action: "delete",
          profile_nonce: profile_nonce,
        },
        beforeSend: function () {
          $bookLoading.show();
        },
        success: function (data) {
          $bookLoading.hide();
          if (data.check == true) {
            notify.addClass("alert alert-success").show().text(data.message);
            $this.parents(".address-item").remove();
          } else {
            notify.addClass("alert alert-danger").show().text(data.message);
          }
        },
      });
    }
  });
  function change_address_checkout() {
    if ($(".ds-address").size() > 0) {
      let data = $(".ds-address").val();
      if (data !== "") {
        let array = data.split("|");
        array.forEach(function (item) {
          item = item.split(":");
          $("input[name=" + item[0] + "]:not([readonly])").val(item[1]);
        });
      }
    }
  }
  $body.on("change", ".ds-address", function () {
    change_address_checkout();
  });
  change_address_checkout();
  
});


// Add this code to your theme's JS file or inside a <script> tag

function initializeSearch() {
    const searchInput = document.getElementById('search');
    if (!searchInput) return;

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        const productItems = document.querySelectorAll('.menu-meals .item');

        // Filter products while keeping categories visible
        productItems.forEach(item => {
            const title = item.querySelector('.title').textContent.toLowerCase();
            const excerpt = item.querySelector('.excerpt')?.textContent.toLowerCase() || '';
            const isMatch = title.includes(searchTerm) || excerpt.includes(searchTerm);
            
            item.style.display = isMatch ? '' : 'none';
        });

        // Keep all category headers visible
        const categoryHeaders = document.querySelectorAll('.menu-meals h2.change_when_scroll');
        categoryHeaders.forEach(header => {
            const categorySection = header.closest('div[id^="link_term_"]');
            if (categorySection) {
                categorySection.style.display = ''; // Always show categories
            }
        });

        // Keep all menu categories visible
        const menuCategories = document.querySelectorAll('.big_menu_side, .category_single');
        menuCategories.forEach(category => {
            category.style.display = ''; // Always show menu items
        });
    });
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeSearch);


// Add this to your book-frontend.js file

function toggle_search_popup() {
    const popup = document.getElementById("search_popuptext");
    const input = document.getElementById("mobile_search");
    popup.classList.toggle("show");
    if (popup.classList.contains("show")) {
        input.focus();
    }
}

function handleSearch(value) {
    // Sync the search value with the desktop search input
    const desktopSearch = document.getElementById("search");
    if (desktopSearch) {
        desktopSearch.value = value;
        // Trigger the search event
        desktopSearch.dispatchEvent(new Event('input'));
    }
}

// Close search popup when clicking outside
document.addEventListener('click', function(event) {
    const popup = document.getElementById("search_popuptext");
    const searchBox = document.querySelector(".outer_search_box");
    
    if (popup && searchBox) {
        if (!popup.contains(event.target) && !searchBox.contains(event.target)) {
            popup.classList.remove("show");
        }
    }
});

// Initialize marquee with dynamic width calculation
function initializeMarquee() {
    const marqueeParents = document.querySelectorAll('.marquee-parent');
    
    marqueeParents.forEach(function(parent) {
        const marquee = parent.querySelector('.marquee-child');
        if (!marquee) return;
        
        // Remove any existing clones
        const existingClones = parent.querySelectorAll('.marquee-child:not(:first-child)');
        existingClones.forEach(function(clone) {
            clone.remove();
        });
        
        // Get the text content
        const text = marquee.textContent || marquee.innerText;
        if (!text.trim()) return;
        
        // Measure the actual width of the text
        const tempSpan = document.createElement('span');
        tempSpan.style.visibility = 'hidden';
        tempSpan.style.position = 'absolute';
        tempSpan.style.whiteSpace = 'nowrap';
        tempSpan.style.fontSize = window.getComputedStyle(marquee).fontSize;
        tempSpan.style.fontFamily = window.getComputedStyle(marquee).fontFamily;
        tempSpan.style.fontWeight = window.getComputedStyle(marquee).fontWeight;
        tempSpan.textContent = text;
        document.body.appendChild(tempSpan);
        const textWidth = tempSpan.offsetWidth;
        document.body.removeChild(tempSpan);
        
        // Get container width
        const containerWidth = parent.offsetWidth;
        
        // Only animate if text is wider than container
        if (textWidth > containerWidth) {
            // Reset styles for animation
            marquee.style.position = 'absolute';
            marquee.style.left = 'auto';
            marquee.style.animation = 'marquee-scroll linear infinite';
            marquee.style.textAlign = 'left';
            marquee.style.width = textWidth + 'px';
            
            // Set animation duration based on text width (adjust speed as needed)
            // Speed: pixels per second (lower = faster)
            const pixelsPerSecond = 50;
            const duration = (textWidth + containerWidth) / pixelsPerSecond;
            marquee.style.animationDuration = duration + 's';
            
            // Duplicate text for seamless loop
            const clone = marquee.cloneNode(true);
            clone.style.animationDelay = '-' + (textWidth / pixelsPerSecond) + 's';
            parent.appendChild(clone);
        } else {
            // Text fits, center it instead of animating
            marquee.style.position = 'relative';
            marquee.style.left = 'auto';
            marquee.style.animation = 'none';
            marquee.style.textAlign = 'center';
            marquee.style.width = 'auto';
            marquee.classList.add('no-animate');
        }
    });
}

// Initialize marquee when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMarquee);
} else {
    initializeMarquee();
}

// Re-initialize on window resize
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(initializeMarquee, 250);
});