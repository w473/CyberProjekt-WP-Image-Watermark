/* liteUploader v2.0.0 | https://github.com/burt202/lite-uploader | Aaron Burtnyk (http://www.burtdev.net) */
function LiteUploader(e,t){this.el=jQuery(e);this.options=t;this.params=t.params;this._init()}jQuery.fn.liteUploader=function(e){var t={script:null,rules:{allowedFileTypes:null,maxSize:null},params:{},changeHandler:true,clickElement:null};return this.each(function(){jQuery.data(this,"liteUploader",new LiteUploader(this,jQuery.extend(t,e)))})};LiteUploader.prototype={_init:function(){if(this.options.changeHandler){this.el.change(function(){this._start()}.bind(this))}if(this.options.clickElement){this.options.clickElement.click(function(){this._start()}.bind(this))}},_start:function(){var e=this.el.get(0).files;if(this._validateInput(e)){this._resetInput();return}if(this._validateFiles(e)){this._resetInput();return}this.el.trigger("lu:before",[e]);this._performUpload(this._collateFormData(e))},_resetInput:function(){this.el.val("")},_validateInput:function(e){var t=[];if(!this.el.attr("name")){t.push("the file input element must have a name attribute")}if(!this.options.script){t.push("the script option is required")}if(e.length===0){t.push("at least one file must be selected")}this.el.trigger("lu:errors",[[{name:"liteUploader_input",errors:t}]]);if(t.length>0){return true}return false},_validateFiles:function(e){var t=false,n=[];jQuery.each(e,function(r){var i=this._findErrors(e[r]);n.push({name:e[r].name,errors:i});if(i.length>0){t=true}}.bind(this));this.el.trigger("lu:errors",[n]);return t},_findErrors:function(e){var t=[];jQuery.each(this.options.rules,function(n,r){if(n==="allowedFileTypes"&&r&&jQuery.inArray(e.type,r.split(","))===-1){t.push({type:"type",rule:r,given:e.type})}if(n==="maxSize"&&r&&e.size>r){t.push({type:"size",rule:r,given:e.size})}});return t},_getFormDataObject:function(){return new FormData},_collateFormData:function(e){var t=this._getFormDataObject();if(this.el.attr("id")){t.append("liteUploader_id",this.el.attr("id"))}jQuery.each(this.params,function(e,n){t.append(e,n)});jQuery.each(e,function(n){t.append(this.el.attr("name")+"[]",e[n])}.bind(this));return t},_performUpload:function(e){jQuery.ajax({xhr:function(){var e=new XMLHttpRequest;e.upload.addEventListener("progress",function(e){if(e.lengthComputable){this.el.trigger("lu:progress",Math.floor(e.loaded/e.total*100))}}.bind(this),false);return e}.bind(this),url:this.options.script,type:"POST",data:e,processData:false,contentType:false}).done(function(e){this.el.trigger("lu:success",e);this._resetInput()}.bind(this)).fail(function(e){this.el.trigger("lu:fail",e);this._resetInput()}.bind(this))},addParam:function(e,t){this.params[e]=t}}

/*
 * jQuery MiniColors: A tiny color picker built on jQuery
 * Copyright Cory LaViska for A Beautiful Site, LLC. (http://www.abeautifulsite.net/)
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 */
jQuery&&function(a){function b(b,c){var d=a('<div class="minicolors" />'),e=a.minicolors.defaults;b.data("minicolors-initialized")||(c=a.extend(!0,{},e,c),d.addClass("minicolors-theme-"+c.theme).toggleClass("minicolors-with-opacity",c.opacity),void 0!==c.position&&a.each(c.position.split(" "),function(){d.addClass("minicolors-position-"+this)}),b.addClass("minicolors-input").data("minicolors-initialized",!1).data("minicolors-settings",c).prop("size",7).wrap(d).after('<div class="minicolors-panel minicolors-slider-'+c.control+'">'+'<div class="minicolors-slider">'+'<div class="minicolors-picker"></div>'+"</div>"+'<div class="minicolors-opacity-slider">'+'<div class="minicolors-picker"></div>'+"</div>"+'<div class="minicolors-grid">'+'<div class="minicolors-grid-inner"></div>'+'<div class="minicolors-picker"><div></div></div>'+"</div>"+"</div>"),c.inline||(b.after('<span class="minicolors-swatch"><span class="minicolors-swatch-color"></span></span>'),b.next(".minicolors-swatch").on("click",function(a){a.preventDefault(),b.focus()})),b.parent().find(".minicolors-panel").on("selectstart",function(){return!1}).end(),c.inline&&b.parent().addClass("minicolors-inline"),h(b,!1),b.data("minicolors-initialized",!0))}function c(a){var b=a.parent();a.removeData("minicolors-initialized").removeData("minicolors-settings").removeProp("size").removeClass("minicolors-input"),b.before(a).remove()}function d(a){var b=a.parent(),c=b.find(".minicolors-panel"),d=a.data("minicolors-settings");!a.data("minicolors-initialized")||a.prop("disabled")||b.hasClass("minicolors-inline")||b.hasClass("minicolors-focus")||(e(),b.addClass("minicolors-focus"),c.stop(!0,!0).fadeIn(d.showSpeed,function(){d.show&&d.show.call(a.get(0))}))}function e(){a(".minicolors-focus").each(function(){var b=a(this),c=b.find(".minicolors-input"),d=b.find(".minicolors-panel"),e=c.data("minicolors-settings");d.fadeOut(e.hideSpeed,function(){e.hide&&e.hide.call(c.get(0)),b.removeClass("minicolors-focus")})})}function f(a,b,c){var m,n,o,p,d=a.parents(".minicolors").find(".minicolors-input"),e=d.data("minicolors-settings"),f=a.find("[class$=-picker]"),h=a.offset().left,i=a.offset().top,j=Math.round(b.pageX-h),k=Math.round(b.pageY-i),l=c?e.animationSpeed:0;b.originalEvent.changedTouches&&(j=b.originalEvent.changedTouches[0].pageX-h,k=b.originalEvent.changedTouches[0].pageY-i),0>j&&(j=0),0>k&&(k=0),j>a.width()&&(j=a.width()),k>a.height()&&(k=a.height()),a.parent().is(".minicolors-slider-wheel")&&f.parent().is(".minicolors-grid")&&(m=75-j,n=75-k,o=Math.sqrt(m*m+n*n),p=Math.atan2(n,m),0>p&&(p+=2*Math.PI),o>75&&(o=75,j=75-75*Math.cos(p),k=75-75*Math.sin(p)),j=Math.round(j),k=Math.round(k)),a.is(".minicolors-grid")?f.stop(!0).animate({top:k+"px",left:j+"px"},l,e.animationEasing,function(){g(d,a)}):f.stop(!0).animate({top:k+"px"},l,e.animationEasing,function(){g(d,a)})}function g(a,b){function c(a,b){var c,d;return a.length&&b?(c=a.offset().left,d=a.offset().top,{x:c-b.offset().left+a.outerWidth()/2,y:d-b.offset().top+a.outerHeight()/2}):null}var d,e,f,g,h,j,k,m=a.val(),o=a.attr("data-opacity"),p=a.parent(),r=a.data("minicolors-settings"),s=p.find(".minicolors-swatch"),t=p.find(".minicolors-grid"),u=p.find(".minicolors-slider"),v=p.find(".minicolors-opacity-slider"),w=t.find("[class$=-picker]"),x=u.find("[class$=-picker]"),y=v.find("[class$=-picker]"),z=c(w,t),A=c(x,u),B=c(y,v);if(b.is(".minicolors-grid, .minicolors-slider")){switch(r.control){case"wheel":g=t.width()/2-z.x,h=t.height()/2-z.y,j=Math.sqrt(g*g+h*h),k=Math.atan2(h,g),0>k&&(k+=2*Math.PI),j>75&&(j=75,z.x=69-75*Math.cos(k),z.y=69-75*Math.sin(k)),e=n(j/.75,0,100),d=n(180*k/Math.PI,0,360),f=n(100-Math.floor(A.y*(100/u.height())),0,100),m=q({h:d,s:e,b:f}),u.css("backgroundColor",q({h:d,s:e,b:100}));break;case"saturation":d=n(parseInt(z.x*(360/t.width()),10),0,360),e=n(100-Math.floor(A.y*(100/u.height())),0,100),f=n(100-Math.floor(z.y*(100/t.height())),0,100),m=q({h:d,s:e,b:f}),u.css("backgroundColor",q({h:d,s:100,b:f})),p.find(".minicolors-grid-inner").css("opacity",e/100);break;case"brightness":d=n(parseInt(z.x*(360/t.width()),10),0,360),e=n(100-Math.floor(z.y*(100/t.height())),0,100),f=n(100-Math.floor(A.y*(100/u.height())),0,100),m=q({h:d,s:e,b:f}),u.css("backgroundColor",q({h:d,s:e,b:100})),p.find(".minicolors-grid-inner").css("opacity",1-f/100);break;default:d=n(360-parseInt(A.y*(360/u.height()),10),0,360),e=n(Math.floor(z.x*(100/t.width())),0,100),f=n(100-Math.floor(z.y*(100/t.height())),0,100),m=q({h:d,s:e,b:f}),t.css("backgroundColor",q({h:d,s:100,b:100}))}a.val(l(m,r.letterCase))}b.is(".minicolors-opacity-slider")&&(o=r.opacity?parseFloat(1-B.y/v.height()).toFixed(2):1,r.opacity&&a.attr("data-opacity",o)),s.find("SPAN").css({backgroundColor:m,opacity:o}),i(a,m,o)}function h(a,b){var c,d,e,f,g,h,j,k=a.parent(),o=a.data("minicolors-settings"),p=k.find(".minicolors-swatch"),s=k.find(".minicolors-grid"),t=k.find(".minicolors-slider"),u=k.find(".minicolors-opacity-slider"),v=s.find("[class$=-picker]"),w=t.find("[class$=-picker]"),x=u.find("[class$=-picker]");switch(c=l(m(a.val(),!0),o.letterCase),c||(c=l(m(o.defaultValue,!0),o.letterCase)),d=r(c),b||a.val(c),o.opacity&&(e=""===a.attr("data-opacity")?1:n(parseFloat(a.attr("data-opacity")).toFixed(2),0,1),isNaN(e)&&(e=1),a.attr("data-opacity",e),p.find("SPAN").css("opacity",e),g=n(u.height()-u.height()*e,0,u.height()),x.css("top",g+"px")),p.find("SPAN").css("backgroundColor",c),o.control){case"wheel":h=n(Math.ceil(.75*d.s),0,s.height()/2),j=d.h*Math.PI/180,f=n(75-Math.cos(j)*h,0,s.width()),g=n(75-Math.sin(j)*h,0,s.height()),v.css({top:g+"px",left:f+"px"}),g=150-d.b/(100/s.height()),""===c&&(g=0),w.css("top",g+"px"),t.css("backgroundColor",q({h:d.h,s:d.s,b:100}));break;case"saturation":f=n(5*d.h/12,0,150),g=n(s.height()-Math.ceil(d.b/(100/s.height())),0,s.height()),v.css({top:g+"px",left:f+"px"}),g=n(t.height()-d.s*(t.height()/100),0,t.height()),w.css("top",g+"px"),t.css("backgroundColor",q({h:d.h,s:100,b:d.b})),k.find(".minicolors-grid-inner").css("opacity",d.s/100);break;case"brightness":f=n(5*d.h/12,0,150),g=n(s.height()-Math.ceil(d.s/(100/s.height())),0,s.height()),v.css({top:g+"px",left:f+"px"}),g=n(t.height()-d.b*(t.height()/100),0,t.height()),w.css("top",g+"px"),t.css("backgroundColor",q({h:d.h,s:d.s,b:100})),k.find(".minicolors-grid-inner").css("opacity",1-d.b/100);break;default:f=n(Math.ceil(d.s/(100/s.width())),0,s.width()),g=n(s.height()-Math.ceil(d.b/(100/s.height())),0,s.height()),v.css({top:g+"px",left:f+"px"}),g=n(t.height()-d.h/(360/t.height()),0,t.height()),w.css("top",g+"px"),s.css("backgroundColor",q({h:d.h,s:100,b:100}))}a.data("minicolors-initialized")&&i(a,c,e)}function i(a,b,c){var d=a.data("minicolors-settings"),e=a.data("minicolors-lastChange");e&&e.hex===b&&e.opacity===c||(a.data("minicolors-lastChange",{hex:b,opacity:c}),d.change&&(d.changeDelay?(clearTimeout(a.data("minicolors-changeTimeout")),a.data("minicolors-changeTimeout",setTimeout(function(){d.change.call(a.get(0),b,c)},d.changeDelay))):d.change.call(a.get(0),b,c)),a.trigger("change").trigger("input"))}function j(b){var c=m(a(b).val(),!0),d=t(c),e=a(b).attr("data-opacity");return d?(void 0!==e&&a.extend(d,{a:parseFloat(e)}),d):null}function k(b,c){var d=m(a(b).val(),!0),e=t(d),f=a(b).attr("data-opacity");return e?(void 0===f&&(f=1),c?"rgba("+e.r+", "+e.g+", "+e.b+", "+parseFloat(f)+")":"rgb("+e.r+", "+e.g+", "+e.b+")"):null}function l(a,b){return"uppercase"===b?a.toUpperCase():a.toLowerCase()}function m(a,b){return a=a.replace(/[^A-F0-9]/gi,""),3!==a.length&&6!==a.length?"":(3===a.length&&b&&(a=a[0]+a[0]+a[1]+a[1]+a[2]+a[2]),"#"+a)}function n(a,b,c){return b>a&&(a=b),a>c&&(a=c),a}function o(a){var b={},c=Math.round(a.h),d=Math.round(255*a.s/100),e=Math.round(255*a.b/100);if(0===d)b.r=b.g=b.b=e;else{var f=e,g=(255-d)*e/255,h=(f-g)*(c%60)/60;360===c&&(c=0),60>c?(b.r=f,b.b=g,b.g=g+h):120>c?(b.g=f,b.b=g,b.r=f-h):180>c?(b.g=f,b.r=g,b.b=g+h):240>c?(b.b=f,b.r=g,b.g=f-h):300>c?(b.b=f,b.g=g,b.r=g+h):360>c?(b.r=f,b.g=g,b.b=f-h):(b.r=0,b.g=0,b.b=0)}return{r:Math.round(b.r),g:Math.round(b.g),b:Math.round(b.b)}}function p(b){var c=[b.r.toString(16),b.g.toString(16),b.b.toString(16)];return a.each(c,function(a,b){1===b.length&&(c[a]="0"+b)}),"#"+c.join("")}function q(a){return p(o(a))}function r(a){var b=s(t(a));return 0===b.s&&(b.h=360),b}function s(a){var b={h:0,s:0,b:0},c=Math.min(a.r,a.g,a.b),d=Math.max(a.r,a.g,a.b),e=d-c;return b.b=d,b.s=0!==d?255*e/d:0,b.h=0!==b.s?a.r===d?(a.g-a.b)/e:a.g===d?2+(a.b-a.r)/e:4+(a.r-a.g)/e:-1,b.h*=60,b.h<0&&(b.h+=360),b.s*=100/255,b.b*=100/255,b}function t(a){return a=parseInt(a.indexOf("#")>-1?a.substring(1):a,16),{r:a>>16,g:(65280&a)>>8,b:255&a}}a.minicolors={defaults:{animationSpeed:50,animationEasing:"swing",change:null,changeDelay:0,control:"hue",defaultValue:"",hide:null,hideSpeed:100,inline:!1,letterCase:"lowercase",opacity:!1,position:"bottom left",show:null,showSpeed:100,theme:"default"}},a.extend(a.fn,{minicolors:function(f,g){switch(f){case"destroy":return a(this).each(function(){c(a(this))}),a(this);case"hide":return e(),a(this);case"opacity":return void 0===g?a(this).attr("data-opacity"):(a(this).each(function(){h(a(this).attr("data-opacity",g))}),a(this));case"rgbObject":return j(a(this),"rgbaObject"===f);case"rgbString":case"rgbaString":return k(a(this),"rgbaString"===f);case"settings":return void 0===g?a(this).data("minicolors-settings"):(a(this).each(function(){var b=a(this).data("minicolors-settings")||{};c(a(this)),a(this).minicolors(a.extend(!0,b,g))}),a(this));case"show":return d(a(this).eq(0)),a(this);case"value":return void 0===g?a(this).val():(a(this).each(function(){h(a(this).val(g))}),a(this));default:return"create"!==f&&(g=f),a(this).each(function(){b(a(this),g)}),a(this)}}}),a(document).on("mousedown.minicolors touchstart.minicolors",function(b){a(b.target).parents().add(b.target).hasClass("minicolors")||e()}).on("mousedown.minicolors touchstart.minicolors",".minicolors-grid, .minicolors-slider, .minicolors-opacity-slider",function(b){var c=a(this);b.preventDefault(),a(document).data("minicolors-target",c),f(c,b,!0)}).on("mousemove.minicolors touchmove.minicolors",function(b){var c=a(document).data("minicolors-target");c&&f(c,b)}).on("mouseup.minicolors touchend.minicolors",function(){a(this).removeData("minicolors-target")}).on("mousedown.minicolors touchstart.minicolors",".minicolors-swatch",function(b){var c=a(this).parent().find(".minicolors-input");b.preventDefault(),d(c)}).on("focus.minicolors",".minicolors-input",function(){var b=a(this);b.data("minicolors-initialized")&&d(b)}).on("blur.minicolors",".minicolors-input",function(){var b=a(this),c=b.data("minicolors-settings");b.data("minicolors-initialized")&&(b.val(m(b.val(),!0)),""===b.val()&&b.val(m(c.defaultValue,!0)),b.val(l(b.val(),c.letterCase)))}).on("keydown.minicolors",".minicolors-input",function(b){var c=a(this);if(c.data("minicolors-initialized"))switch(b.keyCode){case 9:e();break;case 13:case 27:e(),c.blur()}}).on("keyup.minicolors",".minicolors-input",function(){var b=a(this);b.data("minicolors-initialized")&&h(b,!0)}).on("paste.minicolors",".minicolors-input",function(){var b=a(this);b.data("minicolors-initialized")&&setTimeout(function(){h(b,!0)},1)})}(jQuery);

var ciw = {
    customFileFrame: null,
    uploadParams : null,
    texts : {
        errorWrongType : "Wrong type, allowed: ",
        errorWrongSize : "Wrong size, max allowed: ",
        errorOther : "Other error: ",
        noErrors : "No errors",
        confirmRemove : 'Are you sure you want to delete this file ',
        validationError : "Validation error",
        popupTitle : "Choose file for preview",
        popupButton : "Choose",
        addWatermark : "Add Watermark"
    },
    massive_path : null,
    urlwm : null,
    includeUrl : null,
    urlfonts : null,
    addUploader: function(id, action, allowedTypes, type) {
        var display = jQuery("#" + id + '-fileupload-display');
        jQuery("#" + id + '-fileupload').liteUploader(
                {
                    script: ajaxurl,
                    rules: {
                        allowedFileTypes: allowedTypes,
                        maxSize: 2500000
                    },
                    params: {
                        'action': action,
                        'type': 'upload',
                    }
                })
                .on('lu:errors', function(e, errors) {
                    var isErrors = false;
                    display.html('');
                    jQuery.each(errors, function(i, error) {
                        if (error.errors.length > 0) {
                            isErrors = true;
                            var err = "";
                            jQuery.each(error.errors, function(i, errorInfo) {
                                if (errorInfo.type == 'type') {
                                    err = ciw.texts.errorWrongType + allowedTypes;
                                } else if (errorInfo.type == 'size') {
                                    err = ciw.texts.errorWrongSize+2500000;
                                } else {
                                    err = ciw.texts.errorOther + JSON.stringify(errorInfo);
                                }
                                display.append('<br />' + err);
                            });
                        }
                    });

                    if (!isErrors) {
                        display.append('<br />'+ciw.texts.noErrors);
                    }
                })
                .on('lu:success', function(e, response) {
                    response = jQuery.parseJSON(response);
                    var el = jQuery("#" + id);
                    if (response.files.length > 0) {
                        jQuery.each(response.files, function(index, value) {
                            el.append("<option value='" + value.fullName + "'>" + value.name + "</option>");
                        });
                        ciw.iwManagePopulate(type);
                    }
                    if (response.errors.length > 0) {
                        alert(response.errors);
                    }
                });
    },
    iwRemove: function(file, action, div, type) {
        if (confirm(ciw.texts.confirmRemove + file + "?")) {
            jQuery.post(ajaxurl, {'action': action, 'delete': file, 'type': 'delete'}, function(data) {
                if (data.status == 'ok') {
                    jQuery('#' + div).children().each(function(index, element) {
                        el = jQuery(element);
                        if (el.attr('value') == file) {
                            el.remove();
                        }
                    });
                    ciw.iwManagePopulate(type);
                    ciw.imageUrlShow("#"+ciw.uploadParams[type].id);
                } else {
                    alert(data.message);
                }
            }, "json");
        }
    },
    iwImageManagePreviewCreate: function(el) {
        el = jQuery(el);
        src = el.attr('src');
        jQuery.post(ajaxurl,{ 'src': src, 'action': 'ciw_ajax_ipm' },function(response) {
            r = jQuery.parseJSON(response);
            if (r.status == 'ok') {
                el.attr('src', src + "?time=" + new Date());
            } else {

            }
        }
        );
    },
    iwRefresh: function() {
        jQuery('#preview-image').html("");
        jQuery.post(ajaxurl, 'action=ciw_ajax_generate_image&' + jQuery('#iw_form').serialize(), function(data) {
            jQuery('.error').remove();
            jQuery('#preview_image').html("");
            var html = "";
            if (data.status == 'ok') {
                jQuery.each(data.data, function(index, value) {
                    html += "<div class='iw_preview'><a href='" + value + "' target='_blank' ><img class='iw_preview' src='" + value + "' /><br>"+index+"</a></div>";
                });
                jQuery('#preview-image').html(html);
            } else {
                jQuery('#preview-image').html("<div>"+ciw.texts.validationError+"</div>");
                ciw.putValidationErrors(data.data);
            }
        }, "json");
    },
    putValidationErrors: function(vErrors) {
        jQuery.each(vErrors, function(index, value) {
            jQuery('#' + index).after("<span class='error'>" + value + "</span>");
        });
    },
    iwManage: function(type) {
        divId = ciw.uploadParams[type].id + '-manage';
        var div = jQuery('#' + divId);
        jQuery.post(ajaxurl, {'action': ciw.uploadParams[type].action}, function(data) {
            jQuery("#" + divId + '-fields').html(data);
            div.dialog({
                'dialogClass': 'wp-dialog',
                'modal': true,
                'autoOpen': true,
                'closeOnEscape': true,
                'height': 500,
                'width': 700,
                'buttons': {
                    "Close": function() {
                        jQuery(this).dialog('close');
                    }
                }
            });
        });
    },
    iwManagePopulate: function(type) {
        divId = ciw.uploadParams[type].id + '-manage';
        jQuery.post(ajaxurl, {'action': ciw.uploadParams[type].action}, function(data) {
            jQuery("#" + divId + '-fields').html(data);
        });
    },
    imageUrlShow: function(el) {
        var val = jQuery(el).val();
        var id = jQuery(el).attr('id') + "-preview";
        if (jQuery('#' + id).length > 0) {
            var div = jQuery('#' + id);
        } else {
            var div = jQuery("<span id='" + id + "'></span>");
            div.insertAfter(jQuery('#' + jQuery(el).attr('id')));
        }
        var html = "";
        if (val.length > 0) {
            if (jQuery(el).attr('id').match(/font/g)) {
                val = val.replace("ttf", "jpg");
                html = "<img style='width:200px' src='" + this.urlfonts + val + "' />";
            } else {
                html = "<a href='" + this.urlwm + val + "' target='_blank'><img style='width:200px' src='" + this.urlwm + val + "' /></a>";
            }
        }
        div.html(html);
    },
    previewImageChoose: function() {
        //If the frame already exists, reopen it
        if (typeof (ciw.customFileFrame) !== "undefined" && null != ciw.customFileFrame ) {
            ciw.customFileFrame.close();
        }

        //Create WP media frame.
        ciw.customFileFrame = wp.media.frames.customHeader = wp.media({
            //Title of media manager frame
            title: ciw.texts.popupTitle,
            library: {
                type: 'image'
            },
            button: {
                //Button text
                text: ciw.texts.popupButton
            },
            //Do not allow multiple files, if you want multiple, set true
            multiple: false
        });

        //callback for selected image
        ciw.customFileFrame.on('select', function() {
            var attachment = ciw.customFileFrame.state().get('selection').first().toJSON();
            jQuery("#preview").val(attachment.id);
        });

        //Open modal
        ciw.customFileFrame.open();
    },
    restoreOriginalFile: function(imageId){
        jQuery.post( ajaxurl, { 'action': 'ciw_ajax_ri','id': imageId, }, function( data ) {
            if(data.status == 'ok'){
                window.location.href=window.location.href;
            }else{
                alert(data.data);
            }
        }, "json");        
    },
    watermarkOneImage: function(imageId){
        jQuery.post( ajaxurl, { 'action': 'ciw_ajax_woi','id': imageId, }, function( data ) {
            if(data.status == 'ok'){
                window.location.href=window.location.href;
            }else{
                alert(data.data);
            }
        }, "json");
    },
    massive : function(path){
        jQuery.post( ajaxurl, { 'action': 'ciw_ajax_mass','path': path }, function( data ) {
            if(data.status=='ok'){
                data = data.data;
                ciw.massive_path = data.path;
                showdir = data.path.split("/");
                jQuery('#mass_actual_path').html("");
                var pathExploder = "";
                jQuery.each(showdir, function( index, value ) {
                    pathExploder += value+"/";
                    tmp = '<a href="javascript:ciw.massive(\''+pathExploder+'\')">'+value+'</a>/';
                    jQuery('#mass_actual_path').append(tmp);
                });
                jQuery('#mass_dir').html("<table class='mass_dir'/>");
                var table = jQuery('#mass_dir table');
                jQuery.each(data.contents, function( index, value ) {
                    if(value.is_dir){
                        tmp = '<tr><td class="mass_dir_td_left"><a href="javascript:ciw.massive(\''+data.path+'/'+value.name+'\')"><img class="mass_dir" src="'+ciw.includeUrl+'folder.png" />'+value.name+'</a></td></tr>';
                    }else{
                        tmp = '<tr><td class="mass_dir_td_left"><a class="image" target="_blank" href="'+value.url+'">'
                                +'<img class="mass_dir" src="'+ciw.includeUrl+'pics.png" />'+value.name+'</a></td>'
                                +'<td class="mass_dir_td_middle"></td><td class="mass_dir_td_right"><input type="checkbox" name="wm_img" value="'+value.name+'" ></td></tr>';
                    }
                    table.append(tmp);
                });
                jQuery('#mass_dir').append('<a href="javascript:ciw.massiveAddWatermark()"><img src="'+ciw.includeUrl+'increase.png" />'+ciw.texts.addWatermark+'</a>');
                jQuery( '#mass_dir' ).tooltip({     
                    items: "a.image",
                    content: function() {
                        return "<img style='max-width:200px;max-height:200px;' src='"+jQuery( this ).context.href+"'>";
                    }
                });
            }else{
                alert(data.data);
            }
        }, "json");
    },
    massiveAddWatermark : function(){
        jQuery( 'img.error').remove();
        jQuery("input[type='checkbox'][name='wm_img']").filter(":checked").each(function( index ) {
            element = jQuery( this );
            elementIcons = element.parent().prev();
            elementIcons.html("<img src='"+ciw.includeUrl+"settings.png' title=''>");
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                async: false,
                data: { 'action': 'ciw_ajax_mass','path': ciw.massive_path,file: element.context.value },
                success: function( data ) {
                if( 'ok' == data.status ){
                    element.attr('checked', false);
                    elementIcons.html("<img class='error' src='"+ciw.includeUrl+"confirm.png' title=''>");
                }else{
                    elementIcons.html("<img class='error' src='"+ciw.includeUrl+"error.png' title='"+data.data+"'>");
                }
            },
                dataType: "json"
            });
            jQuery( 'img.error').tooltip();
        });
    },
    alert : function(type,message){
        var popup = jQuery('<div class="ciw_popup">'+message+'</div>');
        var parent = jQuery('#tabswm');
        var pos = parent.offset();
        popup.css("top",pos.top+40+"px").css("left",pos.left+parent.width()-200+"px");
        jQuery('#tabswm').append(popup);
        popup.fadeOut(2000,function(){
            popup.remove();
        });
    }
};