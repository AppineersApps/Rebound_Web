(function(f){var h=function(a,b){this.$element=f(a);this.$element.is("input")?(this.options=f.extend({},f.fn.combodate.defaults,b,this.$element.data()),this.init()):f.error("Combodate should be applied to INPUT element")};h.prototype={constructor:h,init:function(){this.map={day:["D","date"],month:["M","month"],year:["Y","year"],hour:["[Hh]","hours"],minute:["m","minutes"],second:["s","seconds"],ampm:["[Aa]",""]};this.$widget=f('<span class="combodate"></span>').html(this.getTemplate());this.initCombos();
this.datetime=null;this.$widget.on("change","select",f.proxy(function(a){this.$element.val(this.getValue()).change();this.options.smartDays&&(f(a.target).is(".month")||f(a.target).is(".year"))&&(this.fillCombo("day"),f(this.$day).trigger("chosen:updated"))},this));this.$widget.find("select").css("width","auto");this.$element.hide().after(this.$widget);this.setValue(this.$element.val()||this.options.value)},getTemplate:function(){var a=this.options.template,b=this.$element.prop("disabled"),d=this.options.customClass,
e=this.options.commonClass,c=this.options.eachClass;f.each(this.map,function(b,c){c=c[0];var d=new RegExp(c+"+"),e=1<c.length?c.substring(1,2):c;a=a.replace(d,"{"+e+"}")});a=a.replace(/ /g,"&nbsp;");f.each(this.map,function(g,f){f=f[0];var k=c[g]?c[g]:"",h=1<f.length?f.substring(1,2):f;a=a.replace("{"+h+"}",'<select class="'+g+" "+d+" "+e+" "+k+'"'+(b?' disabled="disabled"':"")+"></select>")});return a},initCombos:function(){for(var a in this.map){var b=this.$widget.find("."+a);this["$"+a]=b.length?
b:null;this.fillCombo(a)}},fillCombo:function(a){var b=this["$"+a];if(b){a=this["fill"+a.charAt(0).toUpperCase()+a.slice(1)]();var d=b.val();b.empty();for(var e=0;e<a.length;e++)b.append('<option value="'+a[e][0]+'">'+a[e][1]+"</option>");b.val(d);b.trigger("chosen:updated")}},fillCommon:function(a){var b=[];if("name"===this.options.firstItem){var d=moment.localeData?moment.localeData()._relativeTime:moment.relativeTime||moment.langData()._relativeTime;a="function"===typeof d[a]?d[a](1,!0,a,!1):d[a];
a=a.split(" ").reverse()[0];b.push(["",a])}else"empty"===this.options.firstItem&&b.push(["",""]);return b},fillDay:function(){var a=this.fillCommon("d"),b=-1!==this.options.template.indexOf("DD"),d=31;if(this.options.smartDays&&this.$month&&this.$year){var e=parseInt(this.$month.val(),10);var c=parseInt(this.$year.val(),10);isNaN(e)||isNaN(c)||(d=moment([c,e]).daysInMonth())}for(c=1;c<=d;c++)e=b?this.leadZero(c):c,a.push([c,e]);return a},fillMonth:function(){var a=this.fillCommon("M"),b,d=-1!==this.options.template.indexOf("MMMMMM"),
e=-1!==this.options.template.indexOf("MMMMM"),c=-1!==this.options.template.indexOf("MMMM"),g=-1!==this.options.template.indexOf("MMM"),f=-1!==this.options.template.indexOf("MM");for(b=0;11>=b;b++){var l=d?moment().date(1).month(b).format("MM - MMMM"):e?moment().date(1).month(b).format("MM - MMM"):c?moment().date(1).month(b).format("MMMM"):g?moment().date(1).month(b).format("MMM"):f?this.leadZero(b+1):b+1;a.push([b,l])}return a},fillYear:function(){var a=[],b,d=-1!==this.options.template.indexOf("YYYY");
for(b=this.options.maxYear;b>=this.options.minYear;b--){var e=d?b:(b+"").substring(2);a[this.options.yearDescending?"push":"unshift"]([b,e])}return a=this.fillCommon("y").concat(a)},fillHour:function(){var a=this.fillCommon("h"),b;var d=-1!==this.options.template.indexOf("h");this.options.template.indexOf("H");var e=-1!==this.options.template.toLowerCase().indexOf("hh"),c=d?12:23;for(b=d?1:0;b<=c;b++)d=e?this.leadZero(b):b,a.push([b,d]);return a},fillMinute:function(){var a=this.fillCommon("m"),b,
d=-1!==this.options.template.indexOf("mm");for(b=0;59>=b;b+=this.options.minuteStep){var e=d?this.leadZero(b):b;a.push([b,e])}return a},fillSecond:function(){var a=this.fillCommon("s"),b,d=-1!==this.options.template.indexOf("ss");for(b=0;59>=b;b+=this.options.secondStep){var e=d?this.leadZero(b):b;a.push([b,e])}return a},fillAmpm:function(){var a=-1!==this.options.template.indexOf("a");this.options.template.indexOf("A");return[["am",a?"am":"AM"],["pm",a?"pm":"PM"]]},getValue:function(a){var b={},
d=this,e=!1;f.each(this.map,function(a,c){if("ampm"!==a){if(d["$"+a])b[a]=parseInt(d["$"+a].val(),10);else{var f=d.datetime?d.datetime[c[1]]():"day"===a?1:0;b[a]=f}if(isNaN(b[a]))return e=!0,!1}});if(e)return"";this.$ampm&&(b.hour=12===b.hour?"am"===this.$ampm.val()?0:12:"am"===this.$ampm.val()?b.hour:b.hour+12);var c=moment([b.year,b.month,b.day,b.hour,b.minute,b.second]);this.highlight(c);a=void 0===a?this.options.format:a;return null===a?c.isValid()?c:null:c.isValid()?c.format(a):""},setValue:function(a){function b(a,
b){var c={};a.children("option").each(function(a,d){var e=f(d).attr("value");if(""!==e){var g=Math.abs(e-b);if("undefined"===typeof c.distance||g<c.distance)c={value:e,distance:g}}});return c.value}if(a){var d="string"===typeof a?moment(a,this.options.format,!0):moment(a),e=this,c={};d.isValid()?(f.each(this.map,function(a,b){"ampm"!==a&&(c[a]=d[b[1]]())}),this.$ampm&&(12<=c.hour?(c.ampm="pm",12<c.hour&&(c.hour-=12)):(c.ampm="am",0===c.hour&&(c.hour=12))),f.each(c,function(a,c){e["$"+a]&&("minute"===
a&&1<e.options.minuteStep&&e.options.roundTime&&(c=b(e["$"+a],c)),"second"===a&&1<e.options.secondStep&&e.options.roundTime&&(c=b(e["$"+a],c)),e["$"+a].val(c))}),this.options.smartDays&&(this.fillCombo("day"),f(this.$day).trigger("chosen:updated")),this.$element.val(d.format(this.options.format)).change(),this.datetime=d):this.datetime=null}},highlight:function(a){a.isValid()?this.options.errorClass?this.$widget.removeClass(this.options.errorClass):this.$widget.find("select").css("border-color",this.borderColor):
this.options.errorClass?this.$widget.addClass(this.options.errorClass):(this.borderColor||(this.borderColor=this.$widget.find("select").css("border-color")),this.$widget.find("select").css("border-color","red"))},leadZero:function(a){return 9>=a?"0"+a:a},destroy:function(){this.$widget.remove();this.$element.removeData("combodate").show()}};f.fn.combodate=function(a){var b,d=Array.apply(null,arguments);d.shift();return"getValue"===a&&this.length&&(b=this.eq(0).data("combodate"))?b.getValue.apply(b,
d):this.each(function(){var b=f(this),c=b.data("combodate"),g="object"==typeof a&&a;c||b.data("combodate",c=new h(this,g));"string"==typeof a&&"function"==typeof c[a]&&c[a].apply(c,d)})};f.fn.combodate.defaults={format:"DD-MM-YYYY HH:mm",template:"D / MMM / YYYY   H : mm",value:null,minYear:1970,maxYear:2015,yearDescending:!0,minuteStep:5,secondStep:1,firstItem:"empty",errorClass:null,customClass:"",commonClass:"",eachClass:"",roundTime:!0,smartDays:!1}})(window.jQuery);